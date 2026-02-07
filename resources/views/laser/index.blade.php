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
        .product-img img {
            max-width: 100px; /* Limit image size in table */
            max-height: 100px;
        }
        
        /* Flash animation for auto-calculated fields */
        @keyframes flash-animation {
            0%, 100% { background-color: transparent; }
            50% { background-color: #7367F0; color: white; }
        }
        
        .flash-input {
            animation: flash-animation 0.6s ease-in-out;
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
                            <h2 class="content-header-title float-left mb-0">تشغيل الليزر</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#"> الليزر</a>
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
                <!-- Data list view starts -->
                <section id="data-thumb-view" class="data-thumb-view-header">
                     <div class="action-btns" style="display:none; margin-bottom: 10px;">
                        <div class="btn-dropdown mr-1 mb-1">
                            <div class="btn-group dropdown actions-dropodown">
                                <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    الإجراءات
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @if(auth()->user()->can('الفواتير'))
                                    <a class="dropdown-item" href="javascript:void(0)" id="bulk-recalc-btn"><i class="feather icon-refresh-cw"></i> تحديث الاسعار</a>
                                    @endif
                                    <a class="dropdown-item" href="javascript:void(0)" id="bulk-delete-btn"><i class="feather icon-trash"></i> حذف المحدد</a>
                                    @if(auth()->user()->can('الفواتير'))
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="window.addToInvoice()"><i class="feather icon-file-plus"></i> اضافة للفاتورة</a>
                                    @endif
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
                                            <input type="checkbox" id="select-all-laser">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                        </div>
                                    </th>
                                    <th>صورة</th>
                                    <th>اسم العميل</th>
                                    <th>الخامة</th>
                                    <th>المصدر</th>
                                    <th>الطول</th>
                                    <th>العرض</th>
                                    <th>قطع/مقطع</th>
                                    <th>عدد المقاطع</th>
                                    <th>سيليكون</th>
                                    <th>التكلفة للقطعة</th>
                                    <th>تكلفة التشغيل</th>
                                    <th>الاجمالي</th>
                                    <th>ملاحظات</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($Records as $Record)
                                <tr data-id="{{ $Record->id }}" data-cost="{{ $Record->total_cost }}" data-height="{{ $Record->height }}" data-section-count="{{ $Record->section_count }}" data-required-pieces="{{ $Record->required_pieces ?? 0 }}">
                                    <td>
                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                            <input type="checkbox" class="laser-checkbox" value="{{ $Record->id }}">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="product-img">
                                        @if($Record->image_path)
                                            <img src="{{ asset('storage/'.$Record->image_path) }}" alt="Img">
                                        @else
                                            <span class="text-muted">No Img</span>
                                        @endif
                                    </td>
                                    <td>{{ $Record->customer->name ?? '-' }}</td>
                                    <td>{{ $Record->material->name ?? '-' }}</td>
                                    <td>{{ $Record->source == 'ap_group' ? 'AP Group' : 'العميل' }}</td>
                                    <td>{{ $Record->height }}</td>
                                    <td>{{ $Record->width }}</td>
                                    <td>{{ $Record->pieces_per_section }}</td>
                                    <td>{{ $Record->section_count }}</td>
                                    <td>{{ $Record->add_ceylon ? 'نعم' : 'لا' }}</td>
                                    <td>{{ number_format($Record->manufacturing_cost,2) }}</td>
                                    <td>
                                        @if($Record->custom_operating_cost !== null)
                                            <span class="badge badge-warning">{{ $Record->custom_operating_cost }}</span>
                                        @else
                                            <span class="text-muted">{{ $operatingCost ?? 0 }} (Def)</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($Record->total_cost, 2) }}</td>
                                    <td>{{ $Record->notes }}</td>
                                    <td>{{ $Record->created_at->format('Y-m-d') }}</td>
                                    <td class="product-action">
                                         <span class="action-edit" onclick="editLaserOrder({{ $Record->id }})"><i class="feather icon-edit"></i></span>
                                         <span class="action-delete" onclick="deleteLaserOrder({{ $Record->id }})"><i class="feather icon-trash"></i></span>
                                         <span class="action-restart" onclick="restartLaserOrder({{ $Record->id }})" title="إعادة تشغيل"><i class="feather icon-refresh-cw"></i></span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if(auth()->user()->can('الفواتير'))
                            <tfoot>
                                <tr>
                                    <td colspan="16">
                                        <div id="laser-calculator-results" class="alert alert-primary mb-0" style="display:none; font-weight: bold; font-size: 1.1em;">
                                            الإجمالي المحدد: <span id="selected-total">0.00</span> جنيه
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Add Order Modal -->
    <div class="modal fade" id="addOrderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">طلب ليزر جديد</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="laserOrderForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                             <div class="col-md-6 form-group">
                                <label>اسم العميل</label>
                                <input type="text" class="form-control" name="customerName" id="customer-name-input" list="customers-list" placeholder="ابحث أو أضف عميل جديد...">
                                <datalist id="customers-list">
                                    @foreach($customers->unique('name') as $customer)
                                        <option data-id="{{ $customer->id }}" value="{{ $customer->name }}">
                                    @endforeach
                                </datalist>
                                <input type="hidden" id="customer-id-input" name="customerId">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>المصدر</label>
                                <select class="form-control" name="source" id="source-select">
                                    <option value="ap_group">المصنع</option>
                                    <option value="client">العميل</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>الخامة</label>
                                <select class="form-control" name="materialId" id="material-select">
                                    <option value="" data-price="0">اختر الخامة</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}" data-price="{{ $material->price }}">{{ $material->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div class="col-md-6 form-group">
                                <label>إضافة سيليكون؟</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="ceylonSwitch" name="add_ceylon">
                                    <label class="custom-control-label" for="ceylonSwitch">نعم</label>
                                </div>
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>الطول (سم)</label>
                                <input type="number" step="0.01" class="form-control calc-input" name="height" id="height-input" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>العرض (سم)</label>
                                <input type="number" step="0.01" class="form-control " value="150" name="width" id="width-input" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>عدد القطع المطلوبة</label>
                                <input type="number" class="form-control calc-input" name="required_pieces" id="required-pieces-input" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>عدد القطع في المده</label>
                                <input type="number" class="form-control calc-input" name="pieces_per_section" id="pps-input" value="1" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>عدد المدات</label>
                                <input type="number" class="form-control" name="section_count" id="sc-input"  >
                                <small class="text-muted">يحسب تلقائياً</small>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>تكلفة التشغيل (اختياري)</label>
                                <input type="number" step="0.01" class="form-control calc-input" name="custom_operating_cost" id="custom-operating-cost-input" placeholder="الافتراضي: {{ $operatingCost ?? 0 }}">
                            </div>

                             <div class="col-md-12 form-group">
                                <label>الصورة (أو الصق الصورة Ctrl+V)</label>
                                <div id="drop-zone" style="border: 2px dashed #ccc; border-radius: 5px; padding: 20px; text-align: center; background: #f9f9f9; transition: all 0.3s;">
                                    <i class="feather icon-upload" style="font-size: 2em; color: #888;"></i>
                                    <p class="mb-2">اسحب وأفلت الصورة هنا أو انقر للاختيار</p>
                                    <input type="file" class="form-control" id="image-upload" name="image" accept="image/*" style="cursor: pointer;">
                                </div>
                                <div id="pasted-image-preview" class="mt-2 text-center" style="display:none;">
                                    <img src="" style="max-height: 200px; border: 1px solid #ddd; padding: 5px;">
                                    <p class="text-muted mt-1">صورة تم لصقها</p>
                                </div>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>ملاحظات</label>
                                <textarea class="form-control" name="notes"></textarea>
                            </div>
                            @if(auth()->user()->can('الفواتير'))
                             <div class="col-md-12">
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>سعر القطعة: <span id="piece-cost">0.00</span> جنيه</h6>
                                            <h6>سعر المقطع: <span id="section-cost">0.00</span> جنيه</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>عدد المقاطع: <span id="section-count-display">0</span></h6>
                                            <h6>التكلفة الإجمالية: <span id="approx-cost">0.00</span> جنيه</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" id="saveOrderBtn">حفظ</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Image Zoom Modal -->
    <div class="modal fade" id="imageZoomModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="background: transparent; border: none; box-shadow: none;">
                <div class="modal-body text-center p-0">
                    <img id="enlarged-image" src="" style="max-width: 100%; max-height: 85vh; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                    <button type="button" class="btn btn-icon btn-sm btn-white close-zoom" style="position: absolute; top: -15px; right: -15px; background: white; border-radius: 50%; opacity: 1; padding: 5px;">
                        <i class="feather icon-x" style="color: black; font-weight: bold;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.checkboxes.min.js') }}"></script>

    <script>
        window.laserConfig = {
            routes: {
                store: "{{ route('laser.store') }}",
                bulkDelete: "/laser/bulk-delete",
                delete: "/laser/delete/",
                update: "/laser/update/",
                show: "/laser/show/",
                restart: "/laser/restart/"
            },
            csrfToken: "{{ csrf_token() }}",
            costs: {
                operating: {{ $operatingCost ?? 0 }},
                ceylon: {{ $ceylonPrice ?? 0 }}
            }
        };
    </script>
    @vite('resources/js/pages/laser.js')
@endsection
