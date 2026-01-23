<?php

namespace App\Http\Controllers;

use App\Models\Printerlogs;
use Illuminate\Http\Request;
use App\Models\Printers;
use App\Models\Customers;
use App\Models\Machines;
use App\Models\User;
use App\Models\Rollpress;

class RollpressController extends Controller
{

    public function index()
    {
        return view('rollpress.addpressorder');
    }
    


    /**
     * Display a listing of the resource.
     */
    public function presslist(Request $request)
    {
        $Orders = Printers::with('printingprices','ordersImgs')
        ->where('archive', '1')
        ->whereHas('machines', function ($query) {
            $query->where('name', 'sublimation');
        })
        ->get();
        $Rolls = Rollpress::with('order.customers', 'order.ordersImgs')->get();
        $customers = Customers::all();

        return view('rollpress.presslist',
        [
            'Orders'=>$Orders,
            'Rolls' => $Rolls,
            'customers' => $customers,
        ]);
    }

}