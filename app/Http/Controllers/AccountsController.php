<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Printers;
use App\Models\Printingprices;

class AccountsController extends Controller
{
    public function index()
    {
        // Get active orders (not archived) with necessary relationships
        $orders = Printers::with(['customers', 'machines', 'printingprices', 'ordersImgs'])
            // ->where('archive', '0') // Uncomment if we only want active orders
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.accounts.index', compact('orders'));
    }

    public function updatePrice(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:printers,id',
            'field' => 'required|in:pricePerMeter,totalPrice',
            'value' => 'required|numeric|min:0',
        ]);

        $printer = Printers::find($request->order_id);
        
        // Ensure price record exists
        $priceRecord = Printingprices::firstOrCreate(
            ['orderId' => $printer->id],
            [
                'machineId' => $printer->machineId ?? 0,
                'pricePerMeter' => 0,
                'totalPrice' => 0,
                'discount' => 0,
                'finalPrice' => 0
            ]
        );

        $meters = $printer->meters > 0 ? floatval($printer->meters) : 1; 
        $value = floatval($request->value);

        $updatedData = [];

        if ($request->field === 'pricePerMeter') {
            $priceRecord->pricePerMeter = $value;
            // Calculate Total: Price/Meter * Meters
            $priceRecord->totalPrice = $value * $meters;
            $priceRecord->finalPrice = $priceRecord->totalPrice - $priceRecord->discount;
            
            $updatedData['other_field'] = 'totalPrice';
            $updatedData['other_value'] = $priceRecord->totalPrice;

        } elseif ($request->field === 'totalPrice') {
            $priceRecord->totalPrice = $value;
            // Calculate Price/Meter: Total / Meters
            if ($meters > 0) {
                $priceRecord->pricePerMeter = $value / $meters;
            }
            $priceRecord->finalPrice = $priceRecord->totalPrice - $priceRecord->discount;

            $updatedData['other_field'] = 'pricePerMeter';
            $updatedData['other_value'] = $priceRecord->pricePerMeter;
        }

        $priceRecord->save();

        return response()->json([
            'success' => true,
            'pricePerMeter' => $priceRecord->pricePerMeter,
            'totalPrice' => $priceRecord->totalPrice,
            'finalPrice' => $priceRecord->finalPrice,
            'message' => 'تم حفظ البيانات بنجاح',
            'updated_fields' => $updatedData
        ]);
    }
}
