<?php

namespace App\Http\Controllers;

use App\Models\Machines;
use Illuminate\Http\Request;

class MachinesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:الفواتير');
    }
    public function pricing()
    {
        $machines = Machines::all();
        return view('machines.pricing', compact('machines'));
    }

    public function updatePrice(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:machines,id',
            'field' => 'required|in:price_1_pass,price_4_pass,price_6_pass',
            'value' => 'required|numeric|min:0',
        ]);

        $machine = Machines::find($request->id);
        $machine->update([
            $request->field => $request->value
        ]);

        return response()->json(['success' => 'updated successfully']);
    }
}
