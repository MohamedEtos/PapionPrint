<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\Stras;
use Illuminate\Support\Facades\DB;

class StrasController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:الاستراس');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $customers = Customers::all();
        $prices = \App\Models\StrasPrice::all();
        $query = Stras::with(['layers', 'customer'])->orderBy('created_at', 'desc');

        if (auth()->user()->can('الاستراس') && !auth()->user()->can('الفواتير')) {
            $query->take(10);
        }

        $orders = $query->get();
        return view('stras.index',
        [
            'customers' => $customers,
            'prices' => $prices,
            'Records' => $orders,
        ]);
    }

    public function show($id)
    {
        $stras = Stras::with(['layers', 'customer'])->findOrFail($id);
        return view('stras.show', compact('stras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customerId' => 'nullable|exists:customers,id',
            'height' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'cards_count' => 'nullable|integer',
            'pieces_per_card' => 'nullable|integer',
            'layers' => 'required|array',
            'layers.*.size' => 'required|string',
            'layers.*.count' => 'required|numeric',
            'image' => 'nullable|image',
        ]);

        $data = $request->only(['height', 'width', 'cards_count', 'pieces_per_card', 'notes']);

        if($request->has('customerId')){
            $data['customerId'] = $request->customerId;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('stras_images', 'public');
        }

        DB::transaction(function () use ($request, $data) {
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
        });

        return response()->json(['success' => 'Created successfully']);
    }

    public function update(Request $request, $id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:stras,id',
        ])->validate();
         // Placeholder for update logic if needed heavily
         // For now, focus on create as per request scenario "Add"
         // But I should implementing update as well.
         
         $stras = Stras::findOrFail($id);
         
          $request->validate([
            'customerId' => 'nullable|exists:customers,id',
            'height' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'cards_count' => 'nullable|integer',
            'pieces_per_card' => 'nullable|integer',
            'layers' => 'nullable|array',
            'image' => 'nullable|image',
        ]);

        $data = $request->only(['height', 'width', 'cards_count', 'pieces_per_card', 'notes']);
        
        if($request->has('customerId')){
             $data['customerId'] = $request->customerId;
        }
        
        if ($request->hasFile('image')) {
             $data['image_path'] = $request->file('image')->store('stras_images', 'public');
        }

        DB::transaction(function () use ($request, $stras, $data) {
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
        });
        
        return response()->json(['success' => 'Updated successfully']);
    }




    public function destroy($id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:stras,id',
        ])->validate();
        $stras = Stras::find($id);
        if ($stras) {
            $stras->layers()->delete(); // Soft delete due to trait
            $stras->delete(); // Soft delete due to trait
            return response()->json(['success' => 'Deleted successfully test']);
        }
        return response()->json(['error' => 'Not found'], 404);
    }

    public function restart($id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:stras,id',
        ])->validate();
        return DB::transaction(function () use ($id) {
            $original = Stras::with('layers')->findOrFail($id);
            
            $new = $original->replicate();
            $new->created_at = now();
            $new->updated_at = now();
            $new->save();

            foreach ($original->layers as $layer) {
                $newLayer = $layer->replicate();
                $newLayer->stras_id = $new->id;
                $newLayer->save();
            }

            return response()->json(['success' => 'Order restarted (duplicated) successfully']);
        });
    }
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:stras,id',
        ]);
        $ids = $request->ids;
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['error' => 'No items selected'], 400);
        }

        $orders = Stras::whereIn('id', $ids)->get();
        
        DB::transaction(function () use ($orders) {
            foreach ($orders as $order) {
                $order->layers()->delete();
                $order->delete();
            }
        });

        return response()->json(['success' => 'Selected orders deleted successfully']);
    }

    public function trash()
    {
        $orders = Stras::onlyTrashed()->with(['layers' => function($query) {
             $query->withTrashed();
        }, 'customer'])->orderBy('deleted_at', 'desc')->get();
        
        return view('stras.trash', [
            'Records' => $orders
        ]);
    }

    public function restore($id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:stras,id',
        ])->validate();
        $stras = Stras::onlyTrashed()->find($id);
        if ($stras) {
            $stras->restore();
            $stras->layers()->restore(); // Restore related layers
            return response()->json(['success' => 'Restored successfully']);
        }
        return response()->json(['error' => 'Not found'], 404);
    }

    public function forceDelete($id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:stras,id',
        ])->validate();
         $stras = Stras::onlyTrashed()->find($id);
         if ($stras) {
             $stras->layers()->forceDelete(); // Permanently delete layers
             $stras->forceDelete();
             return response()->json(['success' => 'Permanently deleted successfully']);
         }
         return response()->json(['error' => 'Not found'], 404);
    }

    public function pricing()
    {
        $strasPrices = \App\Models\StrasPrice::where('type', 'stras')->get();
        $paperPrices = \App\Models\StrasPrice::where('type', 'paper')->get();
        $otherPrices = \App\Models\StrasPrice::where('type', 'global')->get();

        return view('stras.pricing', compact('strasPrices', 'paperPrices', 'otherPrices'));
    }

    public function updatePrice(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:stras_prices,id',
            'price' => 'required|numeric',
        ]);
        $id = $request->id;
        $price = $request->price;
        
        $record = \App\Models\StrasPrice::find($id);
        if ($record) {
            $record->price = $price;
            $record->save();
             return response()->json(['success' => 'Price updated successfully']);
        }
         return response()->json(['error' => 'Not found'], 404);

    }
}
