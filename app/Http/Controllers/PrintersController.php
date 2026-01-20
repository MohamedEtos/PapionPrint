<?php

namespace App\Http\Controllers;

use App\Models\Printers;
use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\Machines;
use App\Models\User;

class PrintersController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Orders = Printers::with('printingprices','ordersImgs')->get();
        $customers = Customers::all();
        $machines = Machines::all();

        return view('printers.AddPrintOrders', 
        [
            'Orders'=>$Orders,
            'customers' => $customers,
            'machines' => $machines,
        ]);
    }

    public function uploadImage(Request $request)
    {
        if($request->hasFile('file')){
            $file = $request->file('file');
            $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images/orders', $filename, 'public');
            return response()->json(['success' => $filename, 'path' => $path]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $request->validate([
            'customerId' => 'required|string|max:255',
            'machineId' => 'required|exists:machines,id',
            'fileHeight' => 'nullable|numeric',
            'fileWidth' => 'nullable|numeric',
            'fileCopies' => 'nullable|integer',
            'picInCopies' => 'nullable|integer',
            'pass' => 'nullable|integer',
            'meters' => 'nullable|numeric',
            'price' => 'nullable|numeric',
        ], [
            'required' => 'حقل :attribute مطلوب.',
            'numeric' => 'حقل :attribute يجب أن يكون رقماً.',
            'integer' => 'حقل :attribute يجب أن يكون عدداً صحيحاً.',
            'exists' => 'القيمة المختارة لـ :attribute غير موجودة.',
            'string' => 'حقل :attribute يجب أن يكون نصاً.',
            'max' => 'حقل :attribute يجب أن لا يتجاوز :max حرفاً.',
        ], [
            'customerId' => 'اسم العميل',
            'machineId' => 'الماكينة',
            'fileHeight' => 'الطول',
            'fileWidth' => 'العرض',
            'fileCopies' => 'عدد النسخ',
            'picInCopies' => 'الصور في النسخة',
            'pass' => 'عدد الوجوه (Pass)',
            'meters' => 'الأمتار',
            'price' => 'السعر',
        ]);



        $customers = Customers::create([
            'name' => $request->customerId,
        ]);
        // Demonstration Logic
        $printer = Printers::create([
            'orderNumber' => 'ORD-' . time(),
            'customerId' => $customers->id, 
            'machineId' => $request->machineId,
            'fileHeight' => $request->fileHeight ?? 0,
            'fileWidth' => $request->fileWidth ?? 0,
            'fileCopies' => $request->fileCopies ?? 0,
            'picInCopies' => $request->picInCopies ?? 0,
            'pass' => $request->pass ?? 1,
            'meters' => $request->meters ?? 0,
            // 'totalPrice' => $request->price ?? 0, // Removed as column doesn't exist
            'status' => $request->status ?? 'waiting',
            'notes' => $request->notes,
            'designerId' => auth()->id() ?? 1, 
            'operatorId' => 1, 
        ]);

        // Create Price Record
        \App\Models\Printingprices::create([
            'machineId' => $request->machineId,
            'orderId' => $printer->id,
            'pricePerMeter' => 0, // Need calculation logic or input? Assuming 0 for now or calculate from price/meters
            'totalPrice' => $request->price ?? 0,
            'discount' => 0,
            'finalPrice' => $request->price ?? 0, // Assuming final price is same as total for now
        ]);

        if ($request->filled('image_paths')) {
            foreach ($request->image_paths as $path) {
                \App\Models\OrdersImg::create([
                    'orderId' => $printer->id,
                    'path' => $path,
                    'type' => 'image',
                ]);
            }
        }
        
        // Eager load relationships for the frontend response
        $printer->load(['customers', 'machines', 'printingprices', 'ordersImgs']);

        return response()->json(['success' => 'Order created successfully', 'order' => $printer]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Printers $printers)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Printers $printers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Printers $printers)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $printer = Printers::find($id);
        if ($printer) {
            $printer->delete();
            return response()->json(['success' => 'Order deleted successfully']);
        }
        return response()->json(['error' => 'Order not found'], 404);
    }

    public function updateStatus($id)
    {
        $order = Printers::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $statuses = ['بانتظار اجراء', 'بدء الطباعة', 'انتهاء الطباعة', 'تم الكبس', 'تم الانتهاء'];
        $currentStatusIndex = array_search($order->status, $statuses);

        if ($currentStatusIndex !== false && $currentStatusIndex < count($statuses) - 1) {
            $nextStatus = $statuses[$currentStatusIndex + 1];
        } else {
            $nextStatus = 'بانتظار اجراء';
        }

        $order->status = $nextStatus;
        $order->save();

        return response()->json(['success' => 'Status updated', 'status' => $nextStatus]);
    }
}
