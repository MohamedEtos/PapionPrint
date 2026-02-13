@if($Record)
    <tr>
        <td class="product-img">
            @if($Record->image_path)
                <img src="{{ asset('storage/'.$Record->image_path) }}" alt="Img" style="width: 100px; height: 100px; object-fit: cover;">
            @else
                <span class="text-muted">No Img</span>
            @endif
        </td>
        <td>{{ $Record->customer->name ?? '-' }}</td>
        <td><span class='badge {{ $Record->source == 'ap_group' ? 'badge-primary' : 'badge-warning' }}'>{{ $Record->source == 'ap_group' ? 'AP Group' : 'العميل' }}</span></td>
        <td>{{ $Record->height ?? 0 }} sm</td>
        <td>{{ $Record->width ?? 0 }} sm</td>
        <td>{{ $Record->required_pieces ?? 0 }}</td>
        <td>
           <div class="chip chip-success">
                <div class="chip-body">
                    <div class="chip-text">تقطيع: {{ $Record->section_count }} مقطع</div>
                </div>
            </div>
             @if($Record->add_ceylon)
            <div class="chip chip-warning">
                <div class="chip-body">
                    <div class="chip-text">سيليكون</div>
                </div>
            </div>
            @endif
        </td>
        <td>{{ $Record->notes }}</td>
        <td>{{ $Record->created_at->format('Y-m-d') }}</td>
    </tr>
@else
    <tr>
        <td colspan="8" class="text-center">لا توجد تفاصيل</td>
    </tr>
@endif
