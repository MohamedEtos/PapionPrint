<?php

namespace App\Services\Charts;

use App\Models\Customers;
use Carbon\Carbon;

class CustomerChartService
{
    public function getCustomersData()
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

            // Count customers created on this day
            $count = Customers::whereDate('created_at', $date->toDateString())->count();
            $data[] = $count;
        }

        $totalCustomers = Customers::count();

        return [
            'labels' => $labels,
            'totalCustomers' => $totalCustomers,
            'series' => [
                [
                    'name' => 'العملاء',
                    'data' => $data
                ]
            ]
        ];
    }
}
