@extends('layouts.app')

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>تفاصيل طلب الطباعة #{{ $printer->id }}</h5>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm mr-1"><i class="feather icon-printer"></i> طباعة</button>
            <button onclick="openWhatsAppModal()" class="btn btn-success btn-sm"><i class="fa fa-whatsapp"></i> واتساب</button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>معلومات العميل</h6>
                <p><strong>الاسم:</strong> {{ $printer->customers->name ?? '-' }}</p>
                <p><strong>التاريخ:</strong> {{ $printer->created_at->format('Y-m-d H:i') }}</p>
                <p><strong>الحالة:</strong> <span class="badge badge-info">{{ $printer->status }}</span></p>
                <hr>
                <h6>التفاصيل</h6>
                <p><strong>الماكينة:</strong> {{ $printer->machines->name ?? '-' }}</p>
                <p><strong>نوع القماش:</strong> {{ $printer->fabric_type ?? '-' }}</p>
                <p><strong>الامتار:</strong> {{ $printer->meters }}</p>
                <p><strong>Pass:</strong> {{ $printer->pass }}</p>
                <p><strong>الطول:</strong> {{ $printer->fileHeight }}</p>
                <p><strong>العرض:</strong> {{ $printer->fileWidth }}</p>
                <p><strong>نسخ:</strong> {{ $printer->fileCopies }}</p>
                <p><strong>صور في النسخة:</strong> {{ $printer->picInCopies }}</p>
                
                @php
                    $totalPrice = $printer->printingprices->totalPrice ?? 0;
                @endphp
                <p><strong>اجمالي السعر:</strong> {{ number_format($totalPrice, 2) }} ج.م</p>
            </div>
            <div class="col-md-6">
                <h6>ملاحظات</h6>
                <p>{{ $printer->notes ?? 'لا توجد ملاحظات' }}</p>
                
                 <h6>صور الطلب</h6>
                 <div class="row">
                 @if($printer->ordersImgs->count() > 0)
                    @foreach($printer->ordersImgs as $img)
                        <div class="col-md-6 mb-3">
                            <a href="{{ asset('storage/' . $img->path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $img->path) }}" class="img-fluid rounded" style="max-height: 200px;">
                            </a>
                        </div>
                    @endforeach
                 @else
                    <div class="col-12"><p class="text-muted">لا توجد صور</p></div>
                 @endif
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
        var customerName = "{{ $printer->customers->name ?? 'عميل' }}";
        var id = "{{ $printer->id }}";
        var total = "{{ number_format($totalPrice, 2) }}";
        var details = "طباعة - ماكينة: {{ $printer->machines->name ?? '-' }} | أمتار: {{ $printer->meters }}";
        
        // Use first image if available
        @php
            $firstImg = $printer->ordersImgs->first();
            $imgUrl = $firstImg ? asset('storage/' . $firstImg->path) : '';
        @endphp
        var imgUrl = "{{ $imgUrl }}";
        
        var text = "*فاتورة للسيد/ة* " + customerName + "\n";
        text += "━━━━━━━━━━━━━━━━━━\n";
        text += "--- *طباعة* ---\n";
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
