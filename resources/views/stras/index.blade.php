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
                                                            <label for="data-status">الحالة</label>
                                                            <select class="form-control" id="data-status" name="status">
                                                                <option selected value="بانتظار اجراء">بانتظار اجراء</option>
                                                                <option value="تم الانتهاء">تم الانتهاء</option>
                                                                <option value="جاري العمل">جاري العمل</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="data-payment-status">حالة الدفع</label>
                                                            <select class="form-control" id="data-payment-status" name="payment_status">
                                                                <option value="0">غير مدفوع</option>
                                                                <option value="1">مدفوع</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <!-- Step 2 -->
                                            <h6><i class="step-icon feather icon-briefcase"></i> مواصفات القماش</h6>
                                            <fieldset>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="data-fabric-type">نوع القماش</label>
                                                            <input type="text" class="form-control required" id="data-fabric-type" name="fabric_type">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="data-source">المصدر</label>
                                                            <select class="form-control" id="data-source" name="source">
                                                                <option value="العميل">العميل</option>
                                                                <option value="AP Group">AP Group</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="data-code">كود التوب</label>
                                                            <input type="text" class="form-control" id="data-code" name="code">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="data-width">عرض القماش</label>
                                                            <input type="number" step="0.01" class="form-control required" id="data-width" name="width">
                                                        </div>
                                                    </div>
                                                     <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="data-paper-shield">ورق حماية</label>
                                                            <input type="text" class="form-control" id="data-paper-shield" name="paper_shield">
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <!-- Step 3 -->
                                            <h6><i class="step-icon feather icon-image"></i> التفاصيل والإضافات</h6>
                                            <fieldset>
                                                <div class="row">
                                                     <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="data-meters">الأمتار</label>
                                                            <input type="number" step="0.01" class="form-control required" id="data-meters" name="meters">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                         <div class="form-group">
                                                            <label for="data-price">السعر</label>
                                                            <input type="number" step="0.01" class="form-control" id="data-price" name="price">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="data-notes">ملاحظات</label>
                                                            <textarea class="form-control" id="data-notes" name="notes"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                           <label>صورة التصميم</label>
                                                             <input type="file" class="form-control" id="data-image-upload" name="image" capture="environment" accept="image/*">
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
                                    <th></th>
                                    <th>صورة</th>
                                    <th>اسم العميل</th>
                                    <th>نوع القماش</th>
                                    <th>المصدر</th>
                                    <th>كود التوب</th>
                                    <th>عرض القماش</th>
                                    <th>الامتار</th>
                                    <th>الحاله</th>
                                    <th>ورق حمايه</th>
                                    <th>ملاحظات</th>
                                    <th>النوع</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Loop through Printers Orders (Pending Stras?) --}}
                                @foreach ($Orders as $Order)
                                <tr>
                                    <td></td>
                                    <td class="product-img">
                                        <input type="hidden" class="order_id" value="{{ $Order->id }}">
                                        <img src="{{ $Order->ordersImgs->first() ? asset('storage/'.$Order->ordersImgs->first()->path) : asset('core/images/elements/apple-watch.png') }}" alt="Img placeholder">
                                    </td>
                                    <td class="product-name">{{ $Order->customers->name ?? '-' }} </td>
                                    <td class="product-category"><b>{{  $Order->fabric_type ?? '-' }}   </b></td>
                                    <td class="product-category">-</td>
                                    <td class="product-category">-</td>
                                    <td class="product-category">{{ $Order->fileWidth ?? '-' }}</td>
                                    <td class="product-category"><b>{{ $Order->meters ?? '-' }}</b></td>
                                    <td>
                                        <div class="chip chip-warning">
                                            <div class="chip-body status-toggle" style="cursor: pointer">
                                                <div class="chip-text hover_action">تحويل للاستراس</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="product-category">-</td>
                                    <td class="product-category">{{ $Order->notes ?? '-' }}</td>
                                    <td><span class="badge badge-primary">طباعة</span></td>
                                    <td class="product-price" title="{{ $Order->created_at }}">{{ $Order->created_at->locale('ar')->diffForHumans() }}</td>
                                </tr>
                                @endforeach

                                {{-- Loop through Stras Records --}}
                                @foreach ($Records as $Record)
                                <tr>
                                    <td></td>
                                    <td class="product-img">
                                        <input type="hidden" class="stras_id" value="{{ $Record->id }}">
                                        {{-- Image logic? Stras might not have images or linked to order --}}
                                        <img src="{{ asset('core/images/elements/apple-watch.png') }}" alt="Img placeholder">
                                    </td>
                                    <td class="product-name">{{ $Record->customer->name ?? '-' }} </td>
                                    <td class="product-category"><b>{{ $Record->fabrictype ?? '-' }}</b></td>
                                    <td class="product-category">{{ $Record->fabricsrc ?? '-' }}</td>
                                    <td class="product-category">{{ $Record->fabriccode ?? '-' }}</td>
                                    <td class="product-category">{{ $Record->fabricwidth ?? '-' }}</td>
                                    <td class="product-category"><b>{{ $Record->meters ?? '-' }}</b></td>
                                    <td>
                                        <div class="chip chip-info">
                                            <div class="chip-body status-toggle" style="cursor: pointer">
                                                <div class="chip-text hover_action">{{ $Record->status ? 'تم الانتهاء' : 'جاري العمل' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="product-category">{{ $Record->papyershild ?? '-' }}</td>
                                    <td class="product-category">{{ $Record->notes ?? '-' }}</td>
                                    <td><span class="badge badge-success">استراس خارجي</span></td>
                                    <td class="product-price" title="{{ $Record->created_at }}">{{ $Record->created_at->locale('ar')->diffForHumans() }}</td>
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
    <script src="{{ asset('core/vendors/js/extensions/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('core/js/scripts/modal/components-modal.js') }}"></script>

    @vite('resources/js/pages/stras.js')

@endsection
