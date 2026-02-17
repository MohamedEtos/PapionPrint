<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\Mail\GeneralMail;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:المخزن');
    }
    //
    public function consumeInk(Request $request)
    {
        $request->validate([
            'machine_type' => 'required|string',
            'color' => 'required|string',
        ]);

        $stock = \App\Models\Stock::where('type', 'ink')
            ->where('machine_type', $request->machine_type)
            ->where('color', $request->color)
            ->first();

        if ($stock && $stock->quantity >= 1) {
            $stock->decrement('quantity', 1);
            
            // Log the consumption
            \App\Models\InventoryLog::create([
                'type' => 'ink',
                'machine_type' => $request->machine_type,
                'color' => $request->color,
                'quantity' => 1,
            ]);

            if ($stock->quantity <= 1) {
                $this->sendLowStockAlert("Ink ({$request->machine_type} - {$request->color})", $stock->quantity);
            }

            return response()->json(['success' => 'تم خصم 1 لتر بنجاح', 'new_quantity' => $stock->quantity]);
        }
        
        return response()->json(['error' => 'عفوا، الرصيد غير كافي!'], 400);
    }

    public function index()
    {
        // Grouping logic can be handled in frontend or passed as specific collections
        $stocks = \App\Models\Stock::all();
        $paperStocks = $stocks->where('type', 'paper');
        $inkStocks = $stocks->where('type', 'ink');
        
        return view('inventory.index', compact('stocks', 'paperStocks', 'inkStocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:paper,ink',
            'machine_type' => 'required',
            'operation' => 'required|in:add,set',
            // Quantity is required only if we are not submitting colors array
            'quantity' => 'required_without:colors|numeric',
            'colors' => 'nullable|array',
            'unit' => 'required|string',
        ]);

        if ($request->has('colors') && is_array($request->colors)) {
            // Mass update for ink colors
            foreach ($request->colors as $color => $qty) {
                if ($qty === null || $qty === '') continue; // Skip empty inputs

                $this->updateStock(
                    $request->type,
                    $request->machine_type,
                    $color,
                    (float)$qty,
                    $request->unit,
                    $request->operation
                );
            }
        } else {
            // Single update (Paper or single ink if flow was different)
            $this->updateStock(
                $request->type,
                $request->machine_type,
                $request->color,
                (float)$request->quantity,
                $request->unit,
                $request->operation
            );
        }

        return response()->json(['success' => 'تم تحديث المخزون بنجاح']);
    }

    private function updateStock($type, $machineType, $color, $quantity, $unit, $operation)
    {
        $stock = \App\Models\Stock::firstOrNew([
            'type' => $type,
            'machine_type' => $machineType,
            'color' => $color,
        ]);

        if (!$stock->exists) {
             $stock->unit = $unit;
             $stock->quantity = 0;
        }

        if ($operation == 'add') {
            $stock->quantity += $quantity;
        } else {
            $stock->quantity = $quantity;
        }
        
        $stock->save();

        if ($type == 'paper' && $stock->quantity < 100) {
            $this->sendLowStockAlert("Paper ({$machineType} - {$color})", $stock->quantity);
        }

        return $stock;
    }

    private function sendLowStockAlert($itemName, $quantity)
    {
        $setting = Setting::first();
        if (!$setting || empty($setting->inventory_alert_emails)) {
            return;
        }

        $emails = array_filter(array_map('trim', explode(',', $setting->inventory_alert_emails)));
        
        if (empty($emails)) {
            return;
        }

        $subject = "Low Stock Alert: $itemName";
        $body = "Warning: The stock level for **$itemName** is low.\n\nCurrent Quantity: **$quantity**\n\nPlease replenish the stock as soon as possible.";

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new GeneralMail($subject, $body));
            } catch (\Exception $e) {
                // Log error or ignore to prevent blocking the request
                \Illuminate\Support\Facades\Log::error("Failed to send inventory alert to $email: " . $e->getMessage());
            }
        }
    }
}
