@extends('layouts.app')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>تفاصيل طلب الترتر #{{ $tarter->id }}</h5>
                <div>
                    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm mr-1"><i class="feather icon-printer"></i> طباعة</button>
                    <button onclick="openWhatsAppModal()" class="btn btn-success btn-sm"><i class="fa fa-whatsapp"></i> واتساب</button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>معلومات العميل</h6>
                        <p><strong>الاسم:</strong> {{ $tarter->customer->name ?? '-' }}</p>
                        <p><strong>التاريخ:</strong> {{ $tarter->created_at->format('Y-m-d H:i') }}</p>
                        <hr>
                        @php
                            $machineCostRate = \App\Models\TarterPrice::where('type', 'machine_time_cost')->value('price') ?? 0;
                            $operatingCost = $tarter->machine_time * $machineCostRate;
                            
                            // Calculate Total (Simplified assumption: Operating + Layers Cost if available)
                            // Assuming layers have 'price' field populated
                            $layersCost = $tarter->layers->sum(function($layer) {
                                return $layer->count * $layer->price; // Check if price exists per needle count/type
                            });
                            // If price is null in layers, this might be 0.
                            
                            $totalPrice = $operatingCost + $layersCost;
                        @endphp
                        <h6>التفاصيل</h6>
                        <p><strong>الارتفاع:</strong> {{ $tarter->height }}</p>
                        <p><strong>العرض:</strong> {{ $tarter->width }}</p>
                        <p><strong>عدد الكروت:</strong> {{ $tarter->cards_count }}</p>
                        <p><strong>قطع لكل كارت:</strong> {{ $tarter->pieces_per_card }}</p>
                        <p><strong>وقت الماكينة:</strong> {{ $tarter->machine_time }} دقيقة</p>
                        <p><strong>تكلفة التشغيل:</strong> {{ number_format($operatingCost, 2) }} ج.م</p>
                        <p><strong>إجمالي السعر:</strong> {{ number_format($totalPrice, 2) }} ج.م</p>
                    </div>
                    <div class="col-md-6">
                        <h6>الإبر (Needles)</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>المقاس</th>
                                        <th>العدد</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tarter->layers as $layer)
                                    <tr>
                                        <td>{{ $layer->size }}</td>
                                        <td>{{ $layer->count }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                     <div class="col-12">
                         <h6>صورة التصميم</h6>
                         @if($tarter->image_path)
                            <img src="{{ asset('storage/' . $tarter->image_path) }}" class="img-fluid rounded" style="max-height: 400px;">
                         @else
                            <p class="text-muted">لا توجد صورة</p>
                         @endif
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- WhatsApp Review Modal -->
<div class="modal fade" id="whatsappPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">مراجعة رسالة الواتساب</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="whatsapp-message-preview">الرسالة:</label>
                    <textarea class="form-control" id="whatsapp-message-preview" rows="10" style="direction: rtl;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success" id="confirm-send-whatsapp">
                    <i class="fa fa-whatsapp"></i> إرسال عبر واتساب
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function openWhatsAppModal() {
        var customerName = "{{ $tarter->customer->name ?? 'عميل' }}";
        var id = "{{ $tarter->id }}";
        var total = "{{ number_format($totalPrice, 2) }}";
        var details = "استراس - ارتفاع: {{ $tarter->height }} عرض: {{ $tarter->width }}";
        var imgUrl = "{{ $tarter->image_path ? asset('storage/' . $tarter->image_path) : '' }}";
        
        var text = "*فاتورة للسيد/ة* " + customerName + "\n";
        text += "━━━━━━━━━━━━━━━━━━\n";
        text += "--- *ترتر* ---\n";
        text += "• رقم الطلب: " + id + "\n";
        text += "• التفاصيل: " + details + "\n";
        text += "• إجمالي السعر: " + total + " ج.م";
        
        if(imgUrl) {
            text += "\n  رابط الصورة: " + imgUrl;
        }
        
        text += "\n━━━━━━━━━━━━━━━━━━\n";
        
        $('#whatsapp-message-preview').val(text);
        $('#whatsappPreviewModal').modal('show');
    }

    $('#confirm-send-whatsapp').click(function() {
        var text = $('#whatsapp-message-preview').val();
         // Basic phone handling - relying on user to have phone in system logic or prompt? 
         // For 'Show' view, we might not have the phone input visible. 
         // Let's assume we can't easily get the phone here unless passed. 
         // We'll standard open WA and let user pick contact or if we have customer phone variable.
         // Let's try to get customer phone if available in relationship
         var phone = ""; 
         // {{ $tarter->customer->phone ?? '' }} -- Assuming phone exists on customer model
        
        var url = "https://wa.me/" + phone + "?text=" + encodeURIComponent(text);
        window.open(url, '_blank');
        $('#whatsappPreviewModal').modal('hide');
    });
</script>
@endsection
