<?php

namespace App\Http\Controllers;

use App\Models\BiometricAttendance;
use App\Models\BiometricUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BiometricAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:الرواتب');
    }
    public function index(Request $request)
    {
        $query = BiometricAttendance::with('biometricUser');

        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('date', $request->month)
                  ->whereYear('date', $request->year);
        } else {
             // Default to current month
            $query->whereMonth('date', Carbon::now()->month)
                  ->whereYear('date', Carbon::now()->year);
        }

        if ($request->has('biometric_user_id') && $request->biometric_user_id) {
            $query->where('biometric_user_id', $request->biometric_user_id);
        }

        $attendances = $query->orderBy('date', 'desc')->get();
        $biometricUsers = BiometricUser::all();

        // Calculate Payroll Summary using Aggregation for robustness
        // 1. Build Query for Stats
        $statsQuery = BiometricAttendance::query();
        if ($request->has('month') && $request->has('year')) {
            $statsQuery->whereMonth('date', $request->month)
                       ->whereYear('date', $request->year);
        } else {
             $statsQuery->whereMonth('date', Carbon::now()->month)
                        ->whereYear('date', Carbon::now()->year);
        }
        
        // If specific user filtered, we can restrict stats or show all?
        // User asked "Calculate for everyone automatically". 
        // So we REMOVE the filter here to ensure stats are aggregated for ALL users, 
        // allowing the Payroll Table to show everyone's data even if the Attendance List is filtered.
        /* 
        if ($request->has('biometric_user_id') && $request->biometric_user_id) {
            $statsQuery->where('biometric_user_id', $request->biometric_user_id);
        }
        */

        $aggregates = $statsQuery->selectRaw('
            biometric_user_id, 
            SUM(delay_minutes) as total_delay, 
            SUM(overtime_minutes) as total_overtime,
            SUM(absence_deduction) as total_absence,
            SUM(delay_deduction) as total_delay_deduction, 
            SUM(overtime_pay) as total_overtime_pay_db,
            SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as total_absence_days,
            SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as total_attendance_days
        ')
        ->groupBy('biometric_user_id')
        ->get()
        ->keyBy('biometric_user_id');

        $payrollData = [];
        foreach ($biometricUsers as $u) {
            // Get stats from Aggregate or 0
            $stats = $aggregates->get($u->id);
            
            $sumDelayMinutes = $stats ? $stats->total_delay : 0;
            $sumOvertimeMinutes = $stats ? $stats->total_overtime : 0;
            $totalAbsenceDeduction = $stats ? $stats->total_absence : 0;

            // --- Month Level Netting ---
            $balance = $sumOvertimeMinutes - $sumDelayMinutes;
            
            $finalDelayMinutes = 0;
            $finalOvertimeMinutes = 0;
            
            if ($balance > 0) {
                // Net Surplus (Overtime)
                $finalOvertimeMinutes = $balance;
                $finalDelayMinutes = 0;
            } else {
                // Net Deficit (Delay)
                $finalOvertimeMinutes = 0;
                $finalDelayMinutes = abs($balance);
            }
            
            // Calculate Financials based on FINAL NET values
            $workingHours = 9; // Default
            if ($u->shift_start && $u->shift_end) {
                 $start = Carbon::parse($u->shift_start);
                 $end = Carbon::parse($u->shift_end);
                 $workingHours = $start->diffInMinutes($end) / 60;
            }
            
            $minuteRate = 0;
            if ($u->base_salary > 0 && $workingHours > 0) {
                 $minuteRate = ($u->base_salary / 30 / $workingHours / 60);
            }

            $totalDelayDeduction = $finalDelayMinutes * $minuteRate; // 1.0x
            $totalOvertimePay = $finalOvertimeMinutes * $minuteRate * ($u->overtime_rate ?? 1.5); // 1.5x
            
            $netSalary = $u->base_salary - $totalDelayDeduction - $totalAbsenceDeduction + $totalOvertimePay;
            
            $payrollData[$u->id] = [
                'user' => $u,
                'total_delay_minutes' => $sumDelayMinutes . ' -> ' . $finalDelayMinutes, 
                'total_overtime_minutes' => $sumOvertimeMinutes . ' -> ' . $finalOvertimeMinutes,
                'total_deductions' => $totalDelayDeduction + $totalAbsenceDeduction,
                'total_overtime_pay' => $totalOvertimePay,
                'total_attendance_days' => $stats ? $stats->total_attendance_days : 0,
                'total_absence_days' => $stats ? $stats->total_absence_days : 0,
                'net_salary' => $netSalary
            ];
        }

        return view('biometric.index', compact('attendances', 'biometricUsers', 'payrollData'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'attendance_file' => 'required|file',
        ]);

        $file = $request->file('attendance_file');
        $content = file_get_contents($file->getRealPath());
        $lines = explode("\n", $content);

        // Group data by Biometric ID (integer) and Date
        $parsedData = [];

        foreach ($lines as $line) {
            $parts = preg_split('/\s+/', trim($line));
            
            // Expected format: ID Date Time UserID ...
            // Example: 10 2025-06-01 09:17:46 101 0 1 0
            
            if (count($parts) >= 3) {
                // Determine User ID (index 0 based on user feedback and file inspection)
                // File format: ID Date Time MachineID ...
                $biometricId = $parts[0] ?? null; 
                $dateStr = $parts[1] ?? null;
                $timeStr = $parts[2] ?? null;

                if ($biometricId && $dateStr && $timeStr) {
                    try {
                        $timestamp = Carbon::parse("$dateStr $timeStr");
                        $date = $timestamp->format('Y-m-d');

                        if (!isset($parsedData[$biometricId][$date])) {
                            $parsedData[$biometricId][$date] = [];
                        }
                        $parsedData[$biometricId][$date][] = $timestamp;
                    } catch (\Exception $e) {
                         // Skip invalid date lines
                         continue;
                    }
                }
            }
        }

        DB::beginTransaction();
        try {
            foreach ($parsedData as $biometricId => $dates) {
                // Find or Create Biometric User
                $biometricUser = BiometricUser::firstOrCreate(
                    ['biometric_id' => $biometricId],
                    ['name' => 'Employee #' . $biometricId] // Default name
                );

                foreach ($dates as $date => $punches) {
                    // Use usort to sort by timestamp ASC safely
                    usort($punches, function ($a, $b) {
                        return $a->timestamp <=> $b->timestamp;
                    });
                    
                    // DEBUG: Log punches before dedupe
                    $debugMsg = "User {$biometricUser->id} Date {$date} Raw: " . implode(', ', array_map(fn($p)=>$p->format('H:i'), $punches));
                    
                    // Deduplicate Punches
                    $uniquePunches = [];
                    if (count($punches) > 0) {
                        $uniquePunches[] = $punches[0];
                        for ($i = 1; $i < count($punches); $i++) {
                            $lastUnique = $uniquePunches[count($uniquePunches) - 1];
                            $diff = abs($punches[$i]->diffInMinutes($lastUnique));
                            
                            $debugMsg .= " | Diff({$punches[$i]->format('H:i')} - {$lastUnique->format('H:i')}) = {$diff}";
                            
                            if ($diff > 30) {
                                $uniquePunches[] = $punches[$i];
                            }
                        }
                    }
                    $punches = $uniquePunches;
                    
                    $debugMsg .= " | Final: " . implode(', ', array_map(fn($p)=>$p->format('H:i'), $punches));
                    \Illuminate\Support\Facades\Log::info($debugMsg);

                    $firstPunch = $punches[0];
                    $lastPunch = end($punches);

                    $carbonDate = Carbon::parse($date);
                    $dayOfWeek = $carbonDate->dayOfWeek;
                    $isFriday = ($dayOfWeek == Carbon::FRIDAY);
                    
                    // Defaults
                    $checkIn = $firstPunch;
                    $checkOut = $lastPunch;
                    $status = 'present';
                    $delayMinutes = 0;
                    $overtimeMinutes = 0;
                    $missingPunchType = null;
                    $delayDeduction = 0;
                    $overtimePay = 0;

                    // Shift logic from BiometricUser
                    $shiftStart = $biometricUser->shift_start ? Carbon::parse($date . ' ' . $biometricUser->shift_start) : null;
                    $shiftEnd   = $biometricUser->shift_end ? Carbon::parse($date . ' ' . $biometricUser->shift_end) : null;

                    if ($isFriday) {
                        $status = 'weekend'; // Or holiday
                    } elseif ($shiftStart && $shiftEnd) {
                         $punchesCount = count($punches);
                         // Case: Single Punch (Forgot In or Out)
                        if ($punchesCount == 1) {
                             $diffStart = abs($firstPunch->diffInMinutes($shiftStart, false));
                             $diffEnd = abs($firstPunch->diffInMinutes($shiftEnd, false));
                             
                             if ($diffStart < $diffEnd) {
                                 // Closer to Start -> This is Check-In. Check-Out is MISSING.
                                 // Change: Leave Check-Out as NULL (or handle specifically) if no info.
                                 // Logic change request: "Attendance only".
                                 $checkOut = null; 
                                 $missingPunchType = 'check_out';
                             } else {
                                 // Closer to End -> This is Check-Out. Check-In is MISSING.
                                 $checkIn = null; 
                                 $missingPunchType = 'check_in';
                             }
                         }

                        // Calculate Attributes using shared logic
                        $attrs = $this->calculateAttendanceAttributes($checkIn, $checkOut, $shiftStart, $shiftEnd, $biometricUser, $isFriday);
                        
                        $status = $attrs['status'];
                        $delayMinutes = $attrs['delay_minutes'];
                        $delayDeduction = $attrs['delay_deduction'];
                        $overtimeMinutes = $attrs['overtime_minutes'];
                        $overtimePay = $attrs['overtime_pay'];
                    }

                    // Update or Create Record
                    BiometricAttendance::updateOrCreate(
                        [
                            'biometric_user_id' => $biometricUser->id,
                            'date' => $date,
                        ],
                        [
                            'shift_start' => $biometricUser->shift_start,
                            'shift_end' => $biometricUser->shift_end,
                            'check_in' => $checkIn,
                            'check_out' => $checkOut,
                            'status' => $status,
                            'delay_minutes' => $delayMinutes,
                            'overtime_minutes' => $overtimeMinutes,
                            'overtime_pay' => round($overtimePay, 2),
                            'is_friday' => $isFriday,
                            'missing_punch' => $missingPunchType,
                        ]
                    );
                }
            }
            DB::commit();
            return redirect()->route('biometric.index')->with('success', 'File processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }

    public function updateUser(Request $request, $id)
    {
        $user = BiometricUser::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'shift_start' => 'nullable',
            'shift_end' => 'nullable',
            'base_salary' => 'nullable|numeric',
            'overtime_rate' => 'nullable|numeric',
        ]);

        $user->update([
            'name' => $request->name,
            'shift_start' => $request->shift_start,
            'shift_end' => $request->shift_end,
            'base_salary' => $request->base_salary,
            'overtime_rate' => $request->overtime_rate,
        ]);

        // Recalculate attendance for this user to reflect new shift/salary settings
        $this->recalculateAttendance($user);

        return back()->with('success', 'User updated and attendance recalculated successfully.');
    }

    private function recalculateAttendance(BiometricUser $user)
    {
        // Get all attendance records for this user
        $attendances = BiometricAttendance::where('biometric_user_id', $user->id)->get();

        foreach ($attendances as $attendance) {
            // We need to re-evaluate delay and overtime based on new shift times
            // and re-calculate costs based on new salary.

            // 1. Setup Dates
            // We use the ATTENDANCE DATE as the anchor for all time comparisons to avoid Date Mismatches.
            $dateString = $attendance->date->format('Y-m-d');
            
            // 2. Parse Shift Times (if set)
            $shiftStart = $user->shift_start ? Carbon::parse("$dateString {$user->shift_start}") : null;
            $shiftEnd   = $user->shift_end ? Carbon::parse("$dateString {$user->shift_end}") : null;
            
            // 3. Parse Actual Punches
            // Note: $attendance->check_in is a Carbon object (via casts), but its date might be 'today' or '1970'.
            // We must force the date to match the attendance date for valid diffing.
            $checkIn = null;
            if ($attendance->check_in) {
                 $checkIn = Carbon::parse("$dateString " . $attendance->check_in->format('H:i:s'));
            }

            $checkOut = null;
            if ($attendance->check_out) {
                 $checkOut = Carbon::parse("$dateString " . $attendance->check_out->format('H:i:s'));
            }

            $delayMinutes = 0;
            $overtimeMinutes = 0;
            $overtimePay = 0;
            $delayDeduction = 0;

            // Only calculate if we have valid shift config and Friday is not a holiday (assuming)
            // If the user manually set status to 'absent' or 'leave', we might verify that logic later.
            // For now, if shift is defined, we calculate.
            
            // Calculate Attributes using shared logic
            $attrs = $this->calculateAttendanceAttributes($checkIn, $checkOut, $shiftStart, $shiftEnd, $user, $attendance->is_friday);

            $delayMinutes = $attrs['delay_minutes'];
            $delayDeduction = $attrs['delay_deduction'];
            $overtimeMinutes = $attrs['overtime_minutes'];
            $overtimePay = $attrs['overtime_pay'];

            // Update the record
            $attendance->update([
                'shift_start' => $user->shift_start,
                'shift_end' => $user->shift_end,
                'delay_minutes' => $delayMinutes,
                'delay_deduction' => round($delayDeduction, 2),
                'overtime_minutes' => $overtimeMinutes,
                'overtime_pay' => round($overtimePay, 2),
            ]);
        }
    }

    public function generateAbsences(Request $request) 
    {
        // This can be called manually or effectively run when viewing a report for a specific month
        // For now, let's expose a route or call it implicitly.
        // Let's make a route for "Recalculate/Sync" which does this.
        
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        
        $users = BiometricUser::all();
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        foreach ($users as $user) {
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::create($year, $month, $day);
                
                // Check if record exists
                $exists = BiometricAttendance::where('biometric_user_id', $user->id)
                            ->whereDate('date', $date->format('Y-m-d'))
                            ->exists();

                if (!$exists) {
                    // Create Absence Record
                    $isFriday = ($date->dayOfWeek == Carbon::FRIDAY);
                    $status = $isFriday ? 'weekend' : 'absent';
                    $absenceDeduction = 0;

                    if ($status == 'absent' && $user->base_salary > 0) {
                        // 1 day deduction?
                        $dailyRate = $user->base_salary / 30;
                        $absenceDeduction = $dailyRate;
                    }

                    BiometricAttendance::create([
                        'biometric_user_id' => $user->id,
                        'date' => $date->format('Y-m-d'),
                        'status' => $status,
                        'absence_deduction' => round($absenceDeduction, 2),
                        'is_friday' => $isFriday,
                        'shift_start' => $user->shift_start,
                        'shift_end' => $user->shift_end,
                    ]);
                }
            }
        }
        
        return back()->with('success', 'Missing days generated successfully.');
    }

    public function destroyAll()
    {
        // Truncate the table - Deletes ALL records
        BiometricAttendance::truncate();
        
        return redirect()->route('biometric.index')->with('success', 'تم مسح جميع سجلات الحضور بنجاح.');
    }

    private function calculateAttendanceAttributes($checkIn, $checkOut, $shiftStart, $shiftEnd, $user, $isFriday)
    {
        $delayMinutes = 0;
        $overtimeMinutes = 0;
        $delayDeduction = 0;
        $overtimePay = 0;
        
        $status = 'present'; 
        if ($isFriday) {
            $status = 'weekend';
        }

        if (!$isFriday && $shiftStart && $shiftEnd) {
            // Dynamic Working Hours (in Minutes)
            $shiftDurationMinutes = $shiftStart->diffInMinutes($shiftEnd);
            $workingHours = $shiftDurationMinutes / 60; // e.g. 9 hours

            // Rate per minute
            $minuteRate = 0;
            if ($user->base_salary > 0 && $workingHours > 0) {
                // Rate = Salary / 30 days / Working Hours / 60 minutes
                $minuteRate = ($user->base_salary / 30 / $workingHours / 60);
            }

            // --- 1. Calculate Delays (Negative Impact) ---
            $totalDelay = 0;
            
            // Late Arrival
            if ($checkIn && $checkIn->gt($shiftStart)) {
                $totalDelay += abs($checkIn->diffInMinutes($shiftStart));
            }
            
            // Early Departure
            if ($checkOut && $checkOut->lt($shiftEnd)) {
                $totalDelay += abs($checkOut->diffInMinutes($shiftEnd));
            }

            // --- 2. Calculate Overtime (Positive Impact) ---
            $totalOvertime = 0;

            // Early Arrival (Came before shift start)
            if ($checkIn && $checkIn->lt($shiftStart)) {
                $totalOvertime += abs($checkIn->diffInMinutes($shiftStart));
            }

            // Late Departure (Stayed after shift end)
            if ($checkOut && $checkOut->gt($shiftEnd)) {
                $totalOvertime += abs($checkOut->diffInMinutes($shiftEnd));
            }

            // --- 3. Daily Net Calculation ---
            // 1:1 Offsetting
            $balance = $totalOvertime - $totalDelay;

            if ($balance > 0) {
                // Net Overtime
                $overtimeMinutes = $balance;
                $delayMinutes = 0;
                
                // Pay Calculation (Daily calculation for storage, though used mostly for display now)
                $rateMultiplier = $user->overtime_rate ?? 1.5;
                $overtimePay = $overtimeMinutes * $minuteRate * $rateMultiplier;
                $delayDeduction = 0;

            } else {
                // Net Delay
                $overtimeMinutes = 0;
                $delayMinutes = abs($balance);
                
                // Deduction Calculation
                $delayDeduction = $delayMinutes * $minuteRate; // 1.0x Rate
                $overtimePay = 0;
            }
        }

        return [
            'status' => $status,
            'delay_minutes' => $delayMinutes,
            'delay_deduction' => round($delayDeduction, 2),
            'overtime_minutes' => $overtimeMinutes, // Store Integer
            'overtime_pay' => round($overtimePay, 2),
        ];
    }
}

