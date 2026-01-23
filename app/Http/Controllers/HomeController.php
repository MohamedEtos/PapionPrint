<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\thisMonthMeterPrintedSublimation;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param thisMonthMeterPrintedSublimation $thisMonthMeterPrintedSublimation
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, thisMonthMeterPrintedSublimation $thisMonthMeterPrintedSublimation)
    {
        $now = Carbon::now();
        $period = $request->get('period', 'week');
        $periodLabel = match($period) {
            'month' => 'This Month',
            'year' => 'This Year',
            default => 'Last 7 Days',
        };

        // -------- This Month (Sublimation Series with Period) --------
        $chartData = $thisMonthMeterPrintedSublimation->getSublimationSalesSeriesForPeriod($period);
        $thisMonthSeries = $chartData['series'];
        $daysLabels = $chartData['labels'];

        $thisStart = $now->copy()->startOfMonth();
        $thisMonthTotal = $thisMonthMeterPrintedSublimation->getSublimationTotal(
            $thisStart->copy()->startOfDay(),
            $now->copy()->endOfDay()
        );

        // -------- Last Month (Comparison Logic could be improved later for periods) --------
        $lastStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthSeries = collect(); // Keeping empty as per previous step decision
        
        $lastMonthTotal = $thisMonthMeterPrintedSublimation->getGeneralTotal(
            $lastStart->copy()->startOfDay(),
            $lastStart->copy()->endOfMonth()->endOfDay()
        );

        // -------- Customers Series (If needed per period later, we can add getNewCustomersSeriesForPeriod) --------
        $customerSeries = $thisMonthMeterPrintedSublimation->getNewCustomersSeries();

        return view('home', [
            'thisMonthSeries' => $thisMonthSeries,
            'lastMonthSeries' => $lastMonthSeries,
            'thisMonthTotal'  => $thisMonthTotal,
            'lastMonthTotal'  => $lastMonthTotal,
            'customerSeries'  => $customerSeries,
            'daysLabels'      => $daysLabels,
            'periodLabel'     => $periodLabel,
        ]);
    }
}
