<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\LaserOrder;
use App\Models\LaserMaterial;
use App\Models\LaserPrice;

class LaserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $customers = Customers::all();
        $materials = LaserMaterial::all();
        $orders = LaserOrder::with(['material', 'customer'])->orderBy('created_at', 'desc')->get();
        // Fetch global settings like operating cost for frontend usage if needed
        $operatingCost = LaserPrice::where('name', 'operating_cost')->value('price') ?? 0;
        $ceylonPrice = LaserPrice::where('name', 'ceylon_price')->value('price') ?? 0;

        return view('laser.index', [
            'customers' => $customers,
            'materials' => $materials,
            'Records' => $orders,
            'operatingCost' => $operatingCost,
            'ceylonPrice' => $ceylonPrice,
        ]);
    }

    public function show($id)
    {
        $order = LaserOrder::with(['material', 'customer'])->findOrFail($id);
        return view('laser.show', compact('order'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customerId' => 'nullable|exists:customers,id',
            'customerName' => 'nullable|string',
            'materialId' => 'nullable|exists:laser_materials,id',
            'height' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'required_pieces' => 'nullable|integer',
            'pieces_per_section' => 'nullable|integer',
            'custom_operating_cost' => 'nullable|numeric|min:0', // New validation
            'image' => 'nullable|image',
            'source' => 'required|in:client,ap_group',
        ]);

        $data = $request->only(['height', 'width', 'pieces_per_section', 'required_pieces', 'notes', 'source', 'custom_operating_cost']);
        $data['add_ceylon'] = $request->has('add_ceylon') ? true : false;

        // Handle customer
        if($request->has('customerId') && $request->customerId){
            $data['customer_id'] = $request->customerId;
        } elseif($request->has('customerName') && $request->customerName) {
            $customer = Customers::where('name', $request->customerName)->first();
            if(!$customer) {
                $customer = Customers::create(['name' => $request->customerName]);
            }
            $data['customer_id'] = $customer->id;
        }

        if($request->has('materialId')){
            $data['material_id'] = $request->materialId;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('laser_images', 'public');
        }

        // Create initial order without costs
        $order = LaserOrder::create($data);

        // Calculate costs using the helper
        $this->recalculateOrderCost($order);

        return response()->json(['success' => 'Created successfully']);
    }

    public function update(Request $request, $id)
    {
        $order = LaserOrder::findOrFail($id);
         
        $request->validate([
             'customerId' => 'nullable|exists:customers,id',
             'customerName' => 'nullable|string',
             'materialId' => 'nullable|exists:laser_materials,id',
             'height' => 'nullable|numeric',
             'width' => 'nullable|numeric',
             'required_pieces' => 'nullable|integer',
             'pieces_per_section' => 'nullable|integer',
             'custom_operating_cost' => 'nullable|numeric|min:0', // New validation
             'image' => 'nullable|image',
             'source' => 'required|in:client,ap_group',
         ]);

        $data = $request->only(['height', 'width', 'pieces_per_section', 'required_pieces', 'notes', 'source', 'custom_operating_cost']);
        $data['add_ceylon'] = $request->has('add_ceylon') ? true : false;
        
        // Handle customer
        if($request->has('customerId') && $request->customerId){
             $data['customer_id'] = $request->customerId;
        } elseif($request->has('customerName') && $request->customerName) {
            $customer = Customers::where('name', $request->customerName)->first();
            if(!$customer) {
                $customer = Customers::create(['name' => $request->customerName]);
            }
            $data['customer_id'] = $customer->id;
        }

        if($request->has('materialId')){
             $data['material_id'] = $request->materialId;
        }
        
        if ($request->hasFile('image')) {
             $data['image_path'] = $request->file('image')->store('laser_images', 'public');
        }
        
        // Update basic data
        $order->update($data);

        // Recalculate costs
        $this->recalculateOrderCost($order);
        
        return response()->json(['success' => 'Updated successfully']);
    }

    public function destroy($id)
    {
        $order = LaserOrder::find($id);
        if ($order) {
            $order->delete();
            return response()->json(['success' => 'Deleted successfully']);
        }
        return response()->json(['error' => 'Not found'], 404);
    }

    public function restart($id)
    {
        $original = LaserOrder::findOrFail($id);
        
        $new = $original->replicate();
        $new->created_at = now();
        $new->updated_at = now();
        $new->save();

        return response()->json(['success' => 'Order restarted (duplicated) successfully']);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['error' => 'No items selected'], 400);
        }

        LaserOrder::whereIn('id', $ids)->delete();

        return response()->json(['success' => 'Selected orders deleted successfully']);
    }

    public function bulkRecalculate(Request $request)
    {
        $ids = $request->ids;
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['error' => 'No items selected'], 400);
        }

        $orders = LaserOrder::whereIn('id', $ids)->get();
        foreach ($orders as $order) {
            $this->recalculateOrderCost($order);
        }

        return response()->json(['success' => "Recalculated costs for " . count($orders) . " orders."]);
    }

    private function recalculateOrderCost(LaserOrder $order)
    {
        // Fetch global settings
        $globalOperatingCost = LaserPrice::where('name', 'operating_cost')->value('price') ?? 0;
        $ceylonPrice = LaserPrice::where('name', 'ceylon_price')->value('price') ?? 0;

        // Use custom operating cost if set, otherwise global
        $operatingCostPerPiece = $order->custom_operating_cost !== null 
            ? $order->custom_operating_cost 
            : $globalOperatingCost;

        $materialPrice = 0;
        if ($order->source === 'ap_group') {
            // Load material if not loaded or force refresh to get latest price
             $order->load('material');
             if ($order->material) {
                 $materialPrice = $order->material->price;
             }
        }

        $lengthMeters = ($order->height ?? 0) / 100;
        $piecesPerSection = $order->pieces_per_section ?? 1;
        if ($piecesPerSection < 1) $piecesPerSection = 1;

        $requiredPieces = $order->required_pieces ?? 0;
        $sectionCount = ceil($requiredPieces / $piecesPerSection);
        
        // Recalculate Piece Cost
        $materialCostPerPiece = 0;
        if ($order->source === 'ap_group') {
            $sectionMaterialCost = $lengthMeters * $materialPrice;
            if ($order->add_ceylon) {
                $sectionMaterialCost += ($lengthMeters * $ceylonPrice);
            }
            $materialCostPerPiece = $sectionMaterialCost / $piecesPerSection;
        }

        $pieceCost = $materialCostPerPiece + $operatingCostPerPiece;
        
        // Recalculate Section Cost
        $sectionTotalCost = ($order->source === 'ap_group' ? 
            ($lengthMeters * $materialPrice + ($order->add_ceylon ? ($lengthMeters * $ceylonPrice) : 0)) : 0) 
            + ($operatingCostPerPiece * $piecesPerSection);

        $order->section_count = $sectionCount;
        $order->manufacturing_cost = $pieceCost;
        $order->total_cost = $sectionTotalCost * $sectionCount;
        $order->save();
    }

    public function trash()
    {
        $orders = LaserOrder::onlyTrashed()->with(['material', 'customer'])->orderBy('deleted_at', 'desc')->get();
        
        return view('laser.trash', [
            'Records' => $orders
        ]);
    }

    public function restore($id)
    {
        $order = LaserOrder::onlyTrashed()->find($id);
        if ($order) {
            $order->restore();
            return response()->json(['success' => 'Restored successfully']);
        }
        return response()->json(['error' => 'Not found'], 404);
    }

    public function forceDelete($id)
    {
         $order = LaserOrder::onlyTrashed()->find($id);
         if ($order) {
             $order->forceDelete();
             return response()->json(['success' => 'Permanently deleted successfully']);
         }
         return response()->json(['error' => 'Not found'], 404);
    }

    public function pricing()
    {
        $materials = LaserMaterial::all();
        $globalPrices = LaserPrice::all();

        return view('laser.pricing', compact('materials', 'globalPrices'));
    }

    public function updatePrice(Request $request)
    {
        // Handle both Material Prices and Global Prices updates
        $type = $request->type; // 'material' or 'global'
        $id = $request->id;
        $price = $request->price;
        $name = $request->name; 

        if ($type == 'material') {
            $material = LaserMaterial::find($id);
            if($material) {
                $material->price = $price;
                if($request->name) $material->name = $request->name;
                $material->save();
                
                // Trigger Recalculation for related orders
                $orders = LaserOrder::where('material_id', $id)->where('source', 'ap_group')->get();
                foreach($orders as $order) {
                    $this->recalculateOrderCost($order);
                }

                return response()->json(['success' => 'Material price updated and related orders recalculated']);
            }
             
             if(!$id && $request->name) {
                 LaserMaterial::create(['name' => $request->name, 'price' => $price]);
                 return response()->json(['success' => 'Material created']);
             }
        } elseif ($type == 'global') {
             $global = LaserPrice::find($id);
             if ($global) {
                 $global->price = $price;
                 $global->save();

                 // Trigger Recalculation for ALL orders (since global cost affects all)
                 // Note: Ideally queue this for later if many orders.
                 // For now, doing it inline.
                 
                // Only ap_group orders might use ceylon/material. 
                // However, operating_cost affects ALL orders.
                // Re-calculating all orders.
                $orders = LaserOrder::all();
                foreach($orders as $order) {
                    $this->recalculateOrderCost($order);
                }

                 return response()->json(['success' => 'Global price updated and all orders recalculated']);
             }
             
             // Create if not exists (e.g. operating_cost being set for first time)
             if (!$id && $request->name) {
                 LaserPrice::create(['name' => $request->name, 'price' => $price]);
                 return response()->json(['success' => 'Global price created']);
             }
        }

        return response()->json(['error' => 'Update failed'], 400);
    }
}
