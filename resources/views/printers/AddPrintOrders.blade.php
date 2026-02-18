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
                            <h2 class="content-header-title float-left mb-0">اذونات التشغيل</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#"> الطباعة</a>
                                    </li>
                                    <li class="breadcrumb-item active">اذونات التشغيل
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Chat</a><a class="dropdown-item" href="#">Email</a><a class="dropdown-item" href="#">Calendar</a></div>
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
                                    @can('حذف الطباعه')
                                    <a class="dropdown-item text-danger bulk-delete-btn" href="#"><i class="feather icon-trash "></i>حذف</a>
                                    @endcan
                                    <a class="dropdown-item" href="#"><i class="feather icon-archive"></i>ارشفة</a>
                                </div>
                            </div>
                        </div>
                    </div>


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
                                    <th>التاريخ</th>
                                    <th>اجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($Orders as $Order)

                                <tr>
                                    <td></td>

                                    <td class="product-img">
                                        <input type="hidden" class="order_id" value="{{ $Order->id }}">
                                        <img src="{{ $Order->ordersImgs->first() ? asset('storage/'.$Order->ordersImgs->first()->path) : asset('core/images/elements/apple-watch.png') }}" alt="Img placeholder">
                                    </td>
                                    
                                    <td class="product-name">{{ $Order->customers->name }} </td>
                                    <td class="product-category">{{ $Order->machines->name}} {{ $Order->pass}} pass</td>
                                    <td class="product-category"><b>{{ $Order->meters }}</b></td>

                                    <td>
                                        <div class="chip chip-{{ $Order->status == 'تم الانتهاء' ? 'success' : 'info' }}">
                                            <div class="chip-body status-toggle" style="cursor: pointer">
                                                <div class="chip-text hover_action">{{ $Order->status }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- <td class="product-price">{{ optional($Order->printingprices)->pricePerMeter }}</td> -->
                                    <td class="product-price" title="{{ $Order->created_at }}">{{ $Order->created_at->locale('ar')->diffForHumans() }}</td>
                                    <td class="product-action">
                                        @can('تعديل الطباعه')
                                        <span class=" hover_action action-edit "><i class="feather icon-edit"></i></span>
                                        @endcan
                                        @can('حذف الطباعه')
                                        <span class=" hover_action action-delete text-danger " ><i class="feather icon-trash"></i></span>
                                        @endcan

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> 
                    <!-- dataTable ends -->

                    <!-- add new sidebar starts -->
                    <div class="add-new-data-sidebar">
                        <div class="overlay-bg"></div>
                        <div class="add-new-data">
                            <div class="div mt-2 px-2 d-flex new-data-title justify-content-between">
                                <div>
                                    <h4 class="text-uppercase">اضافه اذن تشغيل</h4>
                                </div>
                                <div class="hide-data-sidebar">
                                    <i class="feather icon-x"></i>
                                </div>
                            </div>
                            <div class="data-items pb-3">
                                <div class="data-fields px-2 mt-3">
                                    <div class="row">
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-customer-view">اسم العميل</label>
                                            <input type="text" class="form-control" name="name" id="data-customer-view" list="customers-list" placeholder="ابحث عن العميل...">
                                            <datalist id="customers-list">
                                                @foreach($customers->unique('name') as $customer)
    <option data-id="{{ $customer->id }}" value="{{ $customer->name }}">
@endforeach
                                            </datalist>
                                            <input type="hidden" id="data-customer">
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-category">الماكينة</label>
                                            <select class="form-control" id="data-machine">
                                                <option value="">اختر الماكينة</option>
                                                @foreach($machines as $machine)
                                                <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-6 data-field-col">
                                            <label for="data-height">الطول</label>
                                            <input type="number" step="0.01" class="form-control" id="data-height">
                                        </div>
                                        <div class="col-sm-6 data-field-col">
                                            <label for="data-width">العرض</label>
                                            <input type="number" step="0.01" class="form-control" id="data-width">
                                        </div>
                                        <div class="col-sm-6 data-field-col">
                                            <label for="data-copies">نسخ الملف</label>
                                            <input type="number" class="form-control" id="data-copies">
                                        </div>
                                        <div class="col-sm-6 data-field-col">
                                            <label for="data-pic-copies">صور في النسخة</label>
                                            <input type="number" class="form-control" id="data-pic-copies">
                                        </div>
                                        <div class="col-sm-6 data-field-col">
                                            <label for="data-pass">Pass</label>
                                            <select class="form-control" id="data-pass">
                                                <option value="1">1</option>
                                                <option value="4">4</option>
                                                <option value="6">6</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 data-field-col">
                                            <label for="data-meters">الأمتار</label>
                                            <input type="number" step="0.01" class="form-control" id="data-meters">
                                        </div>
                                        <div class="col12 data-field-col text-muted">
                                            <label for="data-meters text-muted">اجمالي القطع</label>
                                            <span id="data-total-pic" class="">0</span>
                                            <label for="data-price-pic-muted">  سعر القطعه المتوقع</label>
                                            <span id="data-price-pic" class="">0</span>
                                        </div>
                                        <!-- <div class="col-sm-12 data-field-col">
                                            <label for="data-status">حالة الطلب</label>
                                            <select class="form-control" id="data-status">
                                                <option selected value="بانتظار اجراء">بانتظار اجراء</option>
                                                <option value="بدات الطباعة">بدات الطباعة </option>
                                                <option value="انتهاء الطباعة">انتهاء الطباعة</option>
                                                <option value="ملغي">ملغي</option>
                                            </select>
                                        </div> -->
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-fabric-type">نوع القماش </label>
                                            <input type="text" class="form-control" id="data-fabric-type">
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-notes">ملاحظات</label>
                                            <textarea class="form-control" id="data-notes"></textarea>
                                        </div>
                                        <div class="col-sm-12 data-field-col data-list-upload">
                                            <form action="{{ route('printers.upload.image') }}" method="POST" enctype="multipart/form-data" class="dropzone dropzone-area" id="dataListUpload">
                                                @csrf
                                                <div class="dz-message">Upload Image</div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="add-data-footer d-flex justify-content-around px-3 mt-2">
                                <div class="add-data-btn">
                                    <button type="submit" class="btn btn-primary" id="saveDataBtn">Add Data</button>
                                </div>
                                <div class="cancel-data-btn">
                                    <button class="btn btn-outline-danger">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>


                </section>
                <!-- Data list view end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->





 


    <!-- Ink Consumption Modal -->
    <div class="modal fade" id="inkConsumptionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">استهلاك حبر / ورق</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>مخزون الورق (متر)</h6>
                            <div style="height: 300px; position: relative;">
                                <canvas id="paperChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <h6>مخزون الحبر (لتر)</h6>
                             <div style="height: 300px; position: relative;">
                                <canvas id="inkChart"></canvas>
                             </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h6>اختر نوع الماكينة واللون لخصم 1 لتر</h6>
                    <hr>
                    
                    <h6 class="text-primary font-weight-bold mb-2">Sublimation</h6>
                    <div class="d-flex justify-content-center flex-wrap mb-3">
                        <button class="btn btn-info m-1 consume-ink-btn" data-type="sublimation" data-color="Cyan" style="background-color: cyan; border-color: cyan; color: black;">Cyan</button>
                        <button class="btn btn-danger m-1 consume-ink-btn" data-type="sublimation" data-color="Magenta" style="background-color: magenta; border-color: magenta; color: white;">Magenta</button>
                        <button class="btn  m-1 consume-ink-btn" data-type="sublimation" data-color="Yellow" style="background-color: #FF9F43; border-color: #FF9F43; color: black;">Yellow</button>
                        <button class="btn btn-dark m-1 consume-ink-btn" data-type="sublimation" data-color="Black" style="background-color: black; border-color: black; color: white;">Black</button>
                    </div>

                    <hr>

                    <h6 class="text-primary font-weight-bold mb-2">DTF</h6>
                    <div class="d-flex justify-content-center flex-wrap">
                        <button class="btn btn-info m-1 consume-ink-btn" data-type="dtf" data-color="Cyan" style="background-color: cyan; border-color: cyan; color: black;">Cyan</button>
                        <button class="btn btn-danger m-1 consume-ink-btn" data-type="dtf" data-color="Magenta" style="background-color: magenta; border-color: magenta; color: white;">Magenta</button>
                        <button class="btn btn- m-1 consume-ink-btn" data-type="dtf" data-color="Yellow" style="background-color: #FF9F43; border-color: #FF9F43; color: black;">Yellow</button>
                        <button class="btn btn-dark m-1 consume-ink-btn" data-type="dtf" data-color="Black" style="background-color: black; border-color: black; color: white;">Black</button>
                        <button class="btn btn-light m-1 consume-ink-btn" data-type="dtf" data-color="White" style="background-color: white; border-color: #ddd; color: black;">White</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


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
    <script src="{{ asset('core/vendors/js/extensions/dropzone.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.checkboxes.min.js') }}"></script>
    <script src="{{ asset('core/js/scripts/ui/data-list-view.js') }}"></script>
    <script src="{{ asset('core/js/scripts/modal/components-modal.js') }}"></script>

    <script>
        window.papionInvData = {
            inkStocks: @json($inkStocks ?? []),
            paperStocks: @json($paperStocks ?? []),
            machines: @json($machines ?? []), // Inject machines data
            consumeInkRoute: "{{ route('inventory.consumeInk') }}",
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
    @vite('resources/js/pages/AddNewOrder.js')

@endsection
