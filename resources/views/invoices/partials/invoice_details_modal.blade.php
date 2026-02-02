<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>صورة</th>
                <th>النوع</th>
                <th>التفاصيل</th>
                <th>الكمية</th>
                <th>سعر الوحدة</th>
                <th>الإجمالي</th>
                <th>حالة الإرسال</th>
                <th>تاريخ الإرسال</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                @php
                    // Image Logic
                    $imgPath = null;
                    if ($item->order_type === 'App\Models\Printers' && $item->itemable) {
                        $imgObj = $item->itemable->ordersImgs->first();
                        $imgPath = $imgObj ? $imgObj->path : null;
                    } elseif (in_array($item->order_type, ['App\Models\Stras', 'App\Models\Tarter', 'App\Models\LaserOrder']) && $item->itemable) {
                        $imgPath = $item->itemable->image_path;
                    }
                    $imgUrl = $imgPath ? asset('storage/' . $imgPath) : null;

                    // Type Mapping
                    $typeMap = [
                        'App\Models\Stras' => 'استراس',
                        'App\Models\Tarter' => 'ترتر',
                        'App\Models\Printers' => 'طباعة',
                        'App\Models\Rollpress' => 'مكبس',
                        'App\Models\LaserOrder' => 'ليزر'
                    ];
                    $typeLabel = $typeMap[$item->order_type] ?? class_basename($item->order_type);
                    
                    // Link Logic
                    $linkUrl = null;
                    if ($item->itemable) {
                        if ($item->order_type === 'App\Models\Stras') {
                            $linkUrl = route('stras.show', $item->itemable->id);
                        } elseif ($item->order_type === 'App\Models\Tarter') {
                            $linkUrl = route('tarter.show', $item->itemable->id);
                        } elseif ($item->order_type === 'App\Models\Printers') {
                            $linkUrl = route('printers.show', $item->itemable->id);
                        } elseif ($item->order_type === 'App\Models\LaserOrder') {
                            $linkUrl = route('laser.show', $item->itemable->id);
                        }
                    }

                    // Status Label
                    $statusMap = [
                        'pending' => '<span class="badge badge-warning">قيد الانتظار</span>',
                        'sent' => '<span class="badge badge-info">تم الإرسال</span>',
                        'delivered' => '<span class="badge badge-success">تم التسليم</span>'
                    ];
                    $statusLabel = $statusMap[$item->sent_status] ?? '-';
                    
                    // Details Construction
                    $details = '-';
                     if ($item->itemable) {
                        if ($item->order_type === 'App\Models\Stras') {
                            $stras = $item->itemable;
                            $layersInfo = $stras->layers->map(function($l) {
                                return $l->size . ':' . $l->count;
                            })->implode(' | ');
                            $details = 'مراحل: ' . $stras->layers->count() . ' - ' . $layersInfo;
                        } elseif ($item->order_type === 'App\Models\Tarter') {
                             $tarter = $item->itemable;
                             $layersInfo = $tarter->layers->map(function($l) {
                                return $l->size . ':' . $l->count;
                            })->implode(' | ');
                            $details = 'ابر: ' . $tarter->layers->count() . ' - ' . $layersInfo;
                        } elseif ($item->order_type === 'App\Models\LaserOrder') {
                             $laser = $item->itemable;
                             $details = $laser->material->name ?? '-';
                             if ($laser->add_ceylon) {
                                 $details .= ' - سيليكون';
                             }
                        } elseif ($item->order_type === 'App\Models\Rollpress') {
                             $details = 'قماش: ' . ($item->itemable->fabrictype ?? '-');
                        } elseif ($item->order_type === 'App\Models\Printers') {
                             $details = 'ماكينة: ' . ($item->itemable->machines->name ?? '-');
                        }
                    }
                @endphp
                <tr>
                    <td>
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        @else
                            <i class="feather icon-image text-muted"></i>
                        @endif
                    </td>
                    <td>
                        @if($linkUrl)
                            <a href="{{ $linkUrl }}" target="_blank" class="text-primary font-weight-bold">
                                {{ $typeLabel }} <i class="feather icon-external-link small"></i>
                            </a>
                        @else
                            {{ $typeLabel }}
                        @endif
                    </td>
                    <td>{{ $details }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->total_price, 2) }}</td>
                    <td>{!! $statusLabel !!}</td>
                    <td>{{ $item->sent_date ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">الإجمالي الكلي:</th>
                <th>{{ number_format($items->sum('total_price'), 2) }} ج.م</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>
    </table>
</div>

@php
    $firstItem = $items->first();
    $custName = $firstItem->customer_name ?? 'عميل';
    $grandTotal = $items->sum('total_price');
    
    // Construct WhatsApp Message
    $msg = "*فاتورة للسيد/ة* " . $custName . "\n";
    $msg .= "━━━━━━━━━━━━━━━━━━\n";
    
    foreach($items as $item) {
        $tLabel = '-';
        $itemDetails = '-';
        
        // Mapping (Repeat logic or rely on view variables if scoped? No, vars inside loop above are scoped. Need to re-map or grab better)
        // Simplification: Re-use generic logic or copied logic for message generation
         $typeMap = [
            'App\Models\Stras' => 'استراس',
            'App\Models\Tarter' => 'ترتر',
            'App\Models\Printers' => 'طباعة',
            'App\Models\Rollpress' => 'مكبس',
            'App\Models\LaserOrder' => 'ليزر'
        ];
        $tLabel = $typeMap[$item->order_type] ?? 'منتج';
        
         // Details
         $d = '-';
         if ($item->itemable) {
            if ($item->order_type === 'App\Models\Stras') {
                $stras = $item->itemable;
                $layersInfo = $stras->layers->map(function($l) { return $l->size . ':' . $l->count; })->implode(' | ');
                $d = 'مراحل: ' . $stras->layers->count() . ' - ' . $layersInfo;
            } elseif ($item->order_type === 'App\Models\Tarter') {
                 $tarter = $item->itemable;
                 $layersInfo = $tarter->layers->map(function($l) { return $l->size . ':' . $l->count; })->implode(' | ');
                $d = 'ابر: ' . $tarter->layers->count() . ' - ' . $layersInfo;
            } elseif ($item->order_type === 'App\Models\LaserOrder') {
                 $laser = $item->itemable;
                 $d = $laser->material->name ?? '-';
                 if ($laser->add_ceylon) $d .= ' - سيليكون';
            } elseif ($item->order_type === 'App\Models\Rollpress') {
                 $d = 'قماش: ' . ($item->itemable->fabrictype ?? '-');
            } elseif ($item->order_type === 'App\Models\Printers') {
                 $d = 'ماكينة: ' . ($item->itemable->machines->name ?? '-');
            }
        }
        
        $msg .= "\n--- *" . $tLabel . "* ---\n";
        $msg .= "• " . $d . "\n";
        $msg .= "  الكمية: " . number_format($item->quantity, 2) . " | الاجمالي: " . number_format($item->total_price, 2) . " ج.م\n";
        
        // Image Link
        $iPath = null;
        if ($item->order_type === 'App\Models\Printers' && $item->itemable) {
            $imgObj = $item->itemable->ordersImgs->first();
            $iPath = $imgObj ? $imgObj->path : null;
        } elseif (in_array($item->order_type, ['App\Models\Stras', 'App\Models\Tarter', 'App\Models\LaserOrder']) && $item->itemable) {
            $iPath = $item->itemable->image_path;
        }
        if($iPath) {
             $msg .= "  رابط الصورة: " . asset('storage/' . $iPath) . "\n";
        }
    }
    
    $msg .= "\n━━━━━━━━━━━━━━━━━━\n";
    $msg .= "*الإجمالي:* " . number_format($grandTotal, 2) . " ج.م";
@endphp

<input type="hidden" id="details-customer-name" value="{{ $custName }}">
<textarea id="details-whatsapp-text" style="display:none;">{{ $msg }}</textarea>
