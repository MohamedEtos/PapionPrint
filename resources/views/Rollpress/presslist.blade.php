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
                            <h2 class="content-header-title float-left mb-0">تشغيل المكبس</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#"> المكبس</a>
                                    </li>
                                    <li class="breadcrumb-item active"> قائمة الطلبات
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
                                    <th>حالة الدفع</th>
                                    <th>ورق حمايه</th>
                                    <th>السعر</th>
                                    <th>ملاحظات</th>
                                    <th>التاريخ</th>
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
                                    
                                    <td class="product-name">{{ $Order->customers->name ?? '-' }} </td>
                                    <td class="product-category"><b>{{ $Order->fabrictype ?? '-' }}   </b></td>
                                    <td class="product-category">-</td>
                                    <td class="product-category">-</td>
                                    <td class="product-category">-</td>
                                    <td class="product-category"><b>{{ $Order->meters ?? '-' }}</b></td>

                                    <td>
                                        <div class="chip chip-info">
                                            <div class="chip-body status-toggle" style="cursor: pointer">
                                                <div class="chip-text hover_action">{{ $Order->status }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="product-category">{{ $Order->paymentStatus ?? '-' }}</td>
                                    <td class="product-category">-</td>
                                    <td class="product-price">{{ $Order->totalPrice ?? '-' }}</td>
                                    <td class="product-category">{{ $Order->notes ?? '-' }}</td>
                                    <td class="product-price" title="{{ $Order->created_at }}">{{ $Order->created_at->locale('ar')->diffForHumans() }}</td>

                                </tr>
                                @endforeach

                                @foreach ($Rolls as $Roll)
                                <tr>
                                    <td></td>

                                    <td class="product-img">
                                        <input type="hidden" class="roll_id" value="{{ $Roll->id }}">
                                        @if($Roll->order && $Roll->order->ordersImgs->first())
                                            <img src="{{ asset('storage/'.$Roll->order->ordersImgs->first()->path) }}" alt="Img placeholder">
                                        @else
                                            <img src="{{ asset('core/images/elements/apple-watch.png') }}" alt="Img placeholder">
                                        @endif
                                    </td>
                                    
                                    <td class="product-name">{{ $Roll->order->customers->name ?? '-' }} </td>
                                    <td class="product-category"><b>{{ $Roll->fabrictype ?? '-' }}   </b></td>
                                    <td class="product-category">{{ $Roll->fabricsrc ?? '-' }}</td>
                                    <td class="product-category">{{ $Roll->fabriccode ?? '-' }}</td>
                                    <td class="product-category">{{ $Roll->fabricwidth ?? '-' }}</td>
                                    <td class="product-category"><b>{{ $Roll->meters ?? '-' }}</b></td>

                                    <td>
                                        <div class="chip chip-{{ $Roll->status ? 'success' : 'info' }}">
                                            <div class="chip-body status-toggle" style="cursor: pointer">
                                                <div class="chip-text hover_action">{{ $Roll->status ? 'تم الانتهاء' : 'جاري العمل' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="product-category">
                                        <div class="chip chip-{{ $Roll->paymentstatus ? 'success' : 'danger' }}">
                                            <div class="chip-body">
                                                <div class="chip-text">{{ $Roll->paymentstatus ? 'مدفوع' : 'غير مدفوع' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="product-category">{{ $Roll->papyershild ?? '-' }}</td>
                                    <td class="product-price">{{ $Roll->price ?? '-' }}</td>
                                    <td class="product-category">{{ $Roll->notes ?? '-' }}</td>
                                    <td class="product-price" title="{{ $Roll->created_at }}">{{ $Roll->created_at->locale('ar')->diffForHumans() }}</td>

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
    <script src="{{ asset('core/js/scripts/modal/components-modal.js') }}"></script>

    @vite('resources/js/pages/rollpress.js')

@endsection
