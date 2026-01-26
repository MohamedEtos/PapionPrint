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
        'resources/core/css-rtl/plugins/forms/wizard.css',
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
                            <h2 class="content-header-title float-left mb-0">تشغيل الاستراس</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#"> الاستراس</a>
                                    </li>
                                    <li class="breadcrumb-item active"> قائمة الطلبات
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Form wizard with step validation section start -->
                <section id="validation" style="display:none;" class="mb-2">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">إضافة طلب استراس</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form action="#" class="steps-validation wizard-circle">
                                    <!-- Step 1 -->
                                            <h6><i class="step-icon feather icon-home"></i> البيانات الأساسية</h6>
                                            <fieldset>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="data-customer-view">اسم العميل</label>
                                                            <input type="text" class="form-control required" name="name" id="data-customer-view" list="customers-list" placeholder="ابحث عن العميل...">
                                                            <datalist id="customers-list">
                                                                @foreach($customers->unique('name') as $customer)
                                                                    <option data-id="{{ $customer->id }}" value="{{ $customer->name }}">
                                                                @endforeach
                                                            </datalist>
                                                            <input type="hidden" id="data-customer">
                                                        </div>
                                                    </div>
                                                     <div class="col-md-6">
                                                        <div class="form-group">
                                                             <label for="data-height">الطول</label>
                                                             <input type="number" step="0.01" class="form-control" id="data-height" name="height">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                             <label for="data-width">العرض</label>
                                                             <input type="number" step="0.01" class="form-control" id="data-width" name="width">
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <!-- Step 2 -->
                                            <h6><i class="step-icon feather icon-layers"></i> المراحل</h6>
                                            <fieldset>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <button type="button" class="btn btn-outline-primary mb-2" id="add-layer-btn">
                                                            <i class="feather icon-plus"></i> إضافة طبقة
                                                        </button>
                                                        <div id="layers-container">
                                                            <!-- Dynamic layers will optionally appear here -->
                                                             <div class="layer-row row mb-1">
                                                                <div class="col-md-5">
                                                                    <div class="form-group">
                                                                        <label>المقاس</label>
                                                                        <select class="form-control layer-size" name="layers[0][size]">
                                                                             @foreach($prices as $price)
                                                                                <option value="{{ $price->size }}">{{ $price->size }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <div class="form-group">
                                                                        <label>العدد</label>
                                                                        <input type="number" class="form-control layer-count" name="layers[0][count]" placeholder="عدد الحبات">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2 d-flex align-items-center">
                                                                    <button type="button" class="btn btn-danger btn-icon remove-layer-btn"><i class="feather icon-trash"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <!-- Step 3 -->
                                            <h6><i class="step-icon feather icon-image"></i> التفاصيل والإضافات</h6>
                                            <fieldset>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="data-notes">ملاحظات</label>
                                                            <textarea class="form-control" id="data-notes" name="notes"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                           <label>صورة التصميم (أو الصق الصورة Ctrl+V)</label>
                                                             <input type="file" class="form-control" id="data-image-upload" name="image" capture="environment" accept="image/*">
                                                             <div id="pasted-image-preview" class="mt-2 text-center" style="display:none;">
                                                                 <img src="" style="max-height: 200px; border: 1px solid #ddd; padding: 5px;">
                                                                 <p class="text-muted mt-1">صورة تم لصقها</p>
                                                             </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Form wizard with step validation section end -->

                <!-- Data list view starts -->
                <section id="data-thumb-view" class="data-thumb-view-header">
                    <!-- dataTable starts -->
                    <div class="table-responsive">
                        <table class="table data-thumb-view">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                            <input type="checkbox" id="select-all-stras">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                        </div>
                                    </th>
                                    <th>صورة</th>
                                    <th>اسم العميل</th>
                                    <th>الطول</th>
                                    <th>العرض</th>
                                    <th>المراحل</th>
                                    <th>ملاحظات</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Loop through Stras Records --}}
                                @foreach ($Records as $Record)
                                <tr data-layers="{{ json_encode($Record->layers) }}">
                                    <td>
                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                            <input type="checkbox" class="stras-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="product-img">
                                        <input type="hidden" class="stras_id" value="{{ $Record->id }}">
                                        @if($Record->image_path)
                                            <img src="{{ asset('storage/'.$Record->image_path) }}" alt="Img">
                                        @else
                                            <img src="{{ asset('core/images/elements/apple-watch.png') }}" alt="Placeholder">
                                        @endif
                                    </td>
                                    <td class="product-name">{{ $Record->customer->name ?? '-' }} </td>
                                    <td class="product-category">{{ $Record->height ?? '-' }}</td>
                                    <td class="product-category">{{ $Record->width ?? '-' }}</td>
                                    <td class="product-category">
                                        @foreach($Record->layers as $layer)
                                            <span class="badge badge-primary">{{ $layer->size }}: {{ $layer->count }}</span>
                                        @endforeach
                                    </td>
                                    <td class="product-category">{{ $Record->notes ?? '-' }}</td>
                                    <td class="product-price" title="{{ $Record->created_at }}">{{ $Record->created_at ? $Record->created_at->locale('ar')->diffForHumans() : '-' }}</td>
                                </tr>
                                @endforeach

                            </tbody>
                             <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <div id="stras-calculator-results" class="alert alert-primary mb-0" style="display:none; font-weight: bold; font-size: 1.1em;">
                                            <!-- Totals will appear here -->
                                        </div>
                                    </td>
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
    <script src="{{ asset('core/vendors/js/extensions/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('core/js/scripts/modal/components-modal.js') }}"></script>

    @vite('resources/js/pages/stras.js')

@endsection
