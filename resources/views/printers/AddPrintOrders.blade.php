@extends('layouts.app')

@section('css')
    @vite([
        'resources/core/vendors/css/file-uploaders/dropzone.min.css',
        'resources/core/vendors/css/tables/datatable/datatables.min.css',
        'resources/core/vendors/css/tables/datatable/extensions/dataTables.checkboxes.css',
        'resources/core/css-rtl/core/menu/menu-types/vertical-menu.css',
        'resources/core/css-rtl/core/colors/palette-gradient.css',
        'resources/core/css-rtl/plugins/file-uploaders/dropzone.css',
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
                            <h2 class="content-header-title float-left mb-0">اذونات التشغيل</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#"> الطباعه</a>
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
                                <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item text-danger" href="#"><i class="feather icon-trash "></i>حذف</a>
                                    <a class="dropdown-item" href="#"><i class="feather icon-archive"></i>ارشفة</a>
                                    <a class="dropdown-item" href="#"><i class="feather icon-file"></i>تفاصيل</a>
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
                                    <th>نوع الطباعه</th>
                                    <th>الامتار</th>
                                    <th>الحاله</th>
                                    <th>السعر</th>
                                    <th>التاريخ</th>
                                    <th>اجراء</th>
                                </tr>
                            </thead>
                            <tbody>


                                <tr>
                                    <td></td>
                                    <td class="product-img"><img src="{{ asset('core/images/elements/apple-watch.png') }}" alt="Img placeholder">
                                    </td>
                                    <td class="product-name">محمد محروس</td>
                                    <td class="product-category">DTF / 6Pass</td>
                                    <td class="product-category"><b>60</b></td>

                                    <td>
                                        <div class="chip chip-sucendry">
                                            <div class="chip-body">
                                                <div class="chip-text">بدء الطباعه</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="product-price">$69.99</td>
                                    <td class="product-price">1-1-2026</td>
                                    <td class="product-action">
                                        <span class=" hover_action action-info " data-toggle="modal" data-target="#xlarge"><i class="feather icon-file"></i></span>
                                        <span class=" hover_action action-edit "><i class="feather icon-edit"></i></span>
                                        <span class=" hover_action action-delete text-danger " ><i class="feather icon-trash"></i></span>

                                    </td>
                                </tr>



      

                            
                               
                                
                               
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
                                    <h4 class="text-uppercase">Thumb View Data</h4>
                                </div>
                                <div class="hide-data-sidebar">
                                    <i class="feather icon-x"></i>
                                </div>
                            </div>
                            <div class="data-items pb-3">
                                <div class="data-fields px-2 mt-3">
                                    <div class="row">
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-name">Name</label>
                                            <input type="text" class="form-control" id="data-name">
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-category"> Category </label>
                                            <select class="form-control" id="data-category">
                                                <option>Audio</option>
                                                <option>Computers</option>
                                                <option>Fitness</option>
                                                <option>Appliance</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-status">Order Status</label>
                                            <select class="form-control" id="data-status">
                                                <option>Pending</option>
                                                <option>Canceled</option>
                                                <option>Delivered</option>
                                                <option>On Hold</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-price">Price</label>
                                            <input type="text" class="form-control" id="data-price">
                                        </div>
                                        <div class="col-sm-12 data-field-col data-list-upload">
                                            <form action="#" class="dropzone dropzone-area" id="dataListUpload">
                                                <div class="dz-message">Upload Image</div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="add-data-footer d-flex justify-content-around px-3 mt-2">
                                <div class="add-data-btn">
                                    <button class="btn btn-primary">Add Data</button>
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

                    <div class="modal-size-xl mr-1 mb-1 d-inline-block">
                        <!-- Modal -->
                        <div class="modal fade text-left" id="xlarge" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel16">تفاصيل الطلب</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Basic Tables start -->
                                        <div class="row" id="basic-table">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4 class="card-title">تفاصيل الطلب</h4>
                                                    </div>
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center">
                                                                <p class="card-text mb-0 mr-1">هنا كل تفاصيل الاوردرا </p>
                                                                <div><img class="w-50 ml-5 mb-1 justify-content-start" src="{{ asset('core/images/elements/apple-watch.png') }}" alt="Img placeholder"></div>
                                                            </div>
                                                            <!-- Table with outer spacing -->
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>ID</th>
                                                                            <th>اسم العميل</th>
                                                                            <th>الماكينه</th>
                                                                            <th>طول الملف</th>
                                                                            <th>العرض</th>
                                                                            <th>تكرار</th>
                                                                            <th>عدد القطع</th>
                                                                            <th>الامتار</th>
                                                                            <th>سعر المتر</th>
                                                                            <th>الاجمالي</th>
                                                                            <th>المصمم</th>
                                                                            <th>الاوبراتور</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="row">#</th>
                                                                            <td>Leanne Graham</td>
                                                                            <td>sincere@april.biz</td>
                                                                            <td>sincere@april.biz</td>
                                                                            <td>@mdo</td>
                                                                            <td>Leanne Graham</td>
                                                                            <td>Leanne Graham</td>
                                                                            <td>Leanne Graham</td>
                                                                            <td>Leanne Graham</td>
                                                                            <td>Leanne Graham</td>
                                                                            <td>محمد محروس</td>
                                                                            <td>محمد محروس</td>
                                                                        </tr>


                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <p class="px-2"><span class="text-bold-600">  بدايه الطلب:</span> 2022-01-01</p>
                                                        <p class="px-2 text-success" ><span class="text-bold-600 ">  نهايه الطلب:</span> 2022-01-01</p>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Basic Tables end -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Accept</button>
                                    </div>
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


        @vite('resources/js/pages/AddNewOrder.js')
        @vite('resources/core/js/scripts/modal/components-modal.js')



@endsection
