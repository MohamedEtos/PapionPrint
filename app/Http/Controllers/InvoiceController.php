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
            'type' => 'required|string|in:stras,tarter,printer,rollpress,rollpress_archive'
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
                       // Option 1: Create it?
                       // Option 2: Skip?
                       // For now, let's create a default one or skip. 
                       // If we skip, the user sees nothing.
                       // Let's try to find it. If not found, we assume the user might have passed a Rollpress ID? 
                       // (Unlikely given JS).
                       // Let's check if the ID passed IS a Rollpress ID (by checking existence).
                       if (!\App\Models\Rollpress::find($id)) {
                            continue; // Skip if no valid Rollpress record found relative to this ID
                       }
                       // If it was found by find($id), then $itemId is already correct.
                   }
               }
               // For rollpress_archive, $itemId from $id is already correct (Rollpress ID)
            }

            // Calculate Price - pass normalized type for price calculation
            $priceCalcType = ($type === 'rollpress_archive') ? 'rollpress' : $type;
            $customPrice = $this->calculatePrice($priceCalcType, $itemId);

            InvoiceItem::firstOrCreate([
                'invoice_id' => $invoice->id,
                'itemable_id' => $itemId,
                'itemable_type' => $modelClass
            ], [
                'custom_price' => $customPrice,
                'quantity' => 1
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

    private function getModelClass($type)
    {
        return match ($type) {
            'stras' => Stras::class,
            'tarter' => Tarter::class,
            'printer' => Printers::class,
            'rollpress', 'rollpress_archive' => \App\Models\Rollpress::class,
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
            
            $prices = StrasPrice::all();
            $pricesMap = [
                'stras' => [],
                'paper' => [],
                'global' => []
            ];
            foreach ($prices as $p) {
                if ($p->type === 'stras') $pricesMap['stras'][$p->size] = (float)$p->price;
                elseif ($p->type === 'paper') $pricesMap['paper'][(int)preg_replace('/[^0-9]/', '', $p->size)] = (float)$p->price;
                elseif ($p->type === 'global' && $p->size === 'operating_cost') $pricesMap['global']['op_cost'] = (float)$p->price;
            }

            $cardsCount = $item->cards_count ?? 0;
            if ($cardsCount > 0) {
                // Paper Cost
                $paperPrice = $pricesMap['paper'][round($item->width ?? 0)] ?? 0;
                $cardPaperCost = ($item->height / 100) * $paperPrice;
                
                // Op Cost
                $opCost = $pricesMap['global']['op_cost'] ?? 0;
                
                $rowCardCost = $cardPaperCost + $opCost;
                
                // Layers
                foreach ($item->layers as $layer) {
                     $unitPrice = $pricesMap['stras'][$layer->size] ?? 0;
                     $rowCardCost += ($layer->count * $unitPrice);
                }
                
                $price = $rowCardCost * $cardsCount;
            }

        } elseif ($type === 'tarter') {
            $item = Tarter::with('layers')->find($itemId);
            if (!$item) return 0;

            $prices = TarterPrice::all();
            $pricesMap = [
                'needle' => [],
                'paper' => [],
                'global' => [],
                'machine' => 0
            ];
            foreach ($prices as $p) {
                if ($p->type === 'needle') $pricesMap['needle'][$p->size] = (float)$p->price;
                elseif ($p->type === 'paper') $pricesMap['paper'][(int)preg_replace('/[^0-9]/', '', $p->size)] = (float)$p->price;
                elseif ($p->type === 'global' && $p->size === 'operating_cost') $pricesMap['global']['op_cost'] = (float)$p->price;
                elseif ($p->type === 'machine_time_cost') $pricesMap['machine'] = (float)$p->price;
            }

            $cardsCount = $item->cards_count ?? 0;
            if ($cardsCount > 0) {
                $paperPrice = $pricesMap['paper'][round($item->width ?? 0)] ?? 0;
                $cardPaperCost = ($item->height / 100) * $paperPrice;
                $opCost = $pricesMap['global']['op_cost'] ?? 0;
                
                $rowCardCost = $cardPaperCost + $opCost;

                foreach ($item->layers as $layer) {
                     $unitPrice = $pricesMap['needle'][$layer->size] ?? 0;
                     $rowCardCost += ($layer->count * $unitPrice);
                }
                
                $price = ($rowCardCost * $cardsCount);
            }
            // Add Machine Time Cost
            $price += ($item->machine_time * $pricesMap['machine']);

        } elseif ($type === 'printer') {
            $item = Printers::with('machines')->find($itemId);
            if (!$item) return 0;
            
            $machine = $item->machines;
            if ($machine) {
                 $unitPrice = 0;
                 if ($item->pass == 1) $unitPrice = $machine->price_1_pass; 
                 elseif ($item->pass == 4) $unitPrice = $machine->price_4_pass;
                 elseif ($item->pass == 6) $unitPrice = $machine->price_6_pass;
                 
                 $price = ($item->meters ?? 0) * $unitPrice;
            }

        } elseif ($type === 'rollpress') {
            $item = \App\Models\Rollpress::find($itemId);
            if (!$item) return 0;
            $price = $item->price ?? 0;
        }

        return round($price, 2);
    }
}
