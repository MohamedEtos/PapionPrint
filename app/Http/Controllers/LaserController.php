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
        return response()->json($order);
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
            'image' => 'nullable|image',
            'source' => 'required|in:client,ap_group',
        ]);

        $data = $request->only(['height', 'width', 'pieces_per_section', 'required_pieces', 'notes', 'source']);
        $data['add_ceylon'] = $request->has('add_ceylon') ? true : false;

        // Handle customer - find by ID or name, or create new
        if($request->has('customerId') && $request->customerId){
            $data['customer_id'] = $request->customerId;
        } elseif($request->has('customerName') && $request->customerName) {
            $customer = Customers::where('name', $request->customerName)->first();
            if(!$customer) {
                // Create new customer
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

        // --- CALCULATION LOGIC ---
        $operatingCostPerPiece = LaserPrice::where('name', 'operating_cost')->value('price') ?? 0;
        $ceylonPrice = LaserPrice::where('name', 'ceylon_price')->value('price') ?? 0;
        
        $materialPrice = 0;
        if ($data['source'] === 'ap_group' && isset($data['material_id'])) {
             $material = LaserMaterial::find($data['material_id']);
             if($material) {
                 $materialPrice = $material->price;
             }
        }

        $lengthMeters = ($data['height'] ?? 0) / 100;
        $piecesPerSection = $data['pieces_per_section'] ?? 1;
        if($piecesPerSection < 1) $piecesPerSection = 1;

        $requiredPieces = $data['required_pieces'] ?? 0;
        
        // Calculate Section Count
        $sectionCount = ceil($requiredPieces / $piecesPerSection);
        $data['section_count'] = $sectionCount;

        // Calculate Piece Cost
        // Material cost per piece = (Length × Material Price) / Pieces in Section
        $materialCostPerPiece = 0;
        if ($data['source'] === 'ap_group') {
             $sectionMaterialCost = $lengthMeters * $materialPrice;
             if ($data['add_ceylon']) {
                 $sectionMaterialCost += ($lengthMeters * $ceylonPrice);
             }
             $materialCostPerPiece = $sectionMaterialCost / $piecesPerSection;
        }

        // Operating cost is flat per piece
        $pieceCost = $materialCostPerPiece + $operatingCostPerPiece;
        
        // Section cost = Material cost + (Operating cost × pieces in section)
        $sectionTotalCost = ($data['source'] === 'ap_group' ? ($lengthMeters * $materialPrice + ($data['add_ceylon'] ? $lengthMeters * $ceylonPrice : 0)) : 0) + ($operatingCostPerPiece * $piecesPerSection);

        $data['manufacturing_cost'] = $pieceCost;
        $data['total_cost'] = $sectionTotalCost * $sectionCount;

        LaserOrder::create($data);

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
             'image' => 'nullable|image',
             'source' => 'required|in:client,ap_group',
         ]);

        $data = $request->only(['height', 'width', 'pieces_per_section', 'required_pieces', 'notes', 'source']);
        $data['add_ceylon'] = $request->has('add_ceylon') ? true : false;
        
        // Handle customer - find by ID or name, or create new
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

        // --- CALCULATION LOGIC (Same as store) ---
        $operatingCostPerPiece = LaserPrice::where('name', 'operating_cost')->value('price') ?? 0;
        $ceylonPrice = LaserPrice::where('name', 'ceylon_price')->value('price') ?? 0;
        
        $materialPrice = 0;
        if ($data['source'] === 'ap_group' && isset($data['material_id'])) {
             $material = LaserMaterial::find($data['material_id']);
             if($material) {
                 $materialPrice = $material->price;
             }
        }

        $lengthMeters = ($data['height'] ?? 0) / 100;
        $piecesPerSection = $data['pieces_per_section'] ?? 1;
        if($piecesPerSection < 1) $piecesPerSection = 1;

        $requiredPieces = $data['required_pieces'] ?? 0;
        
        $sectionCount = ceil($requiredPieces / $piecesPerSection);
        $data['section_count'] = $sectionCount;

        // Calculate Piece Cost
        $materialCostPerPiece = 0;
        if ($data['source'] === 'ap_group') {
             $sectionMaterialCost = $lengthMeters * $materialPrice;
             if ($data['add_ceylon']) {
                 $sectionMaterialCost += ($lengthMeters * $ceylonPrice);
             }
             $materialCostPerPiece = $sectionMaterialCost / $piecesPerSection;
        }

        // Operating cost is flat per piece
        $pieceCost = $materialCostPerPiece + $operatingCostPerPiece;
        
        // Section cost = Material cost + (Operating cost × pieces in section)
        $sectionTotalCost = ($data['source'] === 'ap_group' ? ($lengthMeters * $materialPrice + ($data['add_ceylon'] ? $lengthMeters * $ceylonPrice : 0)) : 0) + ($operatingCostPerPiece * $piecesPerSection);

        $data['manufacturing_cost'] = $pieceCost;
        $data['total_cost'] = $sectionTotalCost * $sectionCount;


        $order->update($data);
        
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
                return response()->json(['success' => 'Material price updated']);
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
                 return response()->json(['success' => 'Global price updated']);
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
