<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Charts\MeterChartService;
use App\Services\Charts\OrdersChartService;
use App\Services\Charts\CustomerChartService;
use App\Services\Charts\ClientRetentionChartService;

class ChartController extends Controller
{
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
}
