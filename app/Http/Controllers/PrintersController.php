<?php

namespace App\Http\Controllers;

use App\Models\Printers;
use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\Machines;
use App\Models\User;

class PrintersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Orders = Printers::with('printingprices')->get();

        return view('printers.AddPrintOrders', 
        [
            'Orders'=>$Orders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Printers $printers)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Printers $printers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Printers $printers)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Printers $printers)
    {
        //
    }
}
