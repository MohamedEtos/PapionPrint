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
        $Orders = Printers::with(['printingprices','ordersImgs', 'customers', 'machines', 'user', 'user2'])->where('archive', 1)->orderBy('id', 'desc')->get();
        $customers = Customers::all();
        $machines = Machines::all();

        return view('printers.print_log',
        [
            'Orders'=>$Orders,
            'customers' => $customers,
            'machines' => $machines,
        ]);
    }

    public function duplicate($id)
    {
        $order = Printers::with('ordersImgs')->find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $newOrder = $order->replicate();
        $newOrder->archive = 0;
        $newOrder->orderNumber = 'ORD-' . time() . '-' . rand(10, 99);
        $newOrder->status = 'بانتظار اجراء';
        $newOrder->timeEndOpration = null;
        $newOrder->save();

        // Replicate images
        foreach ($order->ordersImgs as $img) {
            $newImg = $img->replicate();
            $newImg->orderId = $newOrder->id;
            $newImg->save();
        }

        return response()->json(['success' => 'Order duplicated successfully']);
    }

}