<?php

namespace App\Http\Controllers;

use App\Models\BiometricAttendance;
use App\Models\BiometricUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BiometricAttendanceController extends Controller
{
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

        // Calculate Payroll Summary
        $payrollData = [];
        foreach ($biometricUsers as $u) {
            $userAttendances = $attendances->where('biometric_user_id', $u->id);
            
            $totalDelayMinutes = $userAttendances->sum('delay_minutes');
            $totalOvertimeMinutes = $userAttendances->sum('overtime_minutes');
            
            $totalDelayDeduction = $userAttendances->sum('delay_deduction');
            $totalAbsenceDeduction = $userAttendances->sum('absence_deduction');
            
            $totalOvertimePay = $userAttendances->sum('overtime_pay');
            
            $netSalary = $u->base_salary - $totalDelayDeduction - $totalAbsenceDeduction + $totalOvertimePay;
            
            // Safety check for negative salary (though theoretically possible with huge deductions)
            // $netSalary = max(0, $netSalary); 

            $payrollData[$u->id] = [
                'user' => $u,
                'total_delay_minutes' => $totalDelayMinutes,
                'total_overtime_minutes' => $totalOvertimeMinutes,
                'total_deductions' => $totalDelayDeduction + $totalAbsenceDeduction,
                'total_overtime_pay' => $totalOvertimePay,
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
                    sort($punches);
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
                    $delayDeduction = 0;
                    $overtimePay = 0;

                    // Shift logic from BiometricUser
                    $shiftStart = $biometricUser->shift_start ? Carbon::parse($date . ' ' . $biometricUser->shift_start) : null;
                    $shiftEnd   = $biometricUser->shift_end ? Carbon::parse($date . ' ' . $biometricUser->shift_end) : null;

                    if ($isFriday) {
                        $status = 'weekend'; // Or holiday
                    } elseif ($shiftStart && $shiftEnd) {
                         // Auto-fill logic
                         $punchesCount = count($punches);
                         if ($punchesCount == 1) {
                             $diffStart = $firstPunch->diffInMinutes($shiftStart);
                             $diffEnd = $firstPunch->diffInMinutes($shiftEnd);
                             
                             if ($diffStart < $diffEnd) {
                                 // Assumed Check-In, missing Check-Out -> fill end
                                 $checkOut = $shiftEnd; 
                             } else {
                                 // Assumed Check-Out, missing Check-In -> fill start
                                 $checkIn = $shiftStart; 
                             }
                         }

                        // Delay
                        if ($checkIn->gt($shiftStart)) {
                             // Consider grace period? e.g. 15 mins
                            $delayMinutes = $checkIn->diffInMinutes($shiftStart);
                            // Simple calculation logic: (Salary / 30 days / 8 hours / 60 mins) * delay * deduction_rate?
                            // User didn't specify formula, keeping generic for now.
                        }

                        // Overtime
                        if ($checkOut->gt($shiftEnd)) {
                            $overtimeMinutes = $checkOut->diffInMinutes($shiftEnd);
                            $hourlyRate = 0;
                            // Calculate pay if salary exists
                            if ($biometricUser->base_salary > 0) {
                                // Assume 30 days, 8 hours? Or user defined working hours?
                                // Standard: Salary / 30 / 8
                                $hourlyRate = ($biometricUser->base_salary / 30) / 8;
                            }
                            // Overtime pay = minutes/60 * rate * factor
                            $overtimePay = ($overtimeMinutes / 60) * $hourlyRate * $biometricUser->overtime_rate;
                        }
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
            
            if (!$attendance->is_friday && $shiftStart && $shiftEnd) {
                
                // --- Delay Calculation ---
                // If CheckIn is AFTER ShiftStart
                if ($checkIn && $checkIn->gt($shiftStart)) {
                    $delayMinutes = $checkIn->diffInMinutes($shiftStart);
                    
                    // Deduction Logic
                    if ($user->base_salary > 0) {
                         // Rate per minute = Salary / 30 days / 8 hours / 60 minutes
                         $minuteRate = ($user->base_salary / 30 / 8 / 60);
                         $delayDeduction = $delayMinutes * $minuteRate;
                    }
                }

                // --- Overtime Calculation ---
                // If CheckOut is AFTER ShiftEnd
                if ($checkOut && $checkOut->gt($shiftEnd)) {
                    $overtimeMinutes = $checkOut->diffInMinutes($shiftEnd);
                    
                    // Overtime Pay Logic
                    if ($user->base_salary > 0) {
                         $minuteRate = ($user->base_salary / 30 / 8 / 60);
                         // Default overtime rate is 1.0 if null
                         $rateMultiplier = $user->overtime_rate ?? 1.0;
                         $overtimePay = $overtimeMinutes * $minuteRate * $rateMultiplier;
                    }
                }
            }

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
}

