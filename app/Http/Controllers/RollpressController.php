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
        $Orders = Printers::with('printingprices','ordersImgs','rollpress')
        ->where('archive', '1')
        ->whereHas('machines', function ($query) {
            $query->where('name', 'sublimation');
        })
        ->get();
        $Rolls = Rollpress::with('customer', 'order.customers', 'order.ordersImgs')->get();
        $customers = Customers::all();
        $machines = Machines::all();

        return view('rollpress.presslist',
        [
            'Orders'=>$Orders,
            'Rolls' => $Rolls,
            'customers' => $customers,
            'machines' => $machines,
        ]);
    }

    public function store(Request $request)
    {
        // 1. Handle Customer
        $customerId = $request->input('customerId');
        $customerName = $request->input('customerName');

        if (!$customerId && $customerName) {
            // Check if exists by name to avoid duplicates if ID missing
            $existingCustomer = Customers::where('name', $customerName)->first();
            if ($existingCustomer) {
                $customerId = $existingCustomer->id;
            } else {
                $newCustomer = Customers::create(['name' => $customerName]);
                $customerId = $newCustomer->id;
            }
        }

  

        // 3. Create Rollpress Record
        $rollpress = new Rollpress();
        if ($request->filled('orderId')) {
             $rollpress->orderId = $request->input('orderId');
        }
        $rollpress->customerId = $customerId; // New Link
        $rollpress->fabrictype = $request->input('fabrictype');
        $rollpress->fabricsrc = $request->input('fabricsrc');
        $rollpress->fabriccode = $request->input('fabriccode');
        $rollpress->fabricwidth = $request->input('fabricwidth');
        $rollpress->meters = $request->input('meters');
        $rollpress->status = $request->input('status') == 'تم الانتهاء' ? 1 : 0; 
        $rollpress->paymentstatus = $request->input('paymentstatus') == '1' ? 1 : 0;
        $rollpress->papyershild = $request->input('papyershild');
        $rollpress->price = $request->input('price');
        $rollpress->notes = $request->input('notes');
        $rollpress->save();

        // 4. Handle Image Upload - TEMPORARILY DISABLED (Requires Schema Update for Rollpress Images)
        /*
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('orders-images', 'public');
            
            // Assuming OrdersImg model
            $imageStart = new \App\Models\OrdersImg();
            $imageStart->orderId = $printer->id; // Printer no longer exists here
            $imageStart->path = $path;
            $imageStart->type = 'start'; 
            $imageStart->save();
        }
        */

        return response()->json(['success' => true, 'rollpress' => $rollpress]);
    }

}