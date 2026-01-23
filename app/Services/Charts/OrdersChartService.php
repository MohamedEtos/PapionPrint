<?php

namespace App\Services\Charts;

use App\Models\Printers; // Assuming orders are in Printers model as per context
use Carbon\Carbon;

class OrdersChartService
{
    public function getOrdersData()
    {
        Carbon::setLocale('ar');
        $now = Carbon::now();
        $labels = [];
        $data = [];

        // Last 7 days
        for ($i = 0; $i < 7; $i++) {
            $date = $now->copy()->subDays(6 - $i);
            $dayName = $date->locale('ar')->translatedFormat('l'); // Day name in Arabic
            
            $labels[] = $dayName;

            // Count orders for this day
            $count = Printers::whereDate('created_at', $date->toDateString())->count();
            $data[] = $count;
        }

        $totalOrders = Printers::count();

        return [
            'labels' => $labels,
            'totalOrders' => $totalOrders,
            'series' => [
                [
                    'name' => 'الطلبات', // General series name
                    'data' => $data
                ]
            ]
        ];
    }
}
