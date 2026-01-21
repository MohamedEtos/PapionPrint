<?php

namespace App\Http\Controllers;

use App\Models\Printerlogs;
use Illuminate\Http\Request;
use App\Models\Printers;
use App\Models\Customers;
use App\Models\Machines;
use App\Models\User;

class PrinterlogsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function printLog()
    {
        $Orders = Printers::with(['printingprices','ordersImgs', 'customers', 'machines', 'user', 'user2'])->orderBy('id', 'desc')->get();
        $customers = Customers::all();
        $machines = Machines::all();

        return view('printers.print_log',
        [
            'Orders'=>$Orders,
            'customers' => $customers,
            'machines' => $machines,
        ]);
    }

}