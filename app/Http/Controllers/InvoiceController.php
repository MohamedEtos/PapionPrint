    <?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customers;
use App\Models\Stras;
use App\Models\Tarter;
use App\Models\Printers; 
use App\Models\Orders; // Rollpress uses Orders model? Checking usage... likely 'Orders' is Rollpress or generic order?
// Rollpress controller uses 'Orders' model.
// Printers controller also seems to us 'Orders' model? Wait. 
// Let's check imports in other controllers to be sure about Polymorphic types.
use Illuminate\Http\Request;
use Auth;
use App\Models\StrasPrice;
use App\Models\TarterPrice;
use App\Models\Machines;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:الفواتير');
    }
    public function showCart()
    {
        // Get current user's draft invoice
        $invoice = Invoice::with(['items.itemable','customer'])->firstOrCreate(
            ['user_id' => Auth::id() ?? 1, 'status' => 'draft']
        );
        
        $prods = $invoice->items; // Items in cart

        // Re-calculate Totals on Load (to ensure pricing updates are reflected)
        // Note: For a "cart", re-calculation is usually good. 
        // Logic for calculation will be handled here or in view/helper.
        
        $customers = Customers::all();


        return view('invoices.create', compact('invoice', 'prods', 'customers'));
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'type' => 'required|string|in:stras,tarter,printer,rollpress,rollpress_archive,laser'
        ]);

        $invoice = Invoice::firstOrCreate(
            ['user_id' => Auth::id() ?? 1, 'status' => 'draft']
        );

        $type = $request->type;
        $modelClass = $this->getModelClass($type);
        
        foreach ($request->ids as $id) {
            $itemId = $id;

            // Resolve Rollpress ID if we received a Printer ID (which is likely from presslist)
            if ($type === 'rollpress' || $type === 'rollpress_archive') {
               // For rollpress_archive, IDs are already Rollpress IDs from archive page
               // For rollpress from presslist, ID might be Printer/order ID
               if ($type === 'rollpress') {
                   // Check if the ID provided is a Rollpress ID or Printer ID. 
                   // Since presslist.js sends order_id (Printer ID), we assume it's Printer ID.
                   // We try to find the linked Rollpress record.
                   $rollpress = \App\Models\Rollpress::where('orderId', $id)->first();
                   
                   if ($rollpress) {
                       $itemId = $rollpress->id;
                   } else {
                       // Record doesn't exist. 
                       // Check if the ID passed IS a Rollpress ID.
                       if (!\App\Models\Rollpress::find($id)) {
                            continue; // Skip if no valid Rollpress record found relative to this ID
                       }
                   }
               }
            }

            // Calculate Price - pass normalized type for price calculation
            $priceCalcType = ($type === 'rollpress_archive') ? 'rollpress' : $type;
            
            $customPrice = 0;
            $quantity = 1;

            if ($type === 'laser') {
                $laserOrder = \App\Models\LaserOrder::find($itemId);
                if ($laserOrder) {
                    // Calculate effective unit price based on Total Cost / Required Pieces
                    if ($laserOrder->required_pieces > 0) {
                         $customPrice = $laserOrder->total_cost / $laserOrder->required_pieces;
                    } else {
                         $customPrice = $laserOrder->manufacturing_cost;
                    }
                    // Round to 2 decimals if needed, or keep precision? User said 2.31 which implies rounding or specific precision. 
                    // Let's keep it raw or round to 2 standard.
                    // $customPrice = round($customPrice, 2); 
                    
                    $quantity = $laserOrder->required_pieces;
                }
            } else {
                $customPrice = $this->calculatePrice($priceCalcType, $itemId);
            }

            InvoiceItem::firstOrCreate([
                'invoice_id' => $invoice->id,
                'itemable_id' => $itemId,
                'itemable_type' => $modelClass
            ], [
                'custom_price' => $customPrice,
                'quantity' => $quantity
            ]);
        }

        // Update Invoice Total
        $this->updateInvoiceTotal($invoice);

        // Prepare data for cart view - load relationships conditionally
        $invoice->load(['items.itemable']);
        
        // Load ordersImgs only for Printer items
        $invoice->items->each(function ($item) {
            if ($item->itemable_type === 'App\Models\Printers' && $item->itemable) {
                $item->itemable->load('ordersImgs');
            }
        });
        
        $cartCount = $invoice->items->count();
        $cartItems = $invoice->items;

        // Render cart dropdown HTML
        $cartHtml = view('components.cart_dropdown', compact('cartItems'))->render();

        return response()->json([
            'success' => 'Added to calculator',
            'cart_count' => $cartCount,
            'cart_html' => $cartHtml
        ]);
    }

    public function addCompositeItem(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string',
            'laser_cost' => 'nullable|numeric|min:0',
            'tarter_cost' => 'nullable|numeric|min:0',
            'print_cost' => 'nullable|numeric|min:0',
            'stras_cost' => 'nullable|numeric|min:0',
            'other_cost' => 'nullable|numeric|min:0',
            'quantity' => 'required|numeric|min:0.1'
        ]);

        $invoice = Invoice::firstOrCreate(
            ['user_id' => Auth::id() ?? 1, 'status' => 'draft']
        );

        $laser = $request->laser_cost ?? 0;
        $tarter = $request->tarter_cost ?? 0;
        $print = $request->print_cost ?? 0;
        $stras = $request->stras_cost ?? 0;
        $other = $request->other_cost ?? 0;
        $total = $laser + $tarter + $print + $stras + $other;

        $compositeItem = \App\Models\CompositeItem::create([
            'name' => $request->name,
            'laser_cost' => $laser,
            'tarter_cost' => $tarter,
            'print_cost' => $print,
            'stras_cost' => $stras,
            'other_cost' => $other,
            'total_price' => $total
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'itemable_id' => $compositeItem->id,
            'itemable_type' => \App\Models\CompositeItem::class,
            'custom_price' => $total,
            'quantity' => $request->quantity,
            'custom_details' => $request->name // Initial detail
        ]);

        $this->updateInvoiceTotal($invoice);

        // Refresh Cart Data
        $invoice->load(['items.itemable']);
        
        $invoice->items->each(function ($item) {
            if ($item->itemable_type === 'App\Models\Printers' && $item->itemable) {
                $item->itemable->load('ordersImgs');
            }
        });

        $cartCount = $invoice->items->count();
        $cartItems = $invoice->items;
        $cartHtml = view('components.cart_dropdown', compact('cartItems'))->render();

        return response()->json([
            'success' => 'Component added',
            'cart_count' => $cartCount,
            'cart_html' => $cartHtml
        ]);
    }

    public function removeItem($id)
    {
        InvoiceItem::destroy($id);
        
        // Update Total
        $invoice = Invoice::where('user_id', Auth::id() ?? 1)->where('status', 'draft')->first();
        if ($invoice) $this->updateInvoiceTotal($invoice);

        // Prepare data for cart view
        if ($invoice) {
            $invoice->load(['items.itemable']);
            
            // Load ordersImgs only for Printer items
            $invoice->items->each(function ($item) {
                if ($item->itemable_type === 'App\Models\Printers' && $item->itemable) {
                    $item->itemable->load('ordersImgs');
                }
                // Ensure other types are loaded if needed, though 'with' on Invoice query handles most.
                // CompositeItem has no extra relations to load deeply yet.
            });
            
            $cartCount = $invoice->items->count();
            $cartItems = $invoice->items;
        } else {
            $cartCount = 0;
            $cartItems = collect();
        }

        // Render cart dropdown HTML
        $cartHtml = view('components.cart_dropdown', compact('cartItems'))->render();

        // Check if request is AJAX
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => 'Item removed',
                'cart_count' => $cartCount,
                'cart_html' => $cartHtml
            ]);
        }

        return redirect()->back()->with('success', 'Item removed');
    }
    
    public function clearCart()
    {
        $invoice = Invoice::where('user_id', Auth::id() ?? 1)->where('status', 'draft')->first();
        if ($invoice) {
            $invoice->items()->delete();
            $invoice->delete();
        }

        // Empty cart data
        $cartCount = 0;
        $cartItems = collect();
        $cartHtml = view('components.cart_dropdown', compact('cartItems'))->render();

        // Check if request is AJAX
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => 'Cart cleared',
                'cart_count' => $cartCount,
                'cart_html' => $cartHtml
            ]);
        }

        return redirect()->back()->with('success', 'Cart cleared');
    }

    public function updateCustomer(Request $request)
    {
         $invoice = Invoice::firstOrCreate(
            ['user_id' => Auth::id() ?? 1, 'status' => 'draft']
        );
        $invoice->update(['customer_id' => $request->customer_id]);
        return response()->json(['success' => 'Customer updated']);
    }

    public function updateCustomerPhone(Request $request)
    {
        $customer = Customers::find($request->customer_id);
        if ($customer) {
            $customer->phone = $request->phone;
            $customer->save();
            return response()->json(['success' => 'Phone updated']);
        }
        return response()->json(['error' => 'Customer not found'], 404);
    }

    public function updateInvoiceItem(Request $request)
    {
        \Log::info('updateInvoiceItem endpoint hit', $request->all());

        $validated = $request->validate([
            'item_id' => 'required|exists:invoice_items,id',
            'quantity' => 'nullable|numeric',
            'custom_price' => 'nullable|numeric',
            'custom_details' => 'nullable|string',
            'sent_date' => 'nullable|date',
            'sent_status' => 'nullable|in:pending,sent,delivered',
            'unit_type' => 'nullable|in:meter,piece'
        ]);

        $item = InvoiceItem::find($request->item_id);
        
        if ($request->has('quantity')) {
            $item->quantity = $request->quantity;
        }
        
        if ($request->has('custom_price')) {
            $item->custom_price = $request->custom_price;
        }
        
        if ($request->has('custom_details')) {
            $item->custom_details = $request->custom_details;
        }
        
        if ($request->has('sent_date')) {
            $item->sent_date = $request->sent_date;
        }
        
        if ($request->has('sent_status')) {
            $item->sent_status = $request->sent_status;
        }

        if ($request->has('unit_type')) {
            $item->unit_type = $request->unit_type;
            
            // Recalculate price and quantity based on unit type
            if ($item->itemable_type === 'App\Models\Printers' && $item->itemable) {
                if ($request->unit_type === 'piece') {
                    $item->custom_price = $item->itemable->manufacturing_cost ?? 0;
                    $files = $item->itemable->fileCopies ?? 1;
                    $pics = $item->itemable->picInCopies ?? 1;
                    $item->quantity = $files * $pics;
                } else {
                    // Revert to meter price
                     $machine = $item->itemable->machines;
                     $uPrice = 0;
                     if($machine) {
                         if($item->itemable->pass == 1) $uPrice = $machine->price_1_pass;
                         elseif($item->itemable->pass == 4) $uPrice = $machine->price_4_pass;
                         elseif($item->itemable->pass == 6) $uPrice = $machine->price_6_pass;
                     }
                     $item->custom_price = $uPrice;
                     $item->quantity = $item->itemable->meters ?? 0;
                }
            }
        }
        
        $item->save();
        
        
        
        try {
            \Log::info('Archiving Invoice Item', [
                'invoice_id' => $item->invoice_id,
                'order_id' => $item->itemable_id,
                'order_type' => $item->itemable_type,
                'quantity' => $item->quantity,
                'price' => $item->custom_price
            ]);

            // Sync with InvoiceArchive
            $archive = \App\Models\InvoiceArchive::updateOrCreate(
                [
                    'invoice_id' => $item->invoice_id,
                    'order_id' => $item->itemable_id,
                    'order_type' => $item->itemable_type
                ],
                [
                    'quantity' => $item->quantity,
                    'unit_price' => $item->custom_price,
                    'total_price' => $item->custom_price * $item->quantity,
                    'sent_date' => $item->sent_date,
                    'sent_status' => $item->sent_status ?? 'pending',
                    'customer_name' => optional($item->invoice->customer)->name
                ]
            );
            
            \Log::info('Archive Saved/Updated', ['id' => $archive->id]);

        } catch (\Exception $e) {
            \Log::error('Failed to archive invoice item: ' . $e->getMessage());
            return response()->json(['error' => 'Saved locally but failed to archive: ' . $e->getMessage()], 500);
        }
        
        // Recalculate invoice total
        $this->updateInvoiceTotal($item->invoice);
        
        return response()->json(['success' => true]);
    }

    public function markAsSent(Request $request) {
        $invoice = Invoice::where('user_id', Auth::id() ?? 1)->where('status', 'draft')->first();
        
        if($invoice) {
            foreach($invoice->items as $item) {
                $item->sent_status = 'sent';
                $item->sent_date = now();
                $item->save();
                
                // Ensure Archive Record Exists/Updated
                \App\Models\InvoiceArchive::updateOrCreate(
                    [
                        'invoice_id' => $invoice->id,
                        'order_id' => $item->itemable_id,
                        'order_type' => $item->itemable_type
                    ],
                    [
                        'quantity' => $item->quantity,
                        'unit_price' => $item->custom_price,
                        'total_price' => $item->custom_price * $item->quantity,
                        'sent_date' => now(),
                        'sent_status' => 'sent',
                        'customer_name' => optional($invoice->customer)->name
                    ]
                );
            }
            
            // Mark the invoice itself as sent so a new draft is created next time
            $invoice->update(['status' => 'sent']);

             // Notification
             \App\Models\Notifications::create([
                'user_id' => auth()->id(),
                'title' => 'فاتورة واتساب #' . $invoice->id,
                'img_path' => null, // Maybe add icon?
                'body' => optional($invoice->customer)->name . ' تم ارسال الفاتورة عبر واتساب',
                'type' => 'invoice',
                'status' => 'unread',
                 'link' => route('invoice.history'), // Link to history as it is sent
            ]);

            return response()->json(['success' => true]);
        }
        return response()->json(['error' => 'No draft invoice found'], 404);
    }

    public function finalize(Request $request)
    {
        $invoice = Invoice::where('user_id', Auth::id() ?? 1)->where('status', 'draft')->first();

        if ($invoice) {
            // Ensure all items are archived before closing
            foreach($invoice->items as $item) {
                 \App\Models\InvoiceArchive::updateOrCreate(
                    [
                        'invoice_id' => $invoice->id,
                        'order_id' => $item->itemable_id,
                        'order_type' => $item->itemable_type
                    ],
                    [
                        'quantity' => $item->quantity,
                        'unit_price' => $item->custom_price,
                        'total_price' => $item->custom_price * $item->quantity,
                        'customer_name' => optional($invoice->customer)->name,
                        // Maintain existing sent status or default to pending
                        'sent_status' => $item->sent_status ?? 'pending',
                        'sent_date' => $item->sent_date
                    ]
                );
            }

            // Change status to 'saved' (or 'closed') so it's no longer picked up as draft
            $invoice->update(['status' => 'saved']);
            
             // Notification
             \App\Models\Notifications::create([
                'user_id' => auth()->id(),
                'title' => 'تم حفظ فاتورة #' . $invoice->id,
                'img_path' => null,
                'body' => optional($invoice->customer)->name . ' تم حفظ الفاتورة بنجاح',
                'type' => 'invoice',
                'status' => 'unread',
                'link' => route('invoice.history'), // Link to history
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'No draft invoice found'], 404);
    }

    public function invoiceHistory()
    {
        return view('invoices.history');
    }

    public function invoiceHistoryData(Request $request)
    {
        // Group by Invoice ID to show one row per invoice
        $query = \App\Models\InvoiceArchive::leftJoin('invoices', 'invoice_archives.invoice_id', '=', 'invoices.id')
            ->leftJoin('users', 'invoices.user_id', '=', 'users.id')
            ->select(
                'invoice_archives.invoice_id', 
                \DB::raw('MAX(invoice_archives.customer_name) as customer_name'), 
                \DB::raw('MAX(invoice_archives.created_at) as created_at'),
                \DB::raw('MAX(invoice_archives.sent_date) as sent_date'),
                \DB::raw('SUM(invoice_archives.total_price) as grand_total'),
                \DB::raw('COUNT(invoice_archives.id) as items_count'),
                \DB::raw('MAX(users.name) as user_name')
            )
            ->whereNotNull('invoice_archives.invoice_id')
            ->groupBy('invoice_archives.invoice_id');
        
        \Log::info('Fetching History Data Grouped');
        
        // Handle searching
        if ($request->has('search') && !empty($request->search['value'])) {
             $searchValue = $request->search['value'];
             $query->having('customer_name', 'like', "%{$searchValue}%")
                   ->orHaving('grand_total', 'like', "%{$searchValue}%")
                   ->orHaving('user_name', 'like', "%{$searchValue}%");
        }
        
        if ($request->has('order')) {
            $orderColumnIndex = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];
            $columns = $request->columns;
            $columnName = $columns[$orderColumnIndex]['name'] ?? null;
            
            $orderableColumns = ['created_at', 'grand_total', 'items_count', 'invoice_id', 'user_id'];
            if ($columnName && in_array($columnName, $orderableColumns)) {
                 if($columnName === 'user_id') $query->orderBy('user_name', $orderDirection);
                 else $query->orderBy($columnName, $orderDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        // Pagination logic remains (simplified for now as before)
         if ($request->has('start') && $request->length != -1) {
            $query->skip($request->start)->take($request->length);
        }
        
        $invoices = $query->get();
        // Recount separately if needed or just use count
        $totalRecords = $invoices->count(); // Simplified
        
        $data = $invoices->map(function($inv) {
            return [
                'id' => $inv->invoice_id,
                'user_id' => $inv->user_name ?? 'N/A', // Map user_name to user_id for datatable
                'customer_name' => $inv->customer_name ?? 'غير معروف',
                'items_count' => $inv->items_count,
                'grand_total' => number_format($inv->grand_total, 2) . ' ج.م',
                'created_at' => \Carbon\Carbon::parse($inv->created_at)->format('Y-m-d H:i'),
                'action' => '<button class="btn btn-sm btn-primary view-details-history" data-id="'.$inv->invoice_id.'">عرض التفاصيل</button>'
            ];
        });
        
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords, // Fix if possible
            'recordsFiltered' => $totalRecords, // Use total for pagination logic to work
            'data' => $data
        ]);
    }
    public function getInvoiceDetails($invoiceId)
    {
        $items = \App\Models\InvoiceArchive::with(['itemable'])
                    ->where('invoice_id', $invoiceId)
                    ->get();
                    
        if ($items->isEmpty()) {
            return response()->json(['error' => 'No items found'], 404);
        }
        
        $html = view('invoices.partials.invoice_details_modal', compact('items'))->render();
        return response()->json(['html' => $html]);
    }

    public function getItemDetails($id)
    {
        $invoiceItem = InvoiceItem::with('itemable')->find($id);
        
        if (!$invoiceItem) {
            return response()->json(['error' => 'Not found'], 404);
        }
        
        // Mock aggregate object to look like archive for the view
        $mockArchive = new \stdClass();
        $mockArchive->quantity = $invoiceItem->quantity;
        $mockArchive->unit_price = $invoiceItem->custom_price;
        $mockArchive->total_price = $invoiceItem->quantity * $invoiceItem->custom_price;
        $mockArchive->sent_status = $invoiceItem->sent_status ?? 'pending'; // Default
        
        $item = $invoiceItem->itemable;
        $typeMap = [
            'App\Models\Stras' => 'stras',
            'App\Models\Tarter' => 'tarter',
            'App\Models\Printers' => 'printer',
            'App\Models\Rollpress' => 'rollpress',
            'App\Models\Rollpress' => 'rollpress',
            'App\Models\LaserOrder' => 'laser',
            'App\Models\CompositeItem' => 'composite'
        ];
        $type = $typeMap[$invoiceItem->itemable_type] ?? 'unknown';
        
        $html = view('invoices.partials.modal_body', [
            'archive' => $mockArchive,
            'item' => $item,
            'type' => $type
        ])->render();
        
        return response()->json(['html' => $html]);
    }

    public function getArchiveDetails($id)
    {
        $archive = \App\Models\InvoiceArchive::with(['itemable'])->find($id);
        
        if (!$archive) {
            return response()->json(['error' => 'Not found'], 404);
        }
        
        $item = $archive->itemable;
        $typeMap = [
            'App\Models\Stras' => 'stras',
            'App\Models\Tarter' => 'tarter',
            'App\Models\Printers' => 'printer',
            'App\Models\Rollpress' => 'rollpress',
            'App\Models\Rollpress' => 'rollpress',
            'App\Models\LaserOrder' => 'laser',
            'App\Models\CompositeItem' => 'composite'
        ];
        $type = $typeMap[$archive->order_type] ?? 'unknown';
        
        $html = view('invoices.partials.modal_body', compact('archive', 'item', 'type'))->render();
        
        return response()->json(['html' => $html]);
    }

    private function getModelClass($type)
    {
        return match ($type) {
            'stras' => Stras::class,
            'tarter' => Tarter::class,
            'printer' => Printers::class,
            'rollpress', 'rollpress_archive' => \App\Models\Rollpress::class,
            'laser' => \App\Models\LaserOrder::class,
            'composite' => \App\Models\CompositeItem::class,
             default => throw new \Exception("Invalid Type"),
        };
    }

    private function updateInvoiceTotal(Invoice $invoice)
    {
        $total = $invoice->items->sum(function($item) {
            return $item->custom_price * $item->quantity;
        });
        $invoice->update(['total_amount' => $total]);
    }

    private function calculatePrice($type, $itemId)
    {
        $price = 0;
        
        if ($type === 'stras') {
            $item = Stras::with('layers')->find($itemId);
            if (!$item) return 0;
            
            return $item->manufacturing_cost ?? 0;

        } elseif ($type === 'tarter') {
            $item = Tarter::find($itemId);
            if (!$item) return 0;
            return $item->manufacturing_cost ?? 0;

        } elseif ($type === 'printer') {
            $item = Printers::with('machines')->find($itemId);
            if (!$item) return 0;
            
            $machine = $item->machines;
            if ($machine) {
                 $unitPrice = 0;
                 if ($item->pass == 1) $unitPrice = $machine->price_1_pass; 
                 elseif ($item->pass == 4) $unitPrice = $machine->price_4_pass;
                 elseif ($item->pass == 6) $unitPrice = $machine->price_6_pass;
                 
                 return $unitPrice;
            }

        } elseif ($type === 'rollpress') {
            $item = \App\Models\Rollpress::find($itemId);
            if (!$item) return 0;
            $price = $item->price ?? 0;
        } elseif ($type === 'laser') {
            $item = \App\Models\LaserOrder::find($itemId);
            if (!$item) return 0;
            $price = $item->total_cost ?? 0;
        } elseif ($type === 'composite') {
            $item = \App\Models\CompositeItem::find($itemId);
            if (!$item) return 0;
            $price = $item->total_price ?? 0;
        }

        return round($price, 2);
    }
}
