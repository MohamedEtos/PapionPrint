@extends('layouts.app')

@section('css')
    @vite([
        'resources/core/vendors/css/tables/datatable/datatables.min.css',
        'resources/core/vendors/css/tables/datatable/extensions/dataTables.checkboxes.css',
        'resources/core/css-rtl/core/menu/menu-types/vertical-menu.css',
        'resources/core/css-rtl/core/colors/palette-gradient.css',
        'resources/core/css-rtl/plugins/file-uploaders/dropzone.css',
        'resources/core/css-rtl/pages/data-list-view.css',
        'resources/core/css-rtl/custom-rtl.css',
        'resources/core/vendors/css/file-uploaders/dropzone.min.css',
    ])
@endsection

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">الحسابات</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                                    <li class="breadcrumb-item active">الحسابات</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="content-body">
                <section id="basic-datatable">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">جدول الحسابات</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <div class="table-responsive">
                                            <table class="table zero-configuration" id="accounts-table">
                                                <thead>
                                                    <tr>
                                                        <th>رقم الطلب</th>
                                                        <th>العميل</th>
                                                        <th>الماكينة</th>
                                                        <th>الأمتار</th>
                                                        <th>سعر المتر</th>
                                                        <th>الاجمالي</th>
                                                        <th>التاريخ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($orders as $order)
                                                        @php
                                                            $priceRecord = $order->printingprices;
                                                            $pricePerMeter = $priceRecord ? $priceRecord->pricePerMeter : 0;
                                                            $totalPrice = $priceRecord ? $priceRecord->totalPrice : 0;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $order->orderNumber ?? $order->id }}</td>
                                                            <td>{{ $order->customers->name ?? 'غير محدد' }}</td>
                                                            <td>{{ $order->machines->name ?? 'غير محدد' }}</td>
                                                            <td>
                                                                <span class="meters-display" data-id="{{ $order->id }}">{{ $order->meters }}</span>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" class="form-control price-input price-per-meter" 
                                                                    data-id="{{ $order->id }}" 
                                                                    data-type="pricePerMeter"
                                                                    value="{{ $pricePerMeter }}" 
                                                                    style="width: 100px;">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" class="form-control price-input total-price" 
                                                                    data-id="{{ $order->id }}" 
                                                                    data-type="totalPrice"
                                                                    value="{{ $totalPrice }}" 
                                                                    style="width: 100px;">
                                                            </td>
                                                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('core/vendors/js/extensions/dropzone.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.checkboxes.min.js') }}"></script>
    <script src="{{ asset('core/js/scripts/ui/data-list-view.js') }}"></script>
    <script src="{{ asset('core/js/scripts/modal/components-modal.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('.zero-configuration').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json"
                }
            });

            // Handle Input Change
            $(document).on('change', '.price-input', function() {
                let input = $(this);
                let orderId = input.data('id');
                let type = input.data('type');
                let value = parseFloat(input.val());
                let row = input.closest('tr');
                
                // Optimistic UI Update (Calculate locally first)
                let meters = parseFloat(row.find('.meters-display').text()) || 0;
                
                if (type === 'pricePerMeter') {
                    let newTotal = (value * meters).toFixed(2);
                    row.find('.total-price').val(newTotal);
                } else if (type === 'totalPrice') {
                     if (meters > 0) {
                        let newPricePerMeter = (value / meters).toFixed(2);
                        row.find('.price-per-meter').val(newPricePerMeter);
                     }
                }

                // Send AJAX Request
                $.ajax({
                    url: "{{ route('accounts.update_price') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        order_id: orderId,
                        field: type,
                        value: value
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message, 'تم الحفظ');
                            // Update with server values to be sure
                            if (response.updated_fields) {
                                if (response.updated_fields.other_field == 'totalPrice') {
                                     row.find('.total-price').val(parseFloat(response.updated_fields.other_value).toFixed(2));
                                } else if (response.updated_fields.other_field == 'pricePerMeter') {
                                     row.find('.price-per-meter').val(parseFloat(response.updated_fields.other_value).toFixed(2));
                                }
                            }
                        } else {
                            toastr.error('حدث خطأ غير متوقع', 'خطأ');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('فشل تحديث البيانات', 'خطأ');
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection
