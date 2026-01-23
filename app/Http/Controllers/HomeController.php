<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function getChartData(Request $request)
    {
        $period = $request->input('period', 'month'); // week, month, year

        $machineId = \App\Models\Machines::where('name', 'sublimation')->value('id');

        if (!$machineId) {
            return response()->json(['error' => 'Machine not found'], 404);
        }

        $query = \App\Models\Printers::where('machineId', $machineId)
            ->where('archive', '!=', 0);

        $now = \Carbon\Carbon::now();
        $labels = [];
        $currentData = [];
        $lastData = [];

        if ($period == 'week') {
            // Last 7 days vs previous 7 days
            for ($i = 0; $i < 7; $i++) {
                $date = $now->copy()->subDays(6 - $i);
                $labels[] = $date->format('D');

                // Current Period
                $currentData[] = (clone $query)->whereDate('created_at', $date->toDateString())->sum('meters');

                // Last Period (7 days before)
                $lastDate = $date->copy()->subDays(7);
                $lastData[] = (clone $query)->whereDate('created_at', $lastDate->toDateString())->sum('meters');
            }
        } elseif ($period == 'month') {
            // This Month vs Last Month (Daily breakdown)
            $daysInMonth = $now->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                 $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT);
                 
                 // Current Month
                 $currentData[] = (clone $query)->whereYear('created_at', $now->year)
                                                ->whereMonth('created_at', $now->month)
                                                ->whereDay('created_at', $i)
                                                ->sum('meters');

                 // Last Month
                 $lastMonth = $now->copy()->subMonth();
                 $lastData[] = (clone $query)->whereYear('created_at', $lastMonth->year)
                                             ->whereMonth('created_at', $lastMonth->month)
                                             ->whereDay('created_at', $i)
                                             ->sum('meters');
            }
        } elseif ($period == 'year') {
             // This Year vs Last Year (Monthly breakdown)
             for ($i = 1; $i <= 12; $i++) {
                 $labels[] = \Carbon\Carbon::create()->month($i)->format('M');

                 // Current Year
                 $currentData[] = (clone $query)->whereYear('created_at', $now->year)
                                                ->whereMonth('created_at', $i)
                                                ->sum('meters');

                 // Last Year
                 $lastYear = $now->copy()->subYear();
                 $lastData[] = (clone $query)->whereYear('created_at', $lastYear->year)
                                             ->whereMonth('created_at', $i)
                                             ->sum('meters');
             }
        }

        return response()->json([
            'labels' => $labels,
            'currentData' => $currentData,
            'lastData' => $lastData,
            'currentTotal' => array_sum($currentData),
            'lastTotal' => array_sum($lastData)
        ]);
    }
}
