<?php

namespace App\Services\Charts;

use App\Models\Tarter;
use Carbon\Carbon;

class TarterOrdersChartService
{
    public function getTarterOrdersData()
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
            $count = Tarter::whereDate('created_at', $date->toDateString())->count();
            $data[] = $count;
        }

        $totalOrders = Tarter::count();

        return [
            'labels' => $labels,
            'totalOrders' => $totalOrders,
            'series' => [
                [
                    'name' => 'اوردرات ترتر',
                    'data' => $data
                ]
            ]
        ];
    }
}
