@extends('layouts.app')

@section('css')
    @vite([
        'resources/core/vendors/css/tables/datatable/datatables.min.css',
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
                            <h2 class="content-header-title float-left mb-0">سلة محذوفات الليزر</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('laser.index') }}"> الليزر</a>
                                    </li>
                                    <li class="breadcrumb-item active"> المحذوفات
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                     <a href="{{ route('laser.index') }}" class="btn btn-primary pull-right"> <i class="feather icon-arrow-right"></i>  العودة للطلبات</a>
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
                                    <th>صورة</th>
                                    <th>اسم العميل</th>
                                    <th>الخامة</th>
                                    <th>الطول</th>
                                    <th>العرض</th>
                                    <th>العدد المطلوب</th>
                                    <th>ملاحظات</th>
                                    <th>تاريخ الحذف</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Loop through Laser Records --}}
                                @foreach ($Records as $Record)
                                <tr>
                                    <td class="product-img">
                                        @if($Record->image_path)
                                            <img src="{{ asset('storage/'.$Record->image_path) }}" alt="Img">
                                        @else
                                            <img src="{{ asset('core/images/elements/apple-watch.png') }}" alt="Placeholder">
                                        @endif
                                    </td>
                                    <td class="product-name">{{ $Record->customer->name ?? '-' }} </td>
                                    <td class="product-category">{{ $Record->material->name ?? '-' }}</td>
                                    <td class="product-category">{{ $Record->height ?? '-' }}</td>
                                    <td class="product-category">{{ $Record->width ?? '-' }}</td>
                                    <td class="product-category">{{ $Record->required_pieces ?? '-' }}</td>
                                    <td class="product-category">{{ $Record->notes ?? '-' }}</td>
                                    <td class="product-price" title="{{ $Record->deleted_at }}">{{ $Record->deleted_at ? $Record->deleted_at->locale('ar')->diffForHumans() : '-' }}</td>
                                    <td class="product-action">
                                        <span class="action-restore btn btn-success btn-sm" onclick="restoreLaser({{ $Record->id }})" title="استرجاع"><i class="feather icon-rotate-ccw"></i></span>
                                        <span class="action-force-delete btn btn-danger btn-sm" onclick="forceDeleteLaser({{ $Record->id }})" title="حذف نهائي"><i class="feather icon-trash-2"></i></span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
             var table = $('.data-thumb-view').DataTable({
                responsive: false,
                columnDefs: [
                    { orderable: true, targets: 0, checkboxes: { selectRow: true } }
                ],
                dom: '<"top"<"actions action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
                oLanguage: {
                    sLengthMenu: "_MENU_",
                    sSearch: ""
                },
                aLengthMenu: [[4, 10, 15, 20], [4, 10, 15, 20]],
                order: [[1, "desc"]],
                bInfo: false,
                pageLength: 4,
                buttons: [],
                initComplete: function(settings, json) {
                    $(".dt-buttons .btn").removeClass("btn-secondary")
                }
            });
        });

        function restoreLaser(id) {
             Swal.fire({
                title: 'استرجاع الطلب؟',
                text: "سيتم إعادة الطلب لقائمة العمل.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28c76f',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، استرجع',
                cancelButtonText: 'الغاء'
            }).then((result) => {
                if (result.value) {
                     $.ajax({
                        url: '/laser/restore/' + id,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire('تم الاسترجاع!', 'تم استرجاع الطلب بنجاح.', 'success').then(() => location.reload());
                        },
                        error: function() { Swal.fire('خطأ!', 'حدث خطأ اثناء الاسترجاع.', 'error'); }
                    });
                }
            });
        }

        function forceDeleteLaser(id) {
            Swal.fire({
                title: 'حذف نهائي؟',
                text: "لن يمكنك استرجاع هذا الطلب مرة أخرى!",
                type: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، حذف نهائي',
                cancelButtonText: 'الغاء'
            }).then((result) => {
                if (result.value) {
                     $.ajax({
                        url: '/laser/force-delete/' + id,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                             Swal.fire('تم الحذف!', 'تم حذف الطلب نهائياً.', 'success').then(() => location.reload());
                        },
                        error: function() { Swal.fire('خطأ!', 'حدث خطأ اثناء الحذف.', 'error'); }
                    });
                }
            });
        }
    </script>
@endsection
