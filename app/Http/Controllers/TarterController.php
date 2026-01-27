<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\Tarter;
use App\Models\TarterLayer;
use App\Models\TarterPrice;

class TarterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $customers = Customers::all();
        $prices = TarterPrice::all();
        $orders = Tarter::with(['layers', 'customer'])->orderBy('created_at', 'desc')->get();
        return view('tarter.index',
        [
            'customers' => $customers,
            'prices' => $prices,
            'Records' => $orders,
        ]);
    }

    public function show($id)
    {
        $tarter = Tarter::with(['layers', 'customer'])->findOrFail($id);
        return response()->json($tarter);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customerId' => 'nullable|exists:customers,id',
            'height' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'cards_count' => 'nullable|integer',
            'pieces_per_card' => 'nullable|integer',
            'machine_time' => 'nullable|integer',
            'layers' => 'required|array',
            'layers.*.size' => 'required|string',
            'layers.*.count' => 'required|numeric',
            'image' => 'nullable|image',
        ]);

        $data = $request->only(['height', 'width', 'cards_count', 'pieces_per_card', 'machine_time', 'notes']);
        if($request->has('customerId')){
            $data['customer_id'] = $request->customerId;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('tarter_images', 'public');
        }

        $tarter = Tarter::create($data);

        foreach ($request->layers as $layer) {
            $tarter->layers()->create([
                'size' => $layer['size'],
                'count' => $layer['count'],
            ]);
        }

        return response()->json(['success' => 'Created successfully']);
    }

    public function update(Request $request, $id)
    {
         $tarter = Tarter::findOrFail($id);
         
          $request->validate([
            'customerId' => 'nullable|exists:customers,id',
            'height' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'cards_count' => 'nullable|integer',
            'pieces_per_card' => 'nullable|integer',
            'machine_time' => 'nullable|integer',
            'layers' => 'nullable|array',
            'image' => 'nullable|image',
        ]);

        $data = $request->only(['height', 'width', 'cards_count', 'pieces_per_card', 'machine_time', 'notes']);

        if($request->has('customerId')){
            $data['customer_id'] = $request->customerId;
        }
        
        if ($request->hasFile('image')) {
             $data['image_path'] = $request->file('image')->store('tarter_images', 'public');
        }

        $tarter->update($data);

        if($request->has('layers')){
             $tarter->layers()->delete(); // Simple re-create for layers
             foreach ($request->layers as $layer) {
                $tarter->layers()->create([
                    'size' => $layer['size'],
                    'count' => $layer['count'],
                ]);
            }
        }
        
        return response()->json(['success' => 'Updated successfully']);
    }

    public function destroy($id)
    {
        $tarter = Tarter::find($id);
        if ($tarter) {
            $tarter->layers()->delete(); // Soft delete due to trait
            $tarter->delete(); // Soft delete due to trait
            return response()->json(['success' => 'Deleted successfully']);
        }
        return response()->json(['error' => 'Not found'], 404);
    }

    public function restart($id)
    {
        $original = Tarter::with('layers')->findOrFail($id);
        
        $new = $original->replicate();
        $new->created_at = now();
        $new->updated_at = now();
        $new->save();

        foreach ($original->layers as $layer) {
            $newLayer = $layer->replicate();
            $newLayer->tarter_id = $new->id;
            $newLayer->save();
        }

        return response()->json(['success' => 'Order restarted (duplicated) successfully']);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['error' => 'No items selected'], 400);
        }

        $orders = Tarter::whereIn('id', $ids)->get();
        
        foreach ($orders as $order) {
            $order->layers()->delete();
            $order->delete();
        }

        return response()->json(['success' => 'Selected orders deleted successfully']);
    }

    public function trash()
    {
        $orders = Tarter::onlyTrashed()->with(['layers' => function($query) {
             $query->withTrashed();
        }, 'customer'])->orderBy('deleted_at', 'desc')->get();
        
        return view('tarter.trash', [
            'Records' => $orders
        ]);
    }

    public function restore($id)
    {
        $tarter = Tarter::onlyTrashed()->find($id);
        if ($tarter) {
            $tarter->restore();
            $tarter->layers()->restore(); // Restore related layers
            return response()->json(['success' => 'Restored successfully']);
        }
        return response()->json(['error' => 'Not found'], 404);
    }

    public function forceDelete($id)
    {
         $tarter = Tarter::onlyTrashed()->find($id);
         if ($tarter) {
             $tarter->layers()->forceDelete(); // Permanently delete layers
             $tarter->forceDelete();
             return response()->json(['success' => 'Permanently deleted successfully']);
         }
         return response()->json(['error' => 'Not found'], 404);
    }

    public function pricing()
    {
        $needlePrices = TarterPrice::where('type', 'needle')->get();
        $paperPrices = TarterPrice::where('type', 'paper')->get();
        $otherPrices = TarterPrice::where('type', 'global')->orWhere('type', 'machine_time_cost')->get();

        return view('tarter.pricing', compact('needlePrices', 'paperPrices', 'otherPrices'));
    }

    public function updatePrice(Request $request)
    {
        $id = $request->id;
        $price = $request->price;
        
        $record = TarterPrice::find($id);
        if ($record) {
            $record->price = $price;
            $record->save();
             return response()->json(['success' => 'Price updated successfully']);
        }
         return response()->json(['error' => 'Not found'], 404);
    }
}
