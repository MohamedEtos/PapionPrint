<?php

namespace App\Services\Charts;

use App\Models\Stras;
use App\Models\Tarter;
use App\Models\InventoryLog;
use Carbon\Carbon;

class ConsumptionChartService
{
    public function getInkConsumption($period, $machine)
    {
        $query = InventoryLog::where('type', 'ink')
            ->where('machine_type', $machine);

        return $this->processChartData($query, $period, 'ink');
    }

    public function getStrasTarterConsumption($period, $type)
    {
        $model = ($type == 'tarter' || $type == 'ترتر') ? Tarter::class : Stras::class;
        $query = $model::query();

        return $this->processChartData($query, $period, 'stras_tarter');
    }

    private function processChartData($query, $period, $chartType)
    {
        // Date Logic
        Carbon::setLocale('ar');
        $now = Carbon::now();
        $startDate = $now->copy();
        $previousStartDate = $now->copy();
        
        $sqlFormat = '%Y-%m-%d';
        
        if ($period == 'week') {
            $startDate->subDays(7);
            $previousStartDate->subDays(14);
        } elseif ($period == 'month') {
            $startDate->subMonth();
            $previousStartDate->subMonths(2);
        } elseif ($period == 'year') {
            $startDate->subYear();
            $previousStartDate->subYears(2);
            $sqlFormat = '%Y-%m'; 
        }

        // Fetch Data
        if ($chartType === 'stras_tarter') {
             // Consumption = Height * CardsCount
             $currentData = (clone $query)->whereBetween('created_at', [$startDate, $now])
                ->selectRaw("DATE_FORMAT(created_at, '$sqlFormat') as date, SUM(height * cards_count) as total")
                ->groupBy('date')
                ->orderBy('date')
                ->get();

             $lastData = (clone $query)->whereBetween('created_at', [$previousStartDate, $startDate])
                ->selectRaw("DATE_FORMAT(created_at, '$sqlFormat') as date, SUM(height * cards_count) as total")
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else {
             // Ink Consumption (Quantity)
             $currentData = (clone $query)->whereBetween('created_at', [$startDate, $now])
                ->selectRaw("DATE_FORMAT(created_at, '$sqlFormat') as date, SUM(quantity) as total")
                ->groupBy('date')
                ->orderBy('date')
                ->get();

             $lastData = (clone $query)->whereBetween('created_at', [$previousStartDate, $startDate])
                ->selectRaw("DATE_FORMAT(created_at, '$sqlFormat') as date, SUM(quantity) as total")
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        // Process Labels and Series
        $labels = [];
        $currentSeries = [];
        $lastSeries = [];
        
        $periodMap = [
            'week' => 7,
            'month' => 30, // Approx
            'year' => 12
        ];
        
        $count = $periodMap[$period] ?? 7;
        
        for ($i = $count - 1; $i >= 0; $i--) {
             if ($period == 'year') {
                 $dateObj = Carbon::now()->subMonths($i);
                 $d = $dateObj->format('Y-m');
                 $label = $dateObj->locale('ar')->translatedFormat('F Y'); // Arabic Month Year
                 
                 $prevDKey = Carbon::now()->subMonths($i + 12)->format('Y-m');
             } else {
                 $dateObj = Carbon::now()->subDays($i);
                 $d = $dateObj->format('Y-m-d');
                 
                 if ($period == 'week') {
                     $label = $dateObj->locale('ar')->translatedFormat('l'); // Arabic Day Name
                 } else {
                     $label = $dateObj->locale('ar')->translatedFormat('d M'); // Day Month
                 }
             }
             
             $labels[] = $label;
             
             // Find & Value Logic
             $currVal = $currentData->firstWhere('date', $d);
             
             if ($chartType === 'stras_tarter') {
                 $val = $currVal ? $currVal->total / 100 : 0; // Convert to Meters
                 $lastValRaw = $lastData->firstWhere('date', isset($prevDKey) ? $prevDKey : Carbon::createFromFormat('Y-m-d', $d)->subDays($count)->format('Y-m-d'));
                 $valOld = $lastValRaw ? $lastValRaw->total / 100 : 0;
             } else {
                 $val = $currVal ? abs($currVal->total) : 0;
                 $lastValRaw = $lastData->firstWhere('date', isset($prevDKey) ? $prevDKey : Carbon::createFromFormat('Y-m-d', $d)->subDays($count)->format('Y-m-d'));
                 $valOld = $lastValRaw ? abs($lastValRaw->total) : 0;
             }

             $currentSeries[] = $val;
             $lastSeries[] = $valOld;
        }

        return [
            'labels' => $labels,
            'currentData' => $currentSeries,
            'lastData' => $lastSeries,
            'currentTotal' => number_format(array_sum($currentSeries), 2),
            'lastTotal' => number_format(array_sum($lastSeries), 2)
        ];
    }
}
