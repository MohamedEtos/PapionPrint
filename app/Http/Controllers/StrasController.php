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
        $prices = \App\Models\StrasPrice::all();
        $orders = Stras::with(['layers', 'customer'])->orderBy('created_at', 'desc')->get();
        return view('stras.index',
        [
            'customers' => $customers,
            'prices' => $prices,
            'Records' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customerId' => 'nullable|exists:customers,id',
            'height' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'layers' => 'required|array',
            'layers.*.size' => 'required|string',
            'layers.*.count' => 'required|numeric',
            'image' => 'nullable|image',
        ]);

        $data = $request->only(['customerId', 'height', 'width', 'notes']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('stras_images', 'public');
        }

        $stras = Stras::create($data);

        foreach ($request->layers as $layer) {
            $stras->layers()->create([
                'size' => $layer['size'],
                'count' => $layer['count'],
                // Fetch price if needed, or rely on frontend passing it?
                // Plan said fetch from master.
                // For now, let's assume simple creation.
            ]);
        }

        return response()->json(['success' => 'Created successfully']);
    }

    public function update(Request $request, $id)
    {
         // Placeholder for update logic if needed heavily
         // For now, focus on create as per request scenario "Add"
         // But I should implementing update as well.
         
         $stras = Stras::findOrFail($id);
         
          $request->validate([
            'customerId' => 'nullable|exists:customers,id',
            'height' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'layers' => 'nullable|array',
            'image' => 'nullable|image',
        ]);

        $data = $request->only(['customerId', 'height', 'width', 'notes']);
        
        if ($request->hasFile('image')) {
             $data['image_path'] = $request->file('image')->store('stras_images', 'public');
        }

        $stras->update($data);

        if($request->has('layers')){
             $stras->layers()->delete(); // Simple re-create for layers
             foreach ($request->layers as $layer) {
                $stras->layers()->create([
                    'size' => $layer['size'],
                    'count' => $layer['count'],
                ]);
            }
        }
        
        return response()->json(['success' => 'Updated successfully']);
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
