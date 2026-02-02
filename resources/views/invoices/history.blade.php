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
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">سجل الفواتير</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">الفواتير</a>
                                    </li>
                                    <li class="breadcrumb-item active">السجل
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Data list view starts -->
                <section id="data-list-view" class="data-list-view-header">
                    <!-- DataTable starts -->
                    <div class="table-responsive">
                        <table class="table data-list-view">
                            <thead>
                                <tr>
                                    <th>رقم الفاتورة</th>
                                    <th>اسم العميل</th>
                                    <th>عدد العناصر</th>
                                    <th>الإجمالي</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!-- DataTable ends -->
                </section>
                <!-- Data list view end -->
            </div>
        </div>
    </div>
    <!-- Invoice Details Modal -->
    <div class="modal fade" id="invoice-details-modal" tabindex="-1" role="dialog" aria-labelledby="invoiceDetailsModalLabel">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoiceDetailsModalLabel">تفاصيل الفاتورة والطلب</h5>
                    <br>
                    <h5 class="modal-title" id="invoice-client-name"></h5>
                   
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="invoice-details-body">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-success mr-1" onclick="resendWhatsApp()">
                        <i class="fa fa-whatsapp"></i> إعادة إرسال واتساب
                    </button>
                    <button type="button" class="btn btn-outline-secondary mr-1" onclick="window.print()">
                        <i class="feather icon-printer"></i> طباعة
                    </button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- WhatsApp Review Modal (Resend) -->
    <div class="modal fade" id="whatsappPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">مراجعة رسالة الواتساب (إعادة إرسال)</h5>
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
                    <button type="button" class="btn btn-success" id="confirm-resend-whatsapp">
                        <i class="fa fa-whatsapp"></i> إرسال عبر واتساب
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- END: Content-->
    
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #invoice-details-modal, #invoice-details-modal * {
                visibility: visible;
            }
            #invoice-details-modal {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .modal-footer {
                display: none; /* Hide buttons in print */
            }
        }
    </style>
@endsection

@section('js')
        <script src="{{ asset('core/vendors/js/extensions/dropzone.min.js') }}"></script>
        {{-- JSZip for Excel Export --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.checkboxes.min.js') }}"></script>
        {{-- <script src="{{ asset('core/js/scripts/ui/data-list-view.js') }}"></script> --}}

        <script>
            var assetPath = "{{ asset('') }}";
            
            function resendWhatsApp() {
                var text = $('#details-whatsapp-text').val();
                if(!text) {
                    alert('لا يوجد نص فاتورة جاهز');
                    return;
                }
                $('#whatsapp-message-preview').val(text);
                $('#whatsappPreviewModal').modal('show');
            }
            
            $('#confirm-resend-whatsapp').click(function() {
                var text = $('#whatsapp-message-preview').val();
                var url = "https://wa.me/?text=" + encodeURIComponent(text);
                window.open(url, '_blank');
                $('#whatsappPreviewModal').modal('hide');
            });
        </script>
        
        @vite('resources/js/pages/invoice_history.js')
@endsection

