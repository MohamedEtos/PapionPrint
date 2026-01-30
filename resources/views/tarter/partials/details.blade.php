<tr>
    <td class="product-img">
        @if($Record->image_path)
            <img src="{{ asset('storage/'.$Record->image_path) }}" alt="Img" style="width:100px; height:auto;">
        @else
            <img src="{{ asset('core/images/elements/apple-watch.png') }}" alt="Placeholder" style="width:100px; height:auto;">
        @endif
    </td>
    <td class="product-name">{{ $Record->customer->name ?? '-' }} </td>
    <td class="product-category">
        {{ $Record->height ?? '-' }} x {{ $Record->width ?? '-' }}
    </td>
    <td class="product-category">
        @if($Record->cards_count && $Record->pieces_per_card)
            {{ $Record->cards_count * $Record->pieces_per_card }}
            <br>
            <small class="text-muted">({{ $Record->cards_count }} كارت)</small>
        @else
            <span class="text-muted">لم يحسب</span>
        @endif
    </td>
    <td class="product-category">{{ $Record->machine_time }} دقيقة</td>
    <td class="product-category">
        @foreach($Record->layers as $layer)
            <span class="badge badge-primary">{{ $layer->size }}: {{ $layer->count }}</span>
        @endforeach
    </td>
    <td class="product-category">{{ $Record->notes ?? '-' }}</td>
    <td class="product-price">{{ $Record->created_at ? $Record->created_at->locale('ar')->diffForHumans() : '-' }}</td>
</tr>
