@extends('layouts.app')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>تفاصيل طلب الاستراس #{{ $stras->id }}</h5>
                <div>
                    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm mr-1"><i class="feather icon-printer"></i> طباعة</button>
                    <button onclick="openWhatsAppModal()" class="btn btn-success btn-sm"><i class="fa fa-whatsapp"></i> واتساب</button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>معلومات العميل</h6>
                        <p><strong>الاسم:</strong> {{ $stras->customer->name ?? '-' }}</p>
                        <p><strong>التاريخ:</strong> {{ $stras->created_at->format('Y-m-d H:i') }}</p>
                        <hr>
                         @php
                            // Operating Cost Logic (Placeholder or Fetch if exists)
                            $operatingCost = 0; // Stras might not have machine time logic yet
                            
                            // Calculate Total
                            // Assuming Layer Price is per piece unit, total = (layer_price * layer_count) * cards? 
                            // Or just layer_price * layer_count?
                            // Usually: pieces_per_card affects consumption, but if layer defines "count" per card?
                            // Let's assume standard: Total = (Sum of Layers Price) * Cards Count
                            
                            $layersTotal = $stras->layers->sum(function($layer) {
                                return ($layer->price ?? 0) * ($layer->count ?? 0);
                            });
                            
                            $grandTotal = ($layersTotal * $stras->cards_count) + $operatingCost;
                        @endphp
                        <h6>التفاصيل</h6>
                        <p><strong>الارتفاع:</strong> {{ $stras->height }}</p>
                        <p><strong>العرض:</strong> {{ $stras->width }}</p>
                        <p><strong>عدد الكروت:</strong> {{ $stras->cards_count }}</p>
                        <p><strong>قطع لكل كارت:</strong> {{ $stras->pieces_per_card }}</p>
                        <p><strong>الإجمالي (قطع):</strong> {{ $stras->cards_count * $stras->pieces_per_card }}</p>
                        <p><strong>تكلفة التشغيل:</strong> {{ number_format($operatingCost, 2) }} ج.م</p>
                        <p><strong>إجمالي السعر:</strong> {{ number_format($grandTotal, 2) }} ج.م</p>
                    </div>
                    <div class="col-md-6">
                        <h6>المراحل (Layers)</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>المقاس</th>
                                        <th>العدد</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stras->layers as $layer)
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
                         @if($stras->image_path)
                            <img src="{{ asset('storage/' . $stras->image_path) }}" class="img-fluid rounded" style="max-height: 400px;">
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
        var customerName = "{{ $stras->customer->name ?? 'عميل' }}";
        var id = "{{ $stras->id }}";
        var total = "{{ number_format($grandTotal, 2) }}";
        var details = "استراس - ارتفاع: {{ $stras->height }} عرض: {{ $stras->width }} كروت: {{ $stras->cards_count }}";
        var imgUrl = "{{ $stras->image_path ? asset('storage/' . $stras->image_path) : '' }}";
        
        var text = "*فاتورة للسيد/ة* " + customerName + "\n";
        text += "━━━━━━━━━━━━━━━━━━\n";
        text += "--- *استراس* ---\n";
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
         var phone = ""; 
        var url = "https://wa.me/" + phone + "?text=" + encodeURIComponent(text);
        window.open(url, '_blank');
        $('#whatsappPreviewModal').modal('hide');
    });
</script>
@endsection
