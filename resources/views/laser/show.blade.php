@extends('layouts.app')

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>تفاصيل طلب الليزر #{{ $order->id }}</h5>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm mr-1"><i class="feather icon-printer"></i> طباعة</button>
            <button onclick="openWhatsAppModal()" class="btn btn-success btn-sm"><i class="fa fa-whatsapp"></i> واتساب</button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>معلومات العميل</h6>
                <p><strong>الاسم:</strong> {{ $order->customer->name ?? '-' }}</p>
                <p><strong>التاريخ:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                <hr>
                <h6>التفاصيل</h6>
                <p><strong>الخامة:</strong> {{ $order->material->name ?? '-' }}</p>
                <p><strong>إضافة سيليكون:</strong> 
                    @if($order->add_ceylon)
                        <span class="badge badge-success">نعم</span>
                    @else
                        <span class="badge badge-secondary">لا</span>
                    @endif
                </p>
                <p><strong>الارتفاع:</strong> {{ $order->height }}</p>
                <p><strong>العرض:</strong> {{ $order->width }}</p>
                <p><strong>عدد القطع المطلوبة:</strong> {{ $order->required_pieces }}</p>
                <p><strong>قطع في الجزء (Pieces/Section):</strong> {{ $order->pieces_per_section }}</p>
                <p><strong>التكلفة الإجمالية:</strong> {{ $order->total_cost }} ج.م</p>
                <p><strong>تكلفة التشغيل:</strong> {{ $order->operating_cost }} ج.م</p>
            </div>
            <div class="col-md-6">
                <h6>ملاحظات</h6>
                <p>{{ $order->notes ?? 'لا توجد ملاحظات' }}</p>
                
                 <h6>صورة التصميم</h6>
                 @if($order->image_path)
                    <img src="{{ asset('storage/' . $order->image_path) }}" class="img-fluid rounded" style="max-height: 400px;">
                 @else
                    <p class="text-muted">لا توجد صورة</p>
                 @endif
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
        var customerName = "{{ $order->customer->name ?? 'عميل' }}";
        var id = "{{ $order->id }}";
        var total = "{{ number_format($order->total_cost, 2) }}";
        var details = "ليزر - خامة: {{ $order->material->name ?? '-' }}";
        var imgUrl = "{{ $order->image_path ? asset('storage/' . $order->image_path) : '' }}";
        
        var text = "*فاتورة للسيد/ة* " + customerName + "\n";
        text += "━━━━━━━━━━━━━━━━━━\n";
        text += "--- *ليزر* ---\n";
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
