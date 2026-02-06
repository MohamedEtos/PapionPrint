<?php

namespace App\Services\Charts;

use App\Models\Stras;
use Carbon\Carbon;

class StrasOrdersChartService
{
    public function getStrasOrdersData()
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
            $count = Stras::whereDate('created_at', $date->toDateString())->count();
            $data[] = $count;
        }

        $totalOrders = Stras::count();

        return [
            'labels' => $labels,
            'totalOrders' => $totalOrders,
            'series' => [
                [
                    'name' => 'اوردرات استراس',
                    'data' => $data
                ]
            ]
        ];
    }
}
