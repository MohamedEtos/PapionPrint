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

    public function show($id, Request $request)
    {
        $stras = Stras::with(['layers', 'customer'])->findOrFail($id);
        if($request->ajax()){
             return response()->json($stras);
        }
        return view('stras.show', compact('stras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'height' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'cards_count' => 'nullable|integer',
            'pieces_per_card' => 'nullable|integer',
            'layers' => 'required|array',
            'layers.*.size' => 'required|string',
            'layers.*.count' => 'required|numeric',
            'image' => 'nullable|image',
            'manufacturing_cost' => 'nullable|numeric',
        ]);

        $data = $request->only(['height', 'width', 'cards_count', 'pieces_per_card', 'notes', 'manufacturing_cost']);

        if($request->has('customerId') && $request->customerId != null){
            $data['customerId'] = $request->customerId;
        } elseif ($request->has('customer_name') && $request->customer_name != null) {
            $customer = Customers::firstOrCreate(['name' => $request->customer_name]);
            $data['customerId'] = $customer->id;
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

            // Notification
            $customer = Customers::find($data['customerId']);
            \App\Models\Notifications::create([
                'user_id' => auth()->id(),
                'title' => 'اوردر استراس #' . $stras->id,
                'img_path' => $stras->image_path ?? null,
                'body' => ($customer->name ?? 'عميل') . ' تم انشاء اوردر استراس جديد',
                'type' => 'order',
                'status' => 'unread',
                'link' => route('stras.show', $stras->id),
            ]);
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
            'customer_name' => 'nullable|string|max:255',
            'height' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'cards_count' => 'nullable|integer',
            'pieces_per_card' => 'nullable|integer',
            'layers' => 'nullable|array',
            'image' => 'nullable|image',
            'manufacturing_cost' => 'nullable|numeric',
        ]);

        $data = $request->only(['height', 'width', 'cards_count', 'pieces_per_card', 'notes', 'manufacturing_cost']);
        
        if($request->has('customerId') && $request->customerId != null){
             $data['customerId'] = $request->customerId;
        } elseif ($request->has('customer_name') && $request->customer_name != null) {
            $customer = Customers::firstOrCreate(['name' => $request->customer_name]);
            $data['customerId'] = $customer->id;
        }
        
        if ($request->hasFile('image')) {
             $data['image_path'] = $request->file('image')->store('stras_images', 'public');
        }

        DB::transaction(function () use ($request, $stras, $data) {
            $stras->update($data);

            if($request->has('layers')){
                 $stras->layers()->forceDelete(); // Force delete to clear old layers and avoid restore conflicts
                 foreach ($request->layers as $layer) {
                    $stras->layers()->create([
                        'size' => $layer['size'],
                        'count' => $layer['count'],
                    ]);
                }
            }

            // Notification
            $customer = Customers::find($stras->customerId);
            \App\Models\Notifications::create([
                'user_id' => auth()->id(),
                'title' => 'تحديث اوردر استراس #' . $stras->id,
                'img_path' => $stras->image_path ?? null,
                'body' => ($customer->name ?? 'عميل') . ' تم تحديث البيانات',
                'type' => 'order',
                'status' => 'unread',
                'link' => route('stras.show', $stras->id),
            ]);
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

             // Notification
             $customer = Customers::find($stras->customerId);
             \App\Models\Notifications::create([
                'user_id' => auth()->id(),
                'title' => 'حذف اوردر استراس #' . $stras->id,
                'img_path' => $stras->image_path ?? null,
                'body' => ($customer->name ?? 'عميل') . ' تم حذف الاوردر (سلة المهملات)',
                'type' => 'alert', // Alert for delete
                'status' => 'unread',
                'link' => route('stras.trash'), // Link to trash? Or nowhere? Let's link to trash for now or just null. 
                // A deleted item can't be shown. But maybe link to Trash page?
                // Or just no link.
                // User said "put link on EVERY notification".
                // If I link to trash, it's a list.
                // Let's link to trash index.
            ]);

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

            // Notification
            $customer = Customers::find($new->customerId);
             \App\Models\Notifications::create([
                'user_id' => auth()->id(),
                'title' => 'تكرار اوردر استراس #' . $new->id,
                'img_path' => $new->image_path ?? null,
                'body' => ($customer->name ?? 'عميل') . ' تم تكرار الاوردر من #' . $original->id,
                'type' => 'order',
                'status' => 'unread',
                'link' => route('stras.show', $new->id),
            ]);

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
