<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Charts\MeterChartService;
use App\Services\Charts\OrdersChartService;
use App\Services\Charts\CustomerChartService;
use App\Services\Charts\ClientRetentionChartService;
use App\Services\Charts\ConsumptionChartService;
use Carbon\Carbon;

class ChartController extends Controller
{
    protected $meterChartService;
    protected $ordersChartService;
    protected $customerChartService;
    protected $clientRetentionChartService;
    protected $consumptionChartService;

    public function __construct(
        MeterChartService $meterChartService, 
        OrdersChartService $ordersChartService, 
        CustomerChartService $customerChartService,
        ClientRetentionChartService $clientRetentionChartService,
        ConsumptionChartService $consumptionChartService
    )
    {
        $this->meterChartService = $meterChartService;
        $this->ordersChartService = $ordersChartService;
        $this->customerChartService = $customerChartService;
        $this->clientRetentionChartService = $clientRetentionChartService;
        $this->consumptionChartService = $consumptionChartService;
    }

    public function getCustomersData(CustomerChartService $service)
    {
        $data = $service->getCustomersData();
        return response()->json($data);
    }

    public function getOrdersData(OrdersChartService $service)
    {
        $data = $service->getOrdersData();
        return response()->json($data);
    }

    public function getMeterData(Request $request, MeterChartService $service)
    {
        $period = $request->input('period', 'month');
        $machine = $request->input('machine', 'sublimation');

        $data = $service->getChartData($period, $machine);

        if (isset($data['error'])) {
            return response()->json($data, 404);
        }

        return response()->json($data);
    }

    public function getClientRetentionData(Request $request, ClientRetentionChartService $service)
    {
        $period = $request->input('period', 'month');
        $data = $service->getChartData($period);
        return response()->json($data);
    }

    public function getInventoryData(Request $request)
    {
        $period = $request->input('period', '7_days');
        
        $date = \Carbon\Carbon::now();
        switch($period) {
            case '7_days': $date->subDays(7); break;
            case '28_days': $date->subDays(28); break;
            case 'month': $date->subMonth(); break;
            case 'year': $date->subYear(); break;
            default: $date->subDays(7);
        }

        // Paper Consumption (From Printers/Orders)
        // Sublimation
        $paperSub = \App\Models\Printers::where('created_at', '>=', $date)
            ->whereHas('machines', function($q) {
                $q->where('name', 'not like', '%dtf%')->where('name', 'not like', '%DTF%');
            })->sum('meters');

        // DTF
        $paperDtf = \App\Models\Printers::where('created_at', '>=', $date)
            ->whereHas('machines', function($q) {
                $q->where('name', 'like', '%dtf%')->orWhere('name', 'like', '%DTF%');
            })->sum('meters');


        // Ink Consumption (From InventoryLogs)
        $inkSub = \App\Models\InventoryLog::where('type', 'ink')
            ->where('machine_type', 'sublimation')
            ->where('created_at', '>=', $date)
            ->sum('quantity');

        $inkDtf = \App\Models\InventoryLog::where('type', 'ink')
            ->where('machine_type', 'dtf')
            ->where('created_at', '>=', $date)
            ->sum('quantity');

        return response()->json([
            'series' => [$paperSub, $paperDtf, $inkSub, $inkDtf],
            'labels' => ['Paper Sub (m)', 'Paper DTF (m)', 'Ink Sub (L)', 'Ink DTF (L)'],
            'stats' => [
                'paper_sub' => number_format($paperSub, 2),
                'paper_dtf' => number_format($paperDtf, 2),
                'ink_sub' => number_format($inkSub, 2),
                'ink_dtf' => number_format($inkDtf, 2),
            ]
        ]);
    }

    public function getInventoryStockData()
    {
        // Fetch Stocks similar to AddPrintOrders logic
        $inkStocks = \App\Models\Stock::where('type', 'ink')->get();
        $paperStocks = \App\Models\Stock::where('type', 'paper')->get();

        // Helper to get quantity safely
        $getQty = function($stocks, $machineType, $color = null) {
            $query = $stocks->where('machine_type', $machineType);
            if ($color) {
                $query = $query->where('color', $color);
            }
            return $query->first()->quantity ?? 0;
        };

        // Ink Data Structure (Sublimation C, M, Y, K then DTF C, M, Y, K, W)
        // Order: Sub-C, Sub-M, Sub-Y, Sub-K, DTF-C, DTF-M, DTF-Y, DTF-K, DTF-W
        $inkData = [
            $getQty($inkStocks, 'sublimation', 'Cyan'),
            $getQty($inkStocks, 'sublimation', 'Magenta'),
            $getQty($inkStocks, 'sublimation', 'Yellow'),
            $getQty($inkStocks, 'sublimation', 'Black'),
            $getQty($inkStocks, 'dtf', 'Cyan'),
            $getQty($inkStocks, 'dtf', 'Magenta'),
            $getQty($inkStocks, 'dtf', 'Yellow'),
            $getQty($inkStocks, 'dtf', 'Black'),
            $getQty($inkStocks, 'dtf', 'White')
        ];

        // Paper Data (Sublimation, DTF)
        $paperData = [
            'sublimation' => $getQty($paperStocks, 'sublimation'),
            'dtf' => $getQty($paperStocks, 'dtf')
        ];

        return response()->json([
            'ink_series' => [
                [
                    'name' => 'Stock',
                    'data' => $inkData
                ]
            ],
            // Colors matching the order: Cyan, Magenta, Yellow, Black, Cyan, Magenta, Yellow, Black, White(ish)
            // Softer Colors: Info, Danger, Warning, Dark Grey, Info, Danger, Warning, Dark Grey, Light Grey
            'colors' => ['#00CFE8', '#EA5455', '#FF9F43', '#4B4B4B', '#00CFE8', '#EA5455', '#FF9F43', '#4B4B4B', '#E5E7EB'],
            'labels' => ['Sub-C', 'Sub-M', 'Sub-Y', 'Sub-K', 'DTF-C', 'DTF-M', 'DTF-Y', 'DTF-K', 'DTF-W'],
            'paper' => $paperData
        ]);
    }

