<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\Stras;

class StrasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $customers = Customers::all();
        // $machines = Machines::all();

        $orders = Stras::all();
        $Records = Stras::all();
        return view('stras.index',
        [
            'customers' => $customers,
            'Orders' => $orders,
            'Records' => $Records,
        ]);
    }




    public function destroy($id)
    {
        // Implement soft delete if needed
        $stras = Stras::find($id);
        if ($stras) {
            $stras->delete();
            return response()->json(['success' => 'Deleted successfully']);
        }
        return response()->json(['error' => 'Not found'], 404);
    }
}
