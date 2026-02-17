<?php

namespace App\Http\Controllers;

use App\Models\Printerlogs;
use Illuminate\Http\Request;
use App\Models\Printers;
use App\Models\Customers;
use App\Models\Machines;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PrinterlogsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function printLog(Request $request)
    {



        if ($request->ajax()) {
            $query = Printers::with(['printingprices', 'ordersImgs', 'customers', 'machines', 'user', 'user2'])
                ->where('archive', 1);

            // Restrict Visibility: printing ONLY -> 10 latest orders
            if (auth()->check() && auth()->user()->can('الطباعه') && !auth()->user()->can('الفواتير')) {
                // Get IDs of latest 10
                $latestIds = Printers::where('archive', 1)
                    ->orderBy('id', 'desc')
                    ->take(30)
                    ->pluck('id');
                
                $query->whereIn('id', $latestIds);
            }

            // Date Filter
            if ($request->has('min') && $request->min != '') {
                $query->whereDate('created_at', '>=', $request->min);
            }
            if ($request->has('max') && $request->max != '') {
                $query->whereDate('created_at', '<=', $request->max);
            }

            // Global Search
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function ($q) use ($searchValue) {
                    $q->where('orderNumber', 'like', "%{$searchValue}%")
                      ->orWhereHas('customers', function ($q) use ($searchValue) {
                          $q->where('name', 'like', "%{$searchValue}%");
                      })
                      ->orWhereHas('machines', function ($q) use ($searchValue) {
                          $q->where('name', 'like', "%{$searchValue}%");
                      })
                      ->orWhere('notes', 'like', "%{$searchValue}%");
                });
            }

            // Sorting
            if ($request->has('order')) {
                $orderColumnIndex = $request->order[0]['column'];
                $orderDirection = $request->order[0]['dir'];
                $columns = $request->columns;
                $columnName = $columns[$orderColumnIndex]['data'];
                
                // Handle specific columns sorting if needed, otherwise default
                if ($columnName && $columnName != 'action' && $columnName != 'image') {
                     // specific handling for related columns can be added here
                     // for now basic sorting
                     if (in_array($columnName, ['orderNumber', 'fileHeight', 'fileWidth', 'fileCopies', 'picInCopies', 'meters', 'created_at'])) {
                         $query->orderBy($columnName, $orderDirection);
                     } else {
                         $query->orderBy('id', 'desc'); 
                     }
                } else {
                    $query->orderBy('id', 'desc');
                }
            } else {
                $query->orderBy('id', 'desc');
            }

            $totalRecords = Printers::where('archive', 1)->count();
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
        $machines = Machines::all();

        return view('printers.print_log',
        [
            'customers' => $customers,
            'machines' => $machines,
        ]);
    }

    public function duplicate($id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:printers,id',
        ])->validate();

        $order = Printers::with(['ordersImgs', 'customers', 'machines'])->find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        DB::transaction(function () use ($order) {
            $newOrder = $order->replicate();
            $newOrder->archive = 0;
            $newOrder->orderNumber = 'ORD-' . time() . '-' . rand(10, 99);
            $newOrder->status = 'بانتظار اجراء';
            $newOrder->timeEndOpration = null;
            $newOrder->created_at = now();
            $newOrder->updated_at = now();
            $newOrder->save();

            // Replicate images
            foreach ($order->ordersImgs as $img) {
                $newImg = $img->replicate();
                $newImg->orderId = $newOrder->id;
                $newImg->save();
            }

            // Deduct Paper Stock
            if ($order->machines && $newOrder->meters > 0) {
                $machineName = strtolower($order->machines->name);
                $type = (str_contains($machineName, 'dtf') || str_contains($machineName, 'DTF')) ? 'dtf' : 'sublimation';
                
                $stock = \App\Models\Stock::where('type', 'paper')
                            ->where('machine_type', $type)
                            ->first();
                
                if ($stock) {
                    $stock->decrement('quantity', $newOrder->meters);
                } else {
                     \App\Models\Stock::create([
                         'type' => 'paper',
                         'machine_type' => $type,
                         'quantity' => -($newOrder->meters),
                         'unit' => 'meter'
                     ]);
                }
            }

             // Notification
             $customer = $order->customers;
             \App\Models\Notifications::create([
                'user_id' => auth()->id(),
                'title' => 'تكرار اوردر طباعه #' . $newOrder->orderNumber,
                'img_path' => $newOrder->ordersImgs->first()->path ?? null,
                'body' => ($customer->name ?? 'عميل') . ' تم تكرار الاوردر من #' . $order->orderNumber,
                'type' => 'order',
                'status' => 'unread',
                'link' => route('printers.show', $newOrder->id),
            ]);
        });

        return response()->json(['success' => 'Order duplicated successfully']);
    }

}