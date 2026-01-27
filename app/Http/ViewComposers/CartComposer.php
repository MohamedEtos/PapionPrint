<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;

class CartComposer
{
    public function compose(View $view)
    {
        $cartCount = 0;
        $cartItems = collect([]);
        $cartTotal = 0;

        if (Auth::check()) {
            $invoice = Invoice::with('items.itemable')->where('user_id', Auth::id())
                        ->where('status', 'draft')
                        ->first();
            
            if ($invoice) {
                $cartCount = $invoice->items->count();
                $cartItems = $invoice->items;
                $cartTotal = $invoice->items->sum('custom_price');
            }
        }

        $view->with('cartCount', $cartCount);
        $view->with('cartItems', $cartItems);
        $view->with('cartTotal', $cartTotal);
    }
}
