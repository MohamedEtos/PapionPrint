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
    <style>
        .product-action .action-restore, .product-action .action-delete-forever {
            cursor: pointer;
            margin-right: 5px;
        }
        .product-action .action-restore {
            color: #28c76f;
        }
        .product-action .action-delete-forever {
            color: #ea5455;
        }
    </style>
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
                            <h2 class="content-header-title float-left mb-0">سلة المحذوفات</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#"> الطباعة</a>
                                    </li>
                                    <li class="breadcrumb-item active">سلة المحذوفات
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                     <a href="{{ route('print_log') }}" class="btn btn-primary pull-right"> <i class="feather icon-arrow-right"></i>  العودة للطلبات</a>
                </div>
            </div>
            <div class="content-body">
                <!-- Data list view starts -->
                <section id="data-thumb-view" class="data-thumb-view-header">
                    
                    <!-- dataTable starts -->
                    <div class="table-responsive">
                        <table class="table data-thumb-view">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>صورة</th>
                                    <th>اسم العميل</th>
                                    <th>نوع الطباعة</th>
                                    <th>الامتار</th>
                                    <th>الحاله</th>
                                    <th>تاريخ الحذف</th>
                                    <th>اجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($Orders as $Order)

                                <tr>
                                    <td></td>

                                    <td class="product-img">
                                        <input type="hidden" class="order_id" value="{{ $Order->id }}">
                                        <img style="height: 50px;" src="{{ $Order->ordersImgs->first() ? asset('storage/'.$Order->ordersImgs->first()->path) : asset('core/images/elements/apple-watch.png') }}" alt="Img placeholder">
                                    </td>
                                    
                                    <td class="product-name">{{ $Order->customers->name }} </td>
                                    <td class="product-category">{{ $Order->machines->name}} {{ $Order->pass}} pass</td>
                                    <td class="product-category"><b>{{ $Order->meters }}</b></td>

                                    <td>
                                        <div class="chip chip-danger">
                                            <div class="chip-body">
                                                <div class="chip-text">محذوف</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="product-price" title="{{ $Order->deleted_at }}">{{ $Order->deleted_at->locale('ar')->diffForHumans() }}</td>
                                    <td class="product-action">
                                        <span class="action-restore" data-url="{{ route('printers.restore', $Order->id) }}" title="استرجاع"><i class="feather icon-rotate-ccw"></i></span>
                                        <span class="action-delete-forever" data-url="{{ route('printers.force_delete', $Order->id) }}" title="حذف نهائي"><i class="feather icon-trash-2"></i></span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
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
        <script src="{{ asset('core/js/scripts/ui/data-list-view.js') }}"></script>

        @vite('resources/js/pages/trash.js')
@endsection
