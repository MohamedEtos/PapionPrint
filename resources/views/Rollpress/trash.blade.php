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
                            <h2 class="content-header-title float-left mb-0">سلة المهملات (المكبس)</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                                    <li class="breadcrumb-item"><a href="#"> المكبس</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('rollpress.archive') }}"> الارشيف</a></li>
                                    <li class="breadcrumb-item active"> المحذوفات</li>
                                </ol>
                            </div>
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
                                    <th>Actions</th>
                                    <th>صورة</th>
                                    <th>اسم العميل</th>
                                    <th>نوع القماش</th>
                                    <th>الامتار</th>
                                    <th>تاريخ الحذف</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data loaded via AJAX -->
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

    @vite('resources/js/pages/rollpress_trash.js')

@endsection
