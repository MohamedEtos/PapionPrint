<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Charts\MeterChartService;
use App\Services\Charts\OrdersChartService;
use App\Services\Charts\CustomerChartService;

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
}
