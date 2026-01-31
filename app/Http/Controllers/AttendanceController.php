<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('admin')) {
            $attendances = Attendance::with('user')->orderBy('date', 'desc')->paginate(20);
        } else {
            $attendances = Attendance::where('user_id', Auth::id())->orderBy('date', 'desc')->paginate(20);
        }
        
        $todayAttendance = Attendance::where('user_id', Auth::id())
            ->where('date', Carbon::today())
            ->first();

        return view('attendance.index', compact('attendances', 'todayAttendance'));
    }

    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();

        if ($attendance) {
            return response()->json(['error' => 'لقد قمت بتسجيل الحضور مسبقاً اليوم.'], 400);
        }

        $now = Carbon::now();
        $delayMinutes = 0;

        if ($user->shift_start) {
            $shiftStart = Carbon::parse($user->shift_start);
            // If check-in is after shift start (with 15 mins grace period maybe? logic simpler for now)
            if ($now->gt($shiftStart)) {
                $delayMinutes = $shiftStart->diffInMinutes($now);
            }
        }

        Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => $now,
            'status' => 'present',
            'ip_address' => $request->ip(),
            'device_info' => $request->header('User-Agent'),
            'delay_minutes' => $delayMinutes,
        ]);

        return response()->json(['success' => 'تم تسجيل الحضور بنجاح!', 'time' => $now->format('h:i A')]);
    }

    public function checkOut(Request $request) {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();

        if (!$attendance) {
            // "Vice Versa" Logic: User Checking Out without Checking In.
            // Assume Check In was at Shift Start
            $shiftStart = $user->shift_start ? Carbon::parse($today . ' ' . $user->shift_start) : Carbon::parse($today . ' 09:00:00');
            
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'check_in' => $shiftStart,
                'status' => 'present',
                'ip_address' => $request->ip(),
                'status_note' => 'تم تحديد الحضور تلقائياً (نسي تسجيل الدخول)',
            ]);
        }
        
        if ($attendance->check_out) {
            return response()->json(['error' => 'لقد قمت بتسجيل الانصراف مسبقاً!'], 400);
        }

        $checkOutTime = Carbon::now();
        $checkInTime = Carbon::parse($attendance->check_in);
        
        // Calculate Logic...
        $totalMinutes = $checkInTime->diffInMinutes($checkOutTime);
        $totalHours = round($totalMinutes / 60, 2);

        $overtimeHours = 0;
        $workingHours = $user->working_hours ?? 8;

        if ($totalHours > $workingHours) {
            $overtimeHours = $totalHours - $workingHours;
        }

        $attendance->update([
            'check_out' => $checkOutTime,
            'total_hours' => $totalHours,
            'overtime_hours' => $overtimeHours,
        ]);

        return response()->json(['success' => 'تم تسجيل الانصراف بنجاح!', 'status' => 'checked_out']);
    }

    public function payroll(Request $request)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('super-admin')) {
            abort(403);
        }

        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $users = User::all();
        $payrollData = [];

        foreach ($users as $user) {
            if (!$user->base_salary) continue;

            $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
            
            // Determine active employment period within this month
            // Use create() with zero time or startOfDay() to avoid current time leakage
            $startOfMonth = Carbon::create($year, $month, 1, 0, 0, 0);
            $endOfMonth = Carbon::create($year, $month, $daysInMonth, 23, 59, 59);
            
            // Auto-fill Missing Checkouts for PAST days in this month
            // We do this before calculation so the numbers are correct.
            $attendancesToFix = Attendance::where('user_id', $user->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->whereNull('check_out')
                ->where('date', '<', Carbon::today()) // Only past days
                ->get();

            foreach ($attendancesToFix as $attendance) {
                // Determine Shift End
                // If user has shift_end, use it. Else default 17:00?
                // Or maybe just +8 hours?
                // User said: "Automatic set departure to shift end".
                
                $shiftEndStr = $user->shift_end ?? '17:00:00';
                $checkOutTime = Carbon::parse($attendance->date . ' ' . $shiftEndStr);
                $checkInTime = Carbon::parse($attendance->check_in);
                
                // If CheckOut < CheckIn (Next day shift?), add day.
                // Assuming same day for now as per simple shift logic.
                if ($checkOutTime->lt($checkInTime)) {
                     // Maybe Shift End is next day? Or just error.
                     // Handled by Carbon logic usually.
                     // If shift_end is 02:00, and date is today.
                     // Carbon parse is Today 02:00.
                     // If checkin is Today 20:00.
                     // Then CheckOut is NEXT DAY.
                     $checkOutTime->addDay();
                }

                $totalMinutes = $checkInTime->diffInMinutes($checkOutTime);
                $totalHours = round($totalMinutes / 60, 2);
                $overtimeHours = 0;
                $workingHours = $user->working_hours ?? 8;

                if ($totalHours > $workingHours) {
                    $overtimeHours = $totalHours - $workingHours;
                }

                $attendance->update([
                    'check_out' => $checkOutTime,
                    'total_hours' => $totalHours,
                    'overtime_hours' => $overtimeHours,
                    'status_note' => 'تم تسجيل الانصراف تلقائياً (نسي تسجيل الخروج)',
                ]);
            }

            // Effective Start Date: Max(Month Start, Joining Date)
            $effectiveStartDate = $startOfMonth->copy();
            if ($user->joining_date) {
                $joiningDate = Carbon::parse($user->joining_date);
                if ($joiningDate->gt($effectiveStartDate)) {
                    $effectiveStartDate = $joiningDate->copy();
                }
            }

            // Effective End Date: Min(Month End, Resignation Date, Today)
            $effectiveEndDate = $endOfMonth->copy();
            
            // Only count days that have passed (cap at today)
            if (Carbon::today()->lt($effectiveEndDate)) {
                $effectiveEndDate = Carbon::today();
            }

            if ($user->resignation_date) {
                $resignationDate = Carbon::parse($user->resignation_date);
                if ($resignationDate->lt($effectiveEndDate)) {
                    $effectiveEndDate = $resignationDate->copy();
                }
            }

            $fridays = 0;
            // Iterate from Effective Start to Effective End
            // Ensure Start <= End (in case user joined after month end or resigned before month start)
            
            if ($effectiveStartDate->lte($effectiveEndDate) && $effectiveStartDate->month == $month) {
                $currentDay = $effectiveStartDate->copy();
                while ($currentDay->lte($effectiveEndDate)) {
                    if ($currentDay->isFriday()) {
                        $fridays++;
                    }
                    $currentDay->addDay();
                }
            }

            $presentDays = Attendance::where('user_id', $user->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('status', 'present')
                ->count();
            
            $totalOvertimeHours = Attendance::where('user_id', $user->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('overtime_hours');

            $totalDelayMinutes = Attendance::where('user_id', $user->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('delay_minutes');

            // Salary Calculation: (Base Salary / 30) * (Present Days + Fridays)
            
            $dailySalary = $user->base_salary / 30;
            $hourlySalary = $dailySalary / ($user->working_hours ?? 8);
            
            // Convert delay minutes to hours for easier calculation
            $totalDelayHours = $totalDelayMinutes / 60;

            // NEW LOGIC: Overtime first offsets lateness (no pay for offset), then excess at 1.5x
            // If overtime > delay: (overtime - delay) * 1.5x is paid, NO delay deduction
            // If overtime <= delay: NO overtime pay, (delay - overtime) is deducted
            
            $overtimeRate = $user->overtime_rate ?? 1.5;
            $overtimePay = 0;
            $delayDeduction = 0;
            
            if ($totalOvertimeHours > $totalDelayHours) {
                // Overtime exceeds delay
                // First part offsets delay completely (no payment for this)
                // Only the EXCESS overtime is paid at 1.5x rate
                $excessOvertimeHours = $totalOvertimeHours - $totalDelayHours;
                $overtimePay = $excessOvertimeHours * $hourlySalary * $overtimeRate;
                
                // No delay deduction since overtime covered it all
                $delayDeduction = 0;
            } else {
                // Overtime does not exceed delay
                // All overtime goes to offset delay (no overtime pay)
                $overtimePay = 0;
                
                // Remaining delay after offset
                $remainingDelayHours = $totalDelayHours - $totalOvertimeHours;
                $delayDeduction = $remainingDelayHours * $hourlySalary;
            }

            $workingDays = $presentDays + $fridays; // Fridays are paid holidays
            
            // Adjust if workingDays > daysInMonth (edge case)
            if ($workingDays > $daysInMonth) $workingDays = $daysInMonth;

            $basicSalary = $dailySalary * $workingDays;
            
            // Deduct delay first, then add overtime
            $salaryAfterDelay = $basicSalary - $delayDeduction;
            $totalSalary = $salaryAfterDelay + $overtimePay;

            $payrollData[] = [
                'user' => $user,
                'present_days' => $presentDays,
                'fridays' => $fridays,
                'delay_minutes' => $totalDelayMinutes,
                'delay_deduction' => number_format($delayDeduction, 2),
                'overtime_hours' => $totalOvertimeHours,
                'overtime_pay' => number_format($overtimePay, 2),
                'basic_salary' => number_format($basicSalary, 2),
                'total_salary' => number_format($totalSalary, 2)
            ];
        }

        return view('payroll.index', compact('payrollData', 'month', 'year'));
    }
}
