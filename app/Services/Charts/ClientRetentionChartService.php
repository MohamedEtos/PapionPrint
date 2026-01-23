<?php

namespace App\Services\Charts;

use App\Models\customers;
use App\Models\Printers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClientRetentionChartService
{
    public function getChartData($period = 'month')
    {
        Carbon::setLocale('ar');
        $now = Carbon::now();
        $labels = [];
        $newClientsData = [];
        $retainedClientsData = [];

        if ($period == 'week') {
            // Last 7 days
            for ($i = 0; $i < 7; $i++) {
                $date = $now->copy()->subDays(6 - $i);
                $labels[] = $date->locale('ar')->translatedFormat('l'); // Day Name

                // New Clients: Created on this day
                $newClientsData[] = customers::whereDate('created_at', $date->toDateString())->count();

                // Retained Clients: Placed order on this day, but created BEFORE this day
                // We count unique customers who have orders on this day AND were created < this day
                $retainedCount = Printers::whereDate('created_at', $date->toDateString())
                    ->whereHas('customers', function ($q) use ($date) {
                        $q->whereDate('created_at', '<', $date->toDateString());
                    })
                    ->distinct('customerId')
                    ->count('customerId');
                
                $retainedClientsData[] = -abs($retainedCount); // Negative for "down" bar
            }
        } elseif ($period == 'month') {
             // Days of this month
             $daysInMonth = $now->daysInMonth;
             for ($i = 1; $i <= $daysInMonth; $i++) {
                 $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT);
                 
                 // New Clients
                 $newClientsData[] = customers::whereYear('created_at', $now->year)
                                        ->whereMonth('created_at', $now->month)
                                        ->whereDay('created_at', $i)
                                        ->count();

                 // Retained
                 $currentDate = Carbon::create($now->year, $now->month, $i);
                 $retainedCount = Printers::whereYear('created_at', $now->year)
                    ->whereMonth('created_at', $now->month)
                    ->whereDay('created_at', $i)
                    ->whereHas('customers', function ($q) use ($currentDate) {
                        $q->whereDate('created_at', '<', $currentDate->toDateString());
                    })
                    ->distinct('customerId')
                    ->count('customerId');

                 $retainedClientsData[] = -abs($retainedCount);
             }

        } elseif ($period == 'year') {
            // Months of this year
            for ($i = 1; $i <= 12; $i++) {
                $labels[] = Carbon::create()->month($i)->locale('ar')->translatedFormat('M');

                // New Clients
                $newClientsData[] = customers::whereYear('created_at', $now->year)
                                    ->whereMonth('created_at', $i)
                                    ->count();
                
                 // Retained
                 // For month granularity, we check for orders in that month, from customers created BEFORE that month
                 $startOfMonth = Carbon::create($now->year, $i, 1);
                 
                 $retainedCount = Printers::whereYear('created_at', $now->year)
                    ->whereMonth('created_at', $i)
                    ->whereHas('customers', function ($q) use ($startOfMonth) {
                         $q->whereDate('created_at', '<', $startOfMonth->toDateString());
                    })
                    ->distinct('customerId')
                    ->count('customerId');

                 $retainedClientsData[] = -abs($retainedCount);
            }
        }

        return [
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'New Clients',
                    'data' => $newClientsData
                ],
                [
                    'name' => 'Retained Clients',
                    'data' => $retainedClientsData
                ]
            ]
        ];
    }
}
