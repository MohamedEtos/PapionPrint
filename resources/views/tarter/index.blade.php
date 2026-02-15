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
    <style>
        @keyframes flash-green {
            0% { background-color: #28c76f; color: white; }
            50% { background-color: rgba(40, 199, 111, 0.5); color: white; }
            100% { background-color: white; color: inherit; }
        }
        .flash-input {
            animation: flash-green 1s ease-out;
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
                            <h2 class="content-header-title float-left mb-0">تشغيل الترتر</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#"> الترتر</a>
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
                                    <h4 class="card-title">إضافة طلب ترتر</h4>
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
                                                             <label for="data-height ">الطول</label>
                                                             <input type="number" step="0.01" class="form-control required " id="data-height" name="height">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                             <label for="data-width">العرض</label>
                                                             <select class="form-control required" id="data-width" name="width">
                                                                <option value="24">24</option>
                                                                <option value="32">32</option>
                                                                <option value="40">40</option>
                                                             </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                             <label for="data-required-pieces">عدد القطع المطلوبة </label>
                                                             <input type="number" class="form-control" id="data-required-pieces" placeholder="ادخال لحساب عدد الكروت">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                             <label for="data-pieces-per-card">عدد القطع في الكارت </label>
                                                             <input type="number" class="form-control required " id="data-pieces-per-card" name="pieces_per_card" >
                                                        </div>
                                                    </div>
                                                                                                        <div class="col-md-6">
                                                        <div class="form-group">
                                                             <label for="data-cards-count">عدد الكروت </label>
                                                             <input type="number" class="form-control required " id="data-cards-count" name="cards_count" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                             <label for="data-machine-time">وقت تشغيل الماكينة (دقائق)</label>
                                                             <input type="number" class="form-control" id="data-machine-time" name="machine_time" placeholder="الوقت بالدقائق">
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
                                                                        <label>مقاس الإبرة</label>
                                                                        <select class="form-control layer-size" name="layers[0][size]">
                                                                             @foreach($prices as $price)
                                                                                 @if($price->type == 'needle')
                                                                                     <option value="{{ $price->size }}">{{ $price->size }}</option>
                                                                                 @endif
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
                     <div class="action-btns" style="display:none; margin-bottom: 10px;">
                        <div class="btn-dropdown mr-1 mb-1">
                            <div class="btn-group dropdown actions-dropodown">
                                <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    الإجراءات
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="javascript:void(0)" id="bulk-delete-btn"><i class="feather icon-trash"></i> حذف المحدد</a>
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="addToInvoice()"><i class="feather icon-file-text"></i> انشاء  فاتوره  </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- dataTable starts -->
                    <div class="table-responsive">
                        <table class="table data-thumb-view">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                            <input type="checkbox" id="select-all-tarter">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                        </div>
                                    </th>
                                    <th>صورة</th>
                                    <th>اسم العميل</th>
                                    <th>الطول/العرض</th> <!-- Combo for space -->
                                    <th>اجمالي القطع</th>
                                    <th>وقت الماكينة</th>
                                    <th>المراحل (الإبر)</th>
                                    <th>ملاحظات</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Loop through Tarter Records --}}
                                @foreach ($Records as $Record)
                                <tr data-layers="{{ json_encode($Record->layers) }}" 
                                    data-height="{{ $Record->height }}" 
                                    data-cards-count="{{ $Record->cards_count }}" 
                                    data-pieces-per-card="{{ $Record->pieces_per_card }}"
                                    data-machine-time="{{ $Record->machine_time }}"
                                    data-width="{{ $Record->width }}">
                                    <td>
                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                            <input type="checkbox" class="tarter-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="product-img">
                                        <input type="hidden" class="tarter_id" value="{{ $Record->id }}">
                                        @if($Record->image_path)
                                            <img src="{{ asset('storage/'.$Record->image_path) }}" alt="Img">
                                        @else
                                            <img src="{{ asset('core/images/elements/apple-watch.png') }}" alt="Placeholder">
                                        @endif
                                    </td>
                                    <td class="product-name">{{ $Record->customer->name ?? '-' }} </td>
                                    <td class="product-category">
                                        {{ $Record->height ?? '-' }} x {{ $Record->width ?? '-' }}
                                    </td>
                                    <td class="product-category">
                                        @if($Record->cards_count && $Record->pieces_per_card)
                                            {{ $Record->cards_count * $Record->pieces_per_card }}
                                            <br>
                                            <small class="text-muted">({{ $Record->cards_count }} كارت)</small>
                                        @else
                                            <span class="text-muted">لم يحسب</span>
                                        @endif
                                    </td>
                                    <td class="product-category">{{ $Record->machine_time }} دقيقة</td>
                                    <td class="product-category">
                                        @foreach($Record->layers as $layer)
                                            <span class="badge badge-primary">{{ $layer->size }}: {{ $layer->count }}</span>
                                        @endforeach
                                    </td>
                                    <td class="product-category">{{ $Record->notes ?? '-' }}</td>
                                    <td class="product-price" title="{{ $Record->created_at }}">{{ $Record->created_at ? $Record->created_at->locale('ar')->diffForHumans() : '-' }}</td>
                                    <td class="product-action">
                                        <span class="action-edit" onclick="editTarter({{ $Record->id }})"><i class="feather icon-edit"></i></span>
                                        <span class="action-delete" onclick="deleteTarter({{ $Record->id }})"><i class="feather icon-trash"></i></span>
                                        <span class="action-restart" onclick="restartTarter({{ $Record->id }})" title="إعادة تشغيل"><i class="feather icon-refresh-cw"></i></span>
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                             <tfoot>
                                <tr>
                                    <td colspan="10">
                                        <div id="tarter-calculator-results" class="alert alert-primary mb-0" style="display:none; font-weight: bold; font-size: 1.1em;">
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

    <script>
        window.tarterPrices = @json($prices);
    </script>

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

    @vite('resources/js/pages/tarter.js')

@endsection
