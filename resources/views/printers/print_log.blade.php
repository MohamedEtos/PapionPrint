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
                            <h2 class="content-header-title float-left mb-0">سجل الطباعة</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#"> الطباعة</a>
                                    </li>
                                    <li class="breadcrumb-item active">سجل الطباعة
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Data list view starts -->
                <section id="data-thumb-view" class="data-thumb-view-header">

                    <div class=" hover_action action-btns d-none">
                        <div class="btn-dropdown mr-1 mb-1">
                            <div class="btn-group dropdown actions-dropodown">
                                <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light close_modal" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item text-danger bulk-delete-btn" href="#"><i class="feather icon-trash "></i>حذف</a>
                                    <a class="dropdown-item" href="#"><i class="feather icon-archive"></i>ارشفة</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Options -->
                    <div class="row mb-2">
                        <div class="col-md-2">
                            <label>من تاريخ:</label>
                            <input type="date" id="min-date" class="form-control" name="min">
                        </div>
                        <div class="col-md-2">
                            <label>إلى تاريخ:</label>
                            <input type="date" id="max-date" class="form-control" name="max">
                        </div>
                    </div>

                    <!-- dataTable starts -->
                    <div class="table-responsive">
                        <table class="table data-thumb-view">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>اجراءات</th>
                                    <th>صورة</th>
                                    <th>رقم الطلب</th>
                                    <th>اسم العميل</th>
                                    <th>الماكينة</th>
                                    <th>الطول</th>
                                    <th>العرض</th>
                                    <th>النسخ</th>
                                    <th>الصور/نسخة</th>
                                    <th>الامتار</th>
                                    {{-- <th>الحاله</th> --}}
                                    <!-- <th>حالة الدفع</th> -->
                                    <th>المصمم</th>
                                     <th>المشغل</th> 
                                    <th>الملاحظات</th>
                                    <th>تاريخ الإنشاء</th>
                                    {{-- <th>تاريخ التحديث</th> --}}
                                    <th>تاريخ الانتهاء</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rows will be populated via AJAX -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="10" style="text-align:left">الإجمالي:</th>
                                    <th id="total-meters">0 متر</th>
                                    <th colspan="5"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- dataTable ends -->

                </section>
                <!-- Data list view end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

@endsection

@section('js')
        <script src="{{ asset('core/vendors/js/extensions/dropzone.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.checkboxes.min.js') }}"></script>
        {{-- <script src="{{ asset('core/js/scripts/ui/data-list-view.js') }}"></script> --}}

        @vite('resources/js/pages/PrinterLog.js')


@endsection