    // public function getInkConsumptionData(Request $request)
    // {
    //     $period = $request->input('period', 'week');
    //     $machine = $request->input('machine', 'sublimation');

    //     $query = \App\Models\InventoryLog::where('type', 'ink')
    //         ->where('machine_type', $machine);

    //     // Date Logic (Current vs Last Period)
    //     $now = \Carbon\Carbon::now();
    //     $startDate = $now->copy();
    //     $previousStartDate = $now->copy();
    //     $format = 'Y-m-d';
        
    //     if ($period == 'week') {
    //         $startDate->subDays(7);
    //         $previousStartDate->subDays(14);
    //     } elseif ($period == 'month') {
    //         $startDate->subMonth();
    //         $previousStartDate->subMonths(2);
    //     } elseif ($period == 'year') {
    //         $startDate->subYear();
    //         $previousStartDate->subYears(2);
    //         $format = 'Y-m'; // Group by month for year view
    //     }

    //     // Fetch Data
    //     $currentData = (clone $query)->whereBetween('created_at', [$startDate, $now])
    //         ->selectRaw("DATE_FORMAT(created_at, '$format') as date, SUM(quantity) as total")
    //         ->groupBy('date')
    //         ->orderBy('date')
    //         ->get();

    //     $lastData = (clone $query)->whereBetween('created_at', [$previousStartDate, $startDate])
    //         ->selectRaw("DATE_FORMAT(created_at, '$format') as date, SUM(quantity) as total")
    //         ->groupBy('date')
    //         ->orderBy('date')
    //         ->get();

    //     // Process Labels and Series
    //     // We need to match dates or just show sequence? 
    //     // Existing chart usually maps days. For simplicity we'll just return the values mapped to days count.
    //     // Better: Generate labels based on period.
        
    //     $labels = [];
    //     $currentSeries = [];
    //     $lastSeries = [];
        
    //     // Simple mapping for demonstration (robust solution would fill missing dates)
    //     // If 'week', we expect 7 days.
    //     // Let's iterate backwards from today for Labels
        
    //     $periodMap = [
    //         'week' => 7,
    //         'month' => 30, // Approx
    //         'year' => 12
    //     ];
        
    //     $count = $periodMap[$period] ?? 7;
        
    //     for ($i = $count - 1; $i >= 0; $i--) {
    //          if ($period == 'year') {
    //              $d = \Carbon\Carbon::now()->subMonths($i)->format('Y-m');
    //              $prevD = \Carbon\Carbon::now()->subMonths($i + 12)->format('Y-m'); // Approx previous year match
    //          } else {
    //              $d = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
    //              // For previous period day matching, we can just take the value from the previous dataset relative to index?
    //              // Or match strictly by date - period?
    //              // Let's just push values if they exist for that specific date key
    //          }
             
    //          $labels[] = $d;
             
    //          // Find in Current
    //          $currVal = $currentData->firstWhere('date', $d);
    //          $currentSeries[] = $currVal ? abs($currVal->total) : 0; // logs might be negative? consumption usually is negative in logs if deducted? 
    //          // Wait, InventoryLog: is it negative for consumption?
    //          // Usually logs track changes. If we consume, it's negative. So we abs() it.
             
    //          // Find in Last
    //          // For last data, the date will be $d minus period.
    //          // We can just use the index if we fetched strictly?
    //          // Or we map strictly.
    //          $prevDateObj = ($period == 'year') ? \Carbon\Carbon::createFromFormat('Y-m', $d)->subYear() : \Carbon\Carbon::createFromFormat('Y-m-d', $d)->subDays($count);
    //          $prevDKey = $prevDateObj->format($period == 'year' ? 'Y-m' : 'Y-m-d');
             
    //          $lastVal = $lastData->firstWhere('date', $prevDKey);
    //          $lastSeries[] = $lastVal ? abs($lastVal->total) : 0;
    //     }

    //     return response()->json([
    //         'labels' => $labels,
    //         'currentData' => $currentSeries,
    //         'lastData' => $lastSeries,
    //         'currentTotal' => number_format(array_sum($currentSeries), 2),
    //         'lastTotal' => number_format(array_sum($lastSeries), 2)
    //     ]);
    // }

    public function getInkConsumptionData(Request $request)
    {
        $period = $request->input('period', 'week');
        $machine = $request->input('machine', 'sublimation');

        return response()->json($this->consumptionChartService->getInkConsumption($period, $machine));
    }

    public function getStrasTarterConsumptionData(Request $request)
    {
        $period = $request->input('period', 'week');
        $type = $request->input('machine', 'stras');

        return response()->json($this->consumptionChartService->getStrasTarterConsumption($period, $type));
    }
}
