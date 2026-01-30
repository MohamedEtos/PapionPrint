<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
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

        if ($stock) {
            $stock->decrement('quantity', 1);
            return response()->json(['success' => 'تم خصم 1 لتر بنجاح', 'new_quantity' => $stock->quantity]);
        }
        
        // Optional: Auto-create if not exists (or return error)
        // For now, let's create it with -1 to track deficit or 0 if we assume stock exists.
        // Let's create with initial 0 then decrement to -1
        $stock = \App\Models\Stock::create([
            'type' => 'ink',
            'machine_type' => $request->machine_type,
            'color' => $request->color,
            'quantity' => -1,
            'unit' => 'liter'
        ]);

        return response()->json(['success' => 'تم خصم 1 لتر (تم إنشاء سجل جديد)', 'new_quantity' => -1]);
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
            'color' => 'nullable|string',
            'quantity' => 'required|numeric',
            'unit' => 'required|string',
            'operation' => 'required|in:add,set' // Add to existing or Set absolute value
        ]);

        $stock = \App\Models\Stock::firstOrNew([
            'type' => $request->type,
            'machine_type' => $request->machine_type,
            'color' => $request->color,
        ]);

        if (!$stock->exists) {
             $stock->unit = $request->unit;
             $stock->quantity = 0;
        }

        if ($request->operation == 'add') {
            $stock->quantity += $request->quantity;
        } else {
            $stock->quantity = $request->quantity;
        }
        
        $stock->save();

        return response()->json(['success' => 'تم تحديث المخزون بنجاح', 'stock' => $stock]);
    }
}
