<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Stras;
use App\Models\Tarter;
use App\Models\Printers;
use App\Models\LaserOrder;
use App\Models\InvoiceArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:العملاء');
    }


    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $customers = Customers::where('name', 'LIKE', "%{$query}%")
                            ->orWhere('phone', 'LIKE', "%{$query}%")
                            ->limit(10)
                            ->get(['id', 'name', 'phone']);

        return response()->json($customers);
    }

    public function index()
    {
        $customers = Customers::withCount(['stras', 'tarters', 'lasers', 'printers'])->get();
        return view('customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = Customers::with(['printers', 'stras', 'tarters', 'lasers'])->findOrFail($id);

        // Calculate Totals
        
        // Invoices Statistics
        // We use Invoice model for total counts of invoices
        $invoices = \App\Models\Invoice::where('customer_id', $id)
                                ->where('status', 'sent')
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        $stats = [
            'printer_orders' => $customer->printers()->count(),
            'stras_orders' => $customer->stras()->count(),
            'tarter_orders' => $customer->tarters()->count(),
            'laser_orders' => $customer->lasers()->count(),
            'total_spent' => 0,
            'total_orders' => $invoices->count()
        ];

        // Ensure total_amount is set in Invoice, otherwise sum items
        if ($invoices->count() > 0) {
             $stats['total_spent'] = $invoices->sum('total_amount');
        }

        // Prepare Recent Orders List (Unified)
        $recentOrders = collect();
        
        // Add Stras
        foreach($customer->stras()->latest()->get() as $order) {
            $recentOrders->push([
                'type' => 'Stras',
                'id' => $order->id,
                'date' => $order->created_at,
                'details' => $order->layers->count() . ' Layers',
                'status' => 'Completed',
                'link' => route('stras.show', $order->id)
            ]);
        }
        // Add Tarter
        foreach($customer->tarters()->latest()->get() as $order) {
            $recentOrders->push([
                'type' => 'Tarter',
                'id' => $order->id,
                'date' => $order->created_at,
                'details' => $order->layers->count() . ' Needles',
                'status' => 'Completed',
                'link' => route('tarter.show', $order->id)
            ]);
        }
        // Add Laser
        foreach($customer->lasers()->latest()->get() as $order) {
             $recentOrders->push([
                'type' => 'Laser',
                'id' => $order->id,
                'date' => $order->created_at,
                'details' => $order->material->name ?? '-',
                'status' => 'Completed',
                'link' => route('laser.show', $order->id)
            ]);
        }
        // Add Printers
         foreach($customer->printers()->latest()->get() as $order) {
             $recentOrders->push([
                'type' => 'Printer',
                'id' => $order->id,
                'date' => $order->created_at,
                'details' => $order->machines->name ?? '-',
                'status' => 'Completed', 
                'link' => route('printers.show', $order->id)
            ]);
        }

        $recentOrders = $recentOrders->sortByDesc('date')->take(10);
        
        // For the invoice list table, we pass the invoice collection
        // View expects groups of items, but now we pass Invoice objects.
        // We need to adjust view or pass compatible structure.
        // Let's pass $invoices which are Invoice models.
        // View needs adjustment to iterate Invoice models instead of Archive groups.

        return view('customers.profile', compact('customer', 'stats', 'recentOrders', 'invoices'));
    }
}
