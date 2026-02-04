<?php

namespace App\Http\Controllers;

use App\Models\Printerlogs;
use Illuminate\Http\Request;
use App\Models\Printers;
use App\Models\Customers;
use App\Models\Machines;
use App\Models\User;
use App\Models\Rollpress;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RollpressController extends Controller
{

    public function index()
    {
        return view('rollpress.addpressorder');
    }
    


    /**
     * Display a listing of the resource.
     */
    public function presslist(Request $request)
    {
        $Orders = Printers::with('printingprices','ordersImgs','rollpress')
        ->where('archive', '1')
        ->whereHas('machines', function ($query) {
            $query->where('name', 'sublimation');
        })
        ->whereDoesntHave('rollpress', function ($query) {
            $query->where('status', 1);
        })
        ->get();
        $Rolls = Rollpress::with('customer', 'order.customers', 'order.ordersImgs')
            ->where('status', '!=', 1)
            ->get();
        $customers = Customers::all();
        $machines = Machines::all();

   
        $todayAttendance = Attendance::where('user_id', Auth::id())
        ->where('date', Carbon::today())
        ->first();


        return view('rollpress.presslist',
        [   
            'todayAttendance' => $todayAttendance,
            'Orders'=>$Orders,
            'Rolls' => $Rolls,
            'customers' => $customers,
            'machines' => $machines,
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validation with Arabic Messages
        $messages = [
            'customerName.required' => 'اسم العميل مطلوب',
            'fabrictype.required' => 'نوع القماش مطلوب',
            'fabricwidth.required' => 'عرض القماش مطلوب',
            'fabricwidth.numeric' => 'عرض القماش يجب أن يكون رقم',
            'meters.required' => 'الامتار مطلوبة',
            'meters.numeric' => 'الامتار يجب أن تكون رقم',
        ];

        $request->validate([
            'customerName' => 'required',
            'fabrictype' => 'required',
            'fabricwidth' => 'required|numeric',
            'meters' => 'required|numeric',
        ], $messages);

        // 2. Handle Customer
        $customerId = $request->customerId;
        $customerName = $request->customerName;

        if (!$customerId && $customerName) {
            // Check if exists by name to avoid duplicates if ID missing
            $existingCustomer = Customers::where('name', $customerName)->first();
            if ($existingCustomer) {
                $customerId = $existingCustomer->id;
            } else {
                $newCustomer = Customers::create(['name' => $customerName]);
                $customerId = $newCustomer->id;
            }
        }

        // 3. Create Rollpress Record w/ Transaction
        $rollpress = DB::transaction(function () use ($request, $customerId) {
            $rollpress = new Rollpress();
            
            if ($request->filled('orderId')) {
                 $rollpress->orderId = $request->orderId;
            }

            $rollpress->customerId = $customerId;
            $rollpress->fabrictype = $request->fabrictype;
            $rollpress->fabricsrc = $request->fabricsrc;
            $rollpress->fabriccode = $request->fabriccode;
            $rollpress->fabricwidth = $request->fabricwidth;
            $rollpress->meters = $request->meters;
            $rollpress->status = 1; 
            $rollpress->paymentstatus = $request->paymentstatus == '1' ? 1 : 0;
            $rollpress->papyershild = $request->papyershild;
            $rollpress->price = $request->price;
            $rollpress->notes = $request->notes;
            $rollpress->save();

            return $rollpress;
        });
        
        return response()->json(['success' => true, 'rollpress' => $rollpress]);
    }

    public function archive(Request $request)
    {


        if ($request->ajax()) {
            $query = Rollpress::with('customer', 'order.customers', 'order.ordersImgs');
            // Filter by Status (Archive usually means history, so maybe show all? Or just status=1?)
            // User said "Archive page... add all data found in table".
            // Let's show ALL data for now, or maybe allow filtering.
            // If it's strictly "Archive", maybe we filter by status=1?
            // "Archive page... add all data found in table" implies listing the table.
            
            // Search logic refined to avoid errors if relations missing
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function ($q) use ($searchValue) {
                    $q->where('id', 'like', "%{$searchValue}%")
                      ->orWhere('fabrictype', 'like', "%{$searchValue}%")
                      ->orWhere('fabriccode', 'like', "%{$searchValue}%")
                      ->orWhere('notes', 'like', "%{$searchValue}%")
                      ->orWhereHas('customer', function ($q) use ($searchValue) {
                          $q->where('name', 'like', "%{$searchValue}%");
                      });
                });
            }

            // Sorting
            if ($request->has('order')) {
                $orderColumnIndex = $request->order[0]['column'];
                $orderDirection = $request->order[0]['dir'];
                $columns = $request->columns;
                $columnName = $columns[$orderColumnIndex]['data'];
                
                // Map column names if necessary, for now assuming data matches db columns
                if ($columnName && !in_array($columnName, ['action', 'image', 'customer.name', 'customerName'])) {
                    // Check if column exists in table to avoid SQL error
                    // For now, rely on frontend sending correct data names.
                     $query->orderBy($columnName, $orderDirection);
                } else {
                     $query->orderBy('created_at', 'desc');
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $totalRecords = Rollpress::count();
            $filteredRecords = $query->count();

            // Pagination
            if ($request->has('start') && $request->length != -1) {
                $query->skip($request->start)->take($request->length);
            }

            $data = $query->get();

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }
        
        $customers = Customers::all();

        return view('rollpress.archive', ['customers' => $customers]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:Rollpress,id',
        ]);
        $ids = $request->ids;
        if (!empty($ids)) {
            DB::transaction(function () use ($ids) {
                Rollpress::whereIn('id', $ids)->delete();
            });
            return response()->json(['success' => 'Orders deleted successfully']);
        }
        return response()->json(['error' => 'No orders selected'], 400);
    }

    public function update(Request $request, $id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:Rollpress,id',
        ])->validate();
                
        $rollpress = Rollpress::find($id);
        if (!$rollpress) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Validation - Similar to store but nullable mostly?
        // Let's use same validation or simplified if partial updates allowed.
        // User asked for "Edit", implying full form submit logic.
        // Replicating store validation but allowing updates.
        
        $messages = [
            'customerName.required' => 'اسم العميل مطلوب',
            'fabrictype.required' => 'نوع القماش مطلوب',
            'fabricwidth.required' => 'عرض القماش مطلوب',
            'fabricwidth.numeric' => 'عرض القماش يجب أن يكون رقم',
            'meters.required' => 'الامتار مطلوبة',
            'meters.numeric' => 'الامتار يجب أن تكون رقم',
        ];

        // If editing via wizard, we likely send all fields.
        $request->validate([
            'customerName' => 'required',
            'fabrictype' => 'required',
            'fabricwidth' => 'required|numeric',
            'meters' => 'required|numeric',
        ], $messages);

        // Handle Customer Update if name changed
        $customerId = $request->customerId;
        $customerName = $request->customerName;

        if ($request->filled('customerName')) {
             $existingCustomer = Customers::where('name', $customerName)->first();
             if ($existingCustomer) {
                 $customerId = $existingCustomer->id;
             } else {
                 $newCustomer = Customers::create(['name' => $customerName]);
                 $customerId = $newCustomer->id;
             }
        }
        
        DB::transaction(function () use ($rollpress, $customerId, $request) {
            $rollpress->customerId = $customerId;
            $rollpress->fabrictype = $request->fabrictype;
            $rollpress->fabricsrc = $request->fabricsrc;
            $rollpress->fabriccode = $request->fabriccode;
            $rollpress->fabricwidth = $request->fabricwidth;
            $rollpress->meters = $request->meters;
            $rollpress->paymentstatus = $request->paymentstatus == '1' ? 1 : 0;
            $rollpress->papyershild = $request->papyershild;
            $rollpress->price = $request->price;
            $rollpress->notes = $request->notes;
            
            // Handle Status update if provided, mapping string/int
            if ($request->filled('status')) {
                 // If incoming is "تم الانتهاء" or "1", set to 1. Else 0.
                 $statusInput = $request->status;
                 $rollpress->status = ($statusInput == '1' || $statusInput == 'تم الانتهاء') ? 1 : 0;
            }

            $rollpress->save();
        });
        
        return response()->json(['success' => true, 'rollpress' => $rollpress]);
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = Rollpress::onlyTrashed()->with('customer', 'order.customers', 'order.ordersImgs');
            
            // Search logic refined
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function ($q) use ($searchValue) {
                    $q->where('id', 'like', "%{$searchValue}%")
                      ->orWhere('fabrictype', 'like', "%{$searchValue}%")
                      ->orWhere('fabriccode', 'like', "%{$searchValue}%")
                      ->orWhere('notes', 'like', "%{$searchValue}%")
                      ->orWhereHas('customer', function ($q) use ($searchValue) {
                          $q->where('name', 'like', "%{$searchValue}%");
                      });
                });
            }

            $query->orderBy('deleted_at', 'desc');

            $totalRecords = Rollpress::onlyTrashed()->count();
            $filteredRecords = $query->count();

            if ($request->has('start') && $request->length != -1) {
                $query->skip($request->start)->take($request->length);
            }

            $data = $query->get();

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }
        
        $customers = Customers::all();

        return view('rollpress.trash', ['customers' => $customers]);
    }

    public function restore($id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:Rollpress,id',
        ])->validate();
        $rollpress = Rollpress::withTrashed()->find($id);
        if ($rollpress && $rollpress->trashed()) {
            $rollpress->restore();
            return response()->json(['success' => 'Order restored successfully']);
        }
        return response()->json(['error' => 'Order not found or not deleted'], 404);
    }

    public function forceDelete($id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:Rollpress,id',
        ])->validate();
        $rollpress = Rollpress::withTrashed()->find($id);
        if ($rollpress) {
            $rollpress->forceDelete();
            return response()->json(['success' => 'Order permanently deleted']);
        }
        return response()->json(['error' => 'Order not found'], 404);
    }

}