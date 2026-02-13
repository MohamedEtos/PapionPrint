<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\Tarter;
use App\Models\TarterLayer;
use App\Models\TarterPrice;
use Illuminate\Support\Facades\DB;

class TarterController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:الترتر');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $customers = Customers::all();
        $prices = TarterPrice::all();
        $query = Tarter::with(['layers', 'customer'])->orderBy('created_at', 'desc');

        if (auth()->user()->can('الترتر') && !auth()->user()->can('الفواتير')) {
            $query->take(10);
        }

        $orders = $query->get();
        return view('tarter.index',
        [
            'customers' => $customers,
            'prices' => $prices,
            'Records' => $orders,
        ]);
    }

    public function show($id, Request $request)
    {
        $tarter = Tarter::with(['layers', 'customer'])->findOrFail($id);
        if($request->ajax()){
             return response()->json($tarter);
        }
        return view('tarter.show', compact('tarter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'height' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'cards_count' => 'nullable|integer',
            'pieces_per_card' => 'nullable|integer',
            'machine_time' => 'nullable|integer',
            'layers' => 'required|array',
            'layers.*.size' => 'required|string',
            'layers.*.count' => 'required|numeric',
            'image' => 'nullable|image',
            'manufacturing_cost' => 'nullable|numeric',
        ]);

        $data = $request->only(['height', 'width', 'cards_count', 'pieces_per_card', 'machine_time', 'notes', 'manufacturing_cost']);
        
        if($request->has('customerId') && $request->customerId != null){
            $data['customer_id'] = $request->customerId;
        } elseif ($request->has('customer_name') && $request->customer_name != null) {
            $customer = Customers::firstOrCreate(['name' => $request->customer_name]);
            $data['customer_id'] = $customer->id;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('tarter_images', 'public');
        }

        DB::transaction(function () use ($request, $data) {
            $tarter = Tarter::create($data);

            foreach ($request->layers as $layer) {
                $tarter->layers()->create([
                    'size' => $layer['size'],
                    'count' => $layer['count'],
                ]);
            }

            // Notification
            $customer = Customers::find($data['customer_id']);
             \App\Models\Notifications::create([
                'user_id' => auth()->id(),
                'title' => 'اوردر ترتر #' . $tarter->id,
                'img_path' => $tarter->image_path ?? null,
                'body' => ($customer->name ?? 'عميل') . ' تم انشاء اوردر ترتر جديد',
                'type' => 'order',
                'status' => 'unread',
                'link' => route('tarter.show', $tarter->id),
            ]);
        });

        return response()->json(['success' => 'Created successfully']);
    }

    public function update(Request $request, $id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:tarter,id',
        ])->validate();
         $tarter = Tarter::findOrFail($id);
         
          $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'height' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'cards_count' => 'nullable|integer',
            'pieces_per_card' => 'nullable|integer',
            'machine_time' => 'nullable|integer',
            'layers' => 'nullable|array',
            'image' => 'nullable|image',
            'manufacturing_cost' => 'nullable|numeric',
        ]);

        $data = $request->only(['height', 'width', 'cards_count', 'pieces_per_card', 'machine_time', 'notes', 'manufacturing_cost']);

        if($request->has('customerId') && $request->customerId != null){
            $data['customer_id'] = $request->customerId;
        } elseif ($request->has('customer_name') && $request->customer_name != null) {
            $customer = Customers::firstOrCreate(['name' => $request->customer_name]);
            $data['customer_id'] = $customer->id;
        }
        
        if ($request->hasFile('image')) {
             $data['image_path'] = $request->file('image')->store('tarter_images', 'public');
        }

        DB::transaction(function () use ($request, $tarter, $data) {
            $tarter->update($data);

            if($request->has('layers')){
                 $tarter->layers()->forceDelete(); // Force delete logic as per fix
                 foreach ($request->layers as $layer) {
                    $tarter->layers()->create([
                        'size' => $layer['size'],
                        'count' => $layer['count'],
                    ]);
                }
            }

            // Notification
            $customer = Customers::find($tarter->customer_id);
             \App\Models\Notifications::create([
                'user_id' => auth()->id(),
                'title' => 'تحديث اوردر ترتر #' . $tarter->id,
                'img_path' => $tarter->image_path ?? null,
                'body' => ($customer->name ?? 'عميل') . ' تم تحديث البيانات',
                'type' => 'order',
                'status' => 'unread',
                'link' => route('tarter.show', $tarter->id),
            ]);
        });
        
        return response()->json(['success' => 'Updated successfully']);
    }

    public function destroy($id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:tarter,id',
        ])->validate();
        $tarter = Tarter::find($id);
        if ($tarter) {
            $tarter->layers()->delete(); // Soft delete due to trait
            $tarter->delete(); // Soft delete due to trait

             // Notification
             $customer = Customers::find($tarter->customer_id);
             \App\Models\Notifications::create([
                'user_id' => auth()->id(),
                'title' => 'حذف اوردر ترتر #' . $tarter->id,
                'img_path' => $tarter->image_path ?? null,
                'body' => ($customer->name ?? 'عميل') . ' تم حذف الاوردر (سلة المهملات)',
                'type' => 'alert',
                'status' => 'unread',
                 'link' => route('tarter.trash'),
            ]);

            return response()->json(['success' => 'Deleted successfully']);
        }
        return response()->json(['error' => 'Not found'], 404);
    }

    public function restart($id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:tarter,id',
        ])->validate();
        return DB::transaction(function () use ($id) {
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

            // Notification
            $customer = Customers::find($new->customer_id);
             \App\Models\Notifications::create([
                'user_id' => auth()->id(),
                'title' => 'تكرار اوردر ترتر #' . $new->id,
                'img_path' => $new->image_path ?? null,
                'body' => ($customer->name ?? 'عميل') . ' تم تكرار الاوردر من #' . $original->id,
                'type' => 'order',
                'status' => 'unread',
                'link' => route('tarter.show', $new->id),
            ]);

            return response()->json(['success' => 'Order restarted (duplicated) successfully']);
        });
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tarters,id',
        ]);
        $ids = $request->ids;
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['error' => 'No items selected'], 400);
        }

        $orders = Tarter::whereIn('id', $ids)->get();
        
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
        $orders = Tarter::onlyTrashed()->with(['layers' => function($query) {
             $query->withTrashed();
        }, 'customer'])->orderBy('deleted_at', 'desc')->get();
        
        return view('tarter.trash', [
            'Records' => $orders
        ]);
    }

    public function restore($id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:tarters,id',
        ])->validate();
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
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:tarters,id',
        ])->validate();
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
        $request->validate([
            'id' => 'required|exists:tarter_prices,id',
            'price' => 'required|numeric',
        ]);
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
