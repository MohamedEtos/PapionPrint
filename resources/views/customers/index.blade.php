@extends('layouts.app')

@section('css')
    @vite([
        'resources/core/vendors/css/tables/datatable/datatables.min.css',
        'resources/core/vendors/css/tables/datatable/extensions/dataTables.checkboxes.css',
        'resources/core/css-rtl/core/menu/menu-types/vertical-menu.css',
        'resources/core/css-rtl/core/colors/palette-gradient.css',
        'resources/core/css-rtl/pages/data-list-view.css',
        'resources/core/css-rtl/custom-rtl.css',
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
                            <h2 class="content-header-title float-left mb-0">العملاء</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item active"> قائمة العملاء
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
                    
                    <!-- dataTable starts -->
                    <div class="table-responsive">
                        <table class="table data-thumb-view">
                            <thead>
                                <tr>
                                    <th>اسم العميل</th>
                                    <th>رقم الهاتف</th>
                                    <th>طلبات استراس</th>
                                    <th>طلبات ترتر</th>
                                    <th>طلبات ليزر</th>
                                    <th>طلبات طباعة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                <tr>
                                    <td class="product-name">{{ $customer->name }}</td>
                                    <td class="product-category">{{ $customer->phone ?? '-' }}</td>
                                    <td class="product-category">
                                        <span class="badge badge-primary">{{ $customer->stras_count }}</span>
                                    </td>
                                    <td class="product-category">
                                        <span class="badge badge-success">{{ $customer->tarters_count }}</span>
                                    </td>
                                    <td class="product-category">
                                        <span class="badge badge-warning">{{ $customer->lasers_count }}</span>
                                    </td>
                                    <td class="product-category">
                                        <span class="badge badge-info">{{ $customer->printers_count }}</span>
                                    </td>
                                    <td class="product-action">
                                        <a href="{{ route('customers.show', $customer->id) }}" class="action-edit"><i class="feather icon-eye"></i></a>
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
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.checkboxes.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.data-thumb-view').DataTable({
                responsive: false,
                dom:
                    '<"top"<"actions action-btns"B><"action-filters"f>><"clear">rt<"bottom"<"actions">lp>',
                oLanguage: {
                    sLengthMenu: "عرض _MENU_",
                    sSearch: "",
                    sSearchPlaceholder: "بحث..."
                },
                aLengthMenu: [[10, 20, 30, 40], [10, 20, 30, 40]],
                order: [[1, "asc"]],
                bInfo: false,
                pageLength: 10,
                buttons: [
                ],
                initComplete: function(settings, json) {
                    $(".dt-buttons .btn").removeClass("btn-secondary")
                }
            });
        });
    </script>
@endsection
