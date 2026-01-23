<?php

namespace App\Services;

use App\Models\Printers;
use App\Models\customers; // Add this line
use Carbon\Carbon;
use Illuminate\Support\Collection;

class thisMonthMeterPrintedSublimation
{
    /**
     * Get the sales series for the dashboard chart.
     * Logic matches: Active Sublimation printers relative to specific day ranges.
     *
     * @param Carbon $monthStart
     * @return Collection
     */
    /**
     * Get sales series based on period.
     *
     * @param string $period
     * @return array ['series' => Collection, 'labels' => Collection]
     */
    public function getSublimationSalesSeriesForPeriod(string $period = 'week'): array
    {
        $series = collect();
        $labels = collect();
        $now = Carbon::now();

        if ($period === 'month') {
            $daysInMonth = $now->daysInMonth;
            $range = range(1, $daysInMonth);
            $monthStart = $now->copy()->startOfMonth();

            $series = collect($range)->map(function ($day) use ($monthStart) {
                $start = $monthStart->copy()->day($day)->startOfDay();
                $end = $start->copy()->endOfDay();
                return $this->getMetersForRange($start, $end);
            });

            $labels = collect($range)->map(function ($day) {
                return str_pad($day, 2, '0', STR_PAD_LEFT);
            });

        } elseif ($period === 'year') {
            $range = range(1, 12);
            $yearStart = $now->copy()->startOfYear();

            $series = collect($range)->map(function ($month) use ($yearStart) {
                $start = $yearStart->copy()->month($month)->startOfMonth()->startOfDay();
                $end = $yearStart->copy()->month($month)->endOfMonth()->endOfDay();
                return $this->getMetersForRange($start, $end);
            });

            $labels = collect($range)->map(function ($month) use ($yearStart) {
                return $yearStart->copy()->month($month)->translatedFormat('M');
            });

        } else { // default to week
            $range = range(6, 0);
            
            $series = collect($range)->map(function ($daysAgo) use ($now) {
                $date = $now->copy()->subDays($daysAgo);
                return $this->getMetersForRange($date->copy()->startOfDay(), $date->copy()->endOfDay());
            });

            $labels = collect($range)->map(function ($daysAgo) use ($now) {
                return $now->copy()->subDays($daysAgo)->translatedFormat('D');
            });
        }

        return ['series' => $series, 'labels' => $labels];
    }

    private function getMetersForRange($start, $end) {
        return (float) Printers::active()
            ->sublimation()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->sum('meters');
    }

    /**
     * Get total sublimation meters for a period.
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return float
     */
    public function getSublimationTotal(Carbon $start, Carbon $end): float
    {
        return (float) Printers::active()
            ->sublimation()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->sum('meters');
    }


    /**
     * Get new customers count series.
     *
     * @param Carbon $monthStart
     * @return Collection
     */
    public function getNewCustomersSeries(): Collection
    {
        return collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::now()->subDays($daysAgo);
            $start = $date->copy()->startOfDay();
            $end = $date->copy()->endOfDay();

            return (int) customers::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->count();
        })->values();
    }

    /**
     * Get total meters for all machines (excluding machineId 0) for a period.
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return float
     */
    public function getGeneralTotal(Carbon $start, Carbon $end): float
    {
        return (float) Printers::active()
            ->where('machineId', '!=', '0')
            ->whereBetween('created_at', [$start, $end])
            ->sum('meters');
    }
}
