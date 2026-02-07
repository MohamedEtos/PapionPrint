@if(isset($cartItems) && $cartItems->count() > 0)
    @foreach($cartItems as $cItem)
        @php
            // Get product image
            $imgPath = null;
            if ($cItem->itemable_type == 'App\Models\Stras') {
                $imgPath = $cItem->itemable->image_path ?? null;
            } elseif ($cItem->itemable_type == 'App\Models\Tarter') {
                $imgPath = $cItem->itemable->image_path ?? null;
            } elseif ($cItem->itemable_type == 'App\Models\Printers') {
                $imgObj = $cItem->itemable->ordersImgs->first();
                $imgPath = $imgObj ? $imgObj->path : null;
            }
            $imgUrl = $imgPath ? asset('storage/' . $imgPath) : null;
        @endphp
        <div class="media align-items-center justify-content-between">
            <a class="d-flex" href="{{ route('invoice.create') }}">
                <div class="media-left d-flex justify-content-center align-items-center" style="width: 50px; height: 50px;">
                    @if($imgUrl)
                        <img src="{{ $imgUrl }}" alt="Product" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                    @else
                        <i class="feather icon-file-text font-medium-5"></i>
                    @endif
                </div>
                <div class="media-body ml-2">
                    <span class="item-title text-truncate text-bold-500 d-block mb-50">
                        {{ class_basename($cItem->itemable_type) }} #{{ $cItem->itemable_id }}
                    </span>
                    <span class="item-desc font-small-2 text-truncate d-block">
                        {{ $cItem->custom_price }} EGP
                    </span>
                </div>
            </a>
            <div class="media-right " style="padding: 0 10px;">
                <a href="#" class="text-danger remove-cart-item" data-id="{{ $cItem->id }}">
                    <i class="feather icon-trash-2"></i>
                </a>
            </div>
        </div>
    @endforeach
@else
     <div class="p-2 text-center">Your Cart Is Empty.</div>
@endif
