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
            
            // Use Intervention Image Manager with GD Driver
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            
            // Read and process image
            $image = $manager->read($file);
            
            // Scale to max height 220 (maintains aspect ratio)
            $image->scale(height: 220);
            
            // Encode to WebP
            $encoded = $image->toWebp(90);
            
            $filename = uniqid() . '_' . time() . '.webp';
            $path = 'images/orders/' . $filename;
            
            // Save using Storage facade
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, (string) $encoded);
            
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
    public function show($id)
    {
        $printer = Printers::with(['customers', 'machines', 'printingprices', 'ordersImgs', 'user', 'user2'])->find($id);

        if (!$printer) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json($printer);
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
    public function update(Request $request, $id)
    {
        

        $printer = Printers::find($id);
        if (!$printer) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $request->validate([
            'customerId' => 'nullable|string|max:255',
            'machineId' => 'nullable|exists:machines,id',
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

        // Update Customer if name changed (Optional: usually we pick existing, but here we might just verify)
        // For simplicity/safety, we might skip updating customer relation *link* based on name unless strict logic exists. 
        // Assuming customerId passed is name string from datalist. 
        // If it's a new name, we create new customer? Or just update existing?
        // logic in store() was: $customers = Customers::create(['name' => $request->customerId]);
        // This implies we create a new customer every time? That seems like a potential duplicate issue but I will stick to existing patterns or minimal changes.
        // Let's assume for update we simply update fields on the Printer. 

        // If 'customerId' input is actually a name string:
        if ($request->filled('customerId')) {
            // Check if customer exists or create new? 
            // Better to find existing by name first implementation in store() was naive.
            // For update, let's respect the ID pattern if possible, or just create new if name changed.
             $customer = Customers::firstOrCreate(['name' => $request->customerId]);
             $printer->customerId = $customer->id;
        }

        if ($request->filled('machineId')) $printer->machineId = $request->machineId;
        if ($request->filled('fileHeight')) $printer->fileHeight = $request->fileHeight;
        if ($request->filled('fileWidth')) $printer->fileWidth = $request->fileWidth;
        if ($request->filled('fileCopies')) $printer->fileCopies = $request->fileCopies;
        if ($request->filled('picInCopies')) $printer->picInCopies = $request->picInCopies;
        if ($request->filled('pass')) $printer->pass = $request->pass;
        if ($request->filled('meters')) $printer->meters = $request->meters;
        if ($request->filled('notes')) $printer->notes = $request->notes;
        
        // Auto Advance Status if requested
        if ($request->boolean('auto_advance_status') && $printer->status == 'بانتظار اجراء') {
             $printer->status = 'بدات الطباعة'; // Next logical step
        } elseif ($request->filled('status')) {
             $printer->status = $request->status;
        }

        $printer->save();

        // Update Price
        $priceRecord = \App\Models\Printingprices::where('orderId', $printer->id)->first();
        if ($priceRecord) {
             $priceRecord->totalPrice = $request->price ?? $priceRecord->totalPrice;
             $priceRecord->finalPrice = $request->price ?? $priceRecord->finalPrice;
             $priceRecord->save();
        } else {
             // Create if missing
             \App\Models\Printingprices::create([
                'machineId' => $printer->machineId,
                'orderId' => $printer->id,
                'pricePerMeter' => 0,
                'totalPrice' => $request->price ?? 0,
                'discount' => 0,
                'finalPrice' => $request->price ?? 0,
            ]);
        }
        
        // Return updated object with relations
        $printer->load(['customers', 'machines', 'printingprices', 'ordersImgs']);

        return response()->json(['success' => 'Order updated successfully', 'order' => $printer]);
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

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!empty($ids)) {
            Printers::whereIn('id', $ids)->delete();
            return response()->json(['success' => 'Orders deleted successfully']);
        }
        return response()->json(['error' => 'No orders selected'], 400);
    }

    public function updateStatus($id)
    {
        $order = Printers::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $statuses = ['بانتظار اجراء', 'بدات الطباعة', 'انتهاء الطباعة', 'تم الكبس', 'تم الانتهاء'];
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
