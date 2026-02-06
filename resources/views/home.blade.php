@extends('layouts.app')

@section('css')

<link rel="stylesheet" href="{{ asset('core/vendors/css/charts/apexcharts.css') }} ">

@endsection

@section('content')
    <!-- BEGIN: Header-->

    <!-- <ul class="main-search-list-defaultlist d-none">
        <li class="d-flex align-items-center"><a class="pb-25" href="#">
                <h6 class="text-primary mb-0">Files</h6>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
                <div class="d-flex">
                    <div class="mr-50"><img src="{{ asset('core/images/icons/xls.png') }}" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Two new item submitted</p><small class="text-muted">Marketing Manager</small>
                    </div>
                </div><small class="search-data-size mr-50 text-muted">&apos;17kb</small>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
                <div class="d-flex">
                    <div class="mr-50"><img src="{{ asset('core/images/icons/jpg.png') }}" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">52 JPG file Generated</p><small class="text-muted">FontEnd Developer</small>
                    </div>
                </div><small class="search-data-size mr-50 text-muted">&apos;11kb</small>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
                <div class="d-flex">
                    <div class="mr-50"><img src="{{ asset('core/images/icons/pdf.png') }}" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">25 PDF File Uploaded</p><small class="text-muted">Digital Marketing Manager</small>
                    </div>
                </div><small class="search-data-size mr-50 text-muted">&apos;150kb</small>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
                <div class="d-flex">
                    <div class="mr-50"><img src="{{ asset('core/images/icons/doc.png') }}" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Anna_Strong.doc</p><small class="text-muted">Web Designer</small>
                    </div>
                </div><small class="search-data-size mr-50 text-muted">&apos;256kb</small>
            </a></li>
        <li class="d-flex align-items-center"><a class="pb-25" href="#">
                <h6 class="text-primary mb-0">Members</h6>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
                <div class="d-flex align-items-center">
                    <div class="avatar mr-50"><img src="{{ asset('core/images/portrait/small/avatar-s-8.jpg') }}" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">John Doe</p><small class="text-muted">UI designer</small>
                    </div>
                </div>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
                <div class="d-flex align-items-center">
                    <div class="avatar mr-50"><img src="{{ asset('core/images/portrait/small/avatar-s-1.jpg') }}" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Michal Clark</p><small class="text-muted">FontEnd Developer</small>
                    </div>
                </div>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
                <div class="d-flex align-items-center">
                    <div class="avatar mr-50"><img src="{{ asset('core/images/portrait/small/avatar-s-14.jpg') }}" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Milena Gibson</p><small class="text-muted">Digital Marketing Manager</small>
                    </div>
                </div>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
                <div class="d-flex align-items-center">
                    <div class="avatar mr-50"><img src="{{ asset('core/images/portrait/small/avatar-s-6.jpg') }}" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Anna Strong</p><small class="text-muted">Web Designer</small>
                    </div>
                </div>
            </a></li>
    </ul>
    <ul class="main-search-list-defaultlist-other-list d-none">
        <li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100 py-50">
                <div class="d-flex justify-content-start"><span class="mr-75 feather icon-alert-circle"></span><span>No results found.</span></div>
            </a></li>
    </ul> -->
    <!-- END: Header-->




    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">


                <!-- Dashboard Ecommerce Starts -->
                <section id="dashboard-ecommerce">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex flex-column align-items-start pb-0">
                                    <div class="avatar bg-rgba-primary p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-users text-primary font-medium-5"></i>
                                        </div>
                                    </div>
                                    <h2 class="text-bold-700 mt-1" id="customers-gained-total">0</h2>
                                    <p class="mb-0">العملاء</p>
                                </div>
                                <div class="card-content">
                                    <div  id="line-area-chart-1"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex flex-column align-items-start pb-0">
                                    <div class="avatar bg-rgba-success p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-star text-success font-medium-5"></i>
                                        </div>
                                    </div>
                                    <h2 class="text-bold-700 mt-1" id="stras-orders-total">0</h2>
                                    <p class="mb-0">اوردرات استراس</p>
                                </div>
                                <div class="card-content">
                                    <div id="line-area-chart-2"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex flex-column align-items-start pb-0">
                                    <div class="avatar bg-rgba-danger p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-disc text-danger font-medium-5"></i>
                                        </div>
                                    </div>
                                    <h2 class="text-bold-700 mt-1" id="tarter-orders-total">0</h2>
                                    <p class="mb-0">اوردرات ترتر</p>
                                </div>
                                <div class="card-content">
                                    <div  id="line-area-chart-3"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex flex-column align-items-start pb-0">
                                    <div class="avatar bg-rgba-warning p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-package text-warning font-medium-5"></i>
                                        </div>
                                    </div>
                                    <h2 class="text-bold-700 mt-1" id="orders-received-total">0</h2>
                                    <p class="mb-0">اوردرات طباعه </p>
                                </div>
                                <div class="card-content">
                                    <div  id="line-area-chart-4"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8 col-md-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-end">
                                    <h4 class="card-title" id="chart-title">Sublimation</h4>
                                    <div class="d-flex">
                                        <div class="dropdown chart-dropdown mr-1">
                                            <button class="btn btn-sm border-0 dropdown-toggle px-0" type="button" id="dropdownMachine" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Sublimation
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMachine">
                                                <a class="dropdown-item revenue-machine-item" href="#" data-machine="sublimation">Sublimation</a>
                                                <a class="dropdown-item revenue-machine-item" href="#" data-machine="dtf">DTF</a>
                                            </div>
                                        </div>
                                        <div class="dropdown chart-dropdown">
                                        <button class="btn btn-sm border-0 dropdown-toggle px-0" type="button" id="dropdownItem2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            ايام الاسبوع 
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownItem2">
                                            <a class="dropdown-item revenue-period-item" href="#" data-period="week">ايام الاسبوع</a>
                                            <a class="dropdown-item revenue-period-item" href="#" data-period="month">ايام الشهر</a>
                                            <a class="dropdown-item revenue-period-item" href="#" data-period="year">شهور السنة</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <div class="card-content">
                                    <div class="card-body pb-0">
                                        <div class="d-flex justify-content-start">
                                            <div class="mr-2">
                                                <p class="mb-50 text-bold-600">الفتره الحاليه</p>
                                                <h2 class="text-bold-400">
                                                    <span class="text-success" id="current-revenue">0</span>
                                                </h2>
                                            </div>
                                            <div>
                                                <p class="mb-50 text-bold-600">الفتره السابقه</p>
                                                <h2 class="text-bold-400">
                                                    <span id="last-revenue">0</span>
                                                </h2>
                                            </div>

                                        </div>
                                        <div id="revenue-chart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row pb-50">
                                            <div class="col-lg-6 col-12 d-flex justify-content-between flex-column order-lg-1 order-2 mt-lg-0 mt-2">
                                                <div>
                                                    <h2 class="text-bold-700 mb-25"></h2>
                                                    <p class="text-bold-500 mb-75">الاحبار في المخزن</p>
                                                    <h5 class="font-medium-2">
                                                        <!-- <span class="text-success">+5.2% </span>
                                                        <span>vs last 7 days</span> -->
                                                    </h5>
                                                </div>
                                                <!-- <a href="#" class="btn btn-primary shadow">View Details <i class="feather icon-chevrons-right"></i></a> -->
                                            </div>
                                            <div class=" col-12 d-flex justify-content-between flex-column text-right order-lg-2 order-1">
                                                <div class="dropdown chart-dropdown">
                                                    <!-- <button class="btn btn-sm border-0 dropdown-toggle p-0" type="button" id="dropdownItem5" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Last 7 Days
                                                    </button> -->
                                                    <!-- <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownItem5">
                                                        <a class="dropdown-item" href="#">Last 28 Days</a>
                                                        <a class="dropdown-item" href="#">Last Month</a>
                                                        <a class="dropdown-item" href="#">Last Year</a>
                                                    </div> -->
                                                </div>
                                                <div id="avg-session-chart"></div>
                                            </div>
                                        </div>
                                        <hr />
                                        <div class="row avg-sessions pt-50">
                                            <div class="col-6">
                                                <p class="mb-0">ورق Sublimation: <span id="paper-stock-sub">0</span> متر</p>
                                                <div class="progress sub_bar  mt-25">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="100" aria-valuemax="100" style="width:100%"></div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-0">ورق DTF: <span id="paper-stock-dtf">0</span> متر</p>
                                                <div class="progress dtf_bar  mt-25">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="100" aria-valuemax="100" style="width:100%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between pb-0">
                                    <h4 class="">استهلاك الحبر والورق</h4>
                                    <div class="dropdown chart-dropdown">
                                        <button class="btn btn-sm border-0  dropdown-toggle p-0" type="button" id="dropdownItem2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            7 أيام
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownItem2">
                                            <a class="dropdown-item inventory-period" href="#" data-period="7_days">7 أيام</a>
                                            <a class="dropdown-item inventory-period" href="#" data-period="28_days">28 يوم</a>
                                            <a class="dropdown-item inventory-period" href="#" data-period="month">شهر</a>
                                            <a class="dropdown-item inventory-period" href="#" data-period="year">سنة</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-content">
                                    <div class="card-body" >
                                        <div id="product-order-chart" class="mb-1"></div>
                                        <!-- Metric 1: Paper Sublimation -->
                                        <div class="chart-info d-flex justify-content-between mb-1">
                                            <div class="series-info d-flex align-items-center">
                                                <i class="feather  icon-droplet text-bold-700 text-primary"></i>
                                                <span class="text-bold-600 ml-50"> ورق سبلميشن</span>
                                            </div>
                                            <div class="product-result">
                                                <span id="stat-paper-sub">Loading...</span>
                                            </div>
                                        </div>

                                        <!-- Metric 2: Paper DTF -->
                                        <div class="chart-info d-flex justify-content-between mb-1">
                                            <div class="series-info d-flex align-items-center">
                                                <i class="feather  icon-droplet text-bold-700 text-warning"></i>
                                                <span class="text-bold-600 ml-50"> ورق DTF</span>
                                            </div>
                                            <div class="product-result">
                                                <span id="stat-paper-dtf">Loading...</span>
                                            </div>
                                        </div>

                                        <!-- Metric 3: Ink Sublimation -->
                                        <div class="chart-info d-flex justify-content-between mb-1">
                                            <div class="series-info d-flex align-items-center">
                                                <i class="feather  icon-droplet text-bold-700 text-danger"></i>
                                                <span class="text-bold-600 ml-50"> حبر سبلميشن</span>
                                            </div>
                                            <div class="product-result">
                                                <span id="stat-ink-sub">Loading...</span>
                                            </div>
                                        </div>

                                        <!-- Metric 4: Ink DTF -->
                                        <div class="chart-info d-flex justify-content-between mb-75">
                                            <div class="series-info d-flex align-items-center">
                                                <i class="feather  icon-droplet text-bold-700 text-success"></i>
                                                <span class="text-bold-600 ml-50"> حبر DTF</span>
                                            </div>
                                            <div class="product-result">
                                                <span id="stat-ink-dtf">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-lg-8 col-md-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-end">
                                    <h4 class="card-title" id="stras-chart-title">ورق استراس </h4>
                                    <div class="d-flex">
                                        <div class="dropdown chart-dropdown mr-1">
                                            <button class="btn btn-sm border-0 dropdown-toggle px-0" type="button" id="dropdownStrasMachine" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                ستراس
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownStrasMachine">
                                                <a class="dropdown-item stras-machine-item" href="#" data-machine="stras">ستراس</a>
                                                <a class="dropdown-item stras-machine-item" href="#" data-machine="ترتر">ترتر</a>
                                            </div>
                                        </div>
                                        <div class="dropdown chart-dropdown">
                                        <button class="btn btn-sm border-0 dropdown-toggle px-0" type="button" id="dropdownStrasPeriod" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            ايام الاسبوع 
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownStrasPeriod">
                                            <a class="dropdown-item stras-period-item" href="#" data-period="week">ايام الاسبوع</a>
                                            <a class="dropdown-item stras-period-item" href="#" data-period="month">ايام الشهر</a>
                                            <a class="dropdown-item stras-period-item" href="#" data-period="year">شهور السنة</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <div class="card-content">
                                    <div class="card-body pb-0">
                                        <div class="d-flex justify-content-start">
                                            <div class="mr-2">
                                                <p class="mb-50 text-bold-600"> الحالي</p>
                                                <h2 class="text-bold-400">
                                                    <span class="text-success" id="current-stras-consumption">0</span>
                                                </h2>
                                            </div>
                                            <div>
                                                <p class="mb-50 text-bold-600"> السابق</p>
                                                <h2 class="text-bold-400">
                                                    <span id="last-stras-consumption">0</span>
                                                </h2>
                                            </div>

                                        </div>
                                        <div id="stras-consumption-chart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class='row'>
                        
                        <div class="col-md-8 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">العملاء الجدد والمتمرين</h4>
                                    <div class="dropdown chart-dropdown">
                                        <button class="btn btn-sm border-0 dropdown-toggle px-0" type="button" id="clientRetentionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            ايام الشهر
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="clientRetentionDropdown">
                                            <a class="dropdown-item client-retention-period" href="#" data-period="week">ايام الاسبوع</a>
                                            <a class="dropdown-item client-retention-period" href="#" data-period="month">ايام الشهر</a>
                                            <a class="dropdown-item client-retention-period" href="#" data-period="year">شهور السنة</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div id="client-retention-chart">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- <div class="row">
                        <div class="col-lg-4 col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-end">
                                    <h4>Sessions By Device</h4>
                                    <div class="dropdown chart-dropdown">
                                        <button class="btn btn-sm border-0 dropdown-toggle px-0" type="button" id="dropdownItem1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Last 7 Days
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownItem1">
                                            <a class="dropdown-item" href="#">Last 28 Days</a>
                                            <a class="dropdown-item" href="#">Last Month</a>
                                            <a class="dropdown-item" href="#">Last Year</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-content">
                                    <div class="card-body pt-0">
                                        <div id="session-chart" class="mb-1"></div>
                                        <div class="chart-info d-flex justify-content-between mb-1">
                                            <div class="series-info d-flex align-items-center">
                                                <i class="feather icon-monitor font-medium-2 text-primary"></i>
                                                <span class="text-bold-600 mx-50">Desktop</span>
                                                <span> - 58.6%</span>
                                            </div>
                                            <div class="series-result">
                                                <span>2%</span>
                                                <i class="feather icon-arrow-up text-success"></i>
                                            </div>
                                        </div>
                                        <div class="chart-info d-flex justify-content-between mb-1">
                                            <div class="series-info d-flex align-items-center">
                                                <i class="feather icon-tablet font-medium-2 text-warning"></i>
                                                <span class="text-bold-600 mx-50">Mobile</span>
                                                <span> - 34.9%</span>
                                            </div>
                                            <div class="series-result">
                                                <span>8%</span>
                                                <i class="feather icon-arrow-up text-success"></i>
                                            </div>
                                        </div>
                                        <div class="chart-info d-flex justify-content-between mb-50">
                                            <div class="series-info d-flex align-items-center">
                                                <i class="feather icon-tablet font-medium-2 text-danger"></i>
                                                <span class="text-bold-600 mx-50">Tablet</span>
                                                <span> - 6.5%</span>
                                            </div>
                                            <div class="series-result">
                                                <span>-5%</span>
                                                <i class="feather icon-arrow-down text-danger"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <div class="card chat-application">
                                <div class="card-header">
                                    <h4 class="card-title">Chat</h4>
                                </div>
                                <div class="chat-app-window">
                                    <div class="user-chats">
                                        <div class="chats">
                                            <div class="chat">
                                                <div class="chat-avatar">
                                                    <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="right" title="" data-original-title="">
                                                        <img src="{{ asset('core/images/portrait/small/avatar-s-2.jpg') }}" alt="avatar" height="40" width="40" />
                                                    </a>
                                                </div>
                                                <div class="chat-body">
                                                    <div class="chat-content">
                                                        <p>Cake sesame snaps cupcake gingerbread</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chat chat-left">
                                                <div class="chat-avatar mt-50">
                                                    <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="left" title="" data-original-title="">
                                                        <img src="{{ asset('core/images/portrait/small/avatar-s-5.jpg') }}" alt="avatar" height="40" width="40" />
                                                    </a>
                                                </div>
                                                <div class="chat-body">
                                                    <div class="chat-content">
                                                        <p>Apple pie pie jujubes chupa chups muffin</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chat">
                                                <div class="chat-avatar">
                                                    <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="right" title="" data-original-title="">
                                                        <img src="{{ asset('core/images/portrait/small/avatar-s-2.jpg') }}" alt="avatar" height="40" width="40" />
                                                    </a>
                                                </div>
                                                <div class="chat-body">
                                                    <div class="chat-content">
                                                        <p>Chocolate cake</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chat chat-left">
                                                <div class="chat-avatar mt-50">
                                                    <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="left" title="" data-original-title="">
                                                        <img src="{{ asset('core/images/portrait/small/avatar-s-5.jpg') }}" alt="avatar" height="40" width="40" />
                                                    </a>
                                                </div>
                                                <div class="chat-body">
                                                    <div class="chat-content">
                                                        <p>Donut sweet pie oat cake dragée fruitcake</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chat">
                                                <div class="chat-avatar mt-50">
                                                    <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="right" title="" data-original-title="">
                                                        <img src="{{ asset('core/images/portrait/small/avatar-s-2.jpg') }}" alt="avatar" height="40" width="40" />
                                                    </a>
                                                </div>
                                                <div class="chat-body">
                                                    <div class="chat-content">
                                                        <p>Liquorice chocolate bar jelly beans icing</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chat chat-left">
                                                <div class="chat-avatar mt-50">
                                                    <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="left" title="" data-original-title="">
                                                        <img src="{{ asset('core/images/portrait/small/avatar-s-5.jpg') }}" alt="avatar" height="40" width="40" />
                                                    </a>
                                                </div>
                                                <div class="chat-body">
                                                    <div class="chat-content">
                                                        <p>Powder toffee tootsie roll macaroon cupcake.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chat">
                                                <div class="chat-avatar">
                                                    <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="right" title="" data-original-title="">
                                                        <img src="{{ asset('core/images/portrait/small/avatar-s-2.jpg') }}" alt="avatar" height="40" width="40" />
                                                    </a>
                                                </div>
                                                <div class="chat-body">
                                                    <div class="chat-content">
                                                        <p>Apple pie oat cake brownie cotton candy cupcake chocolate bar dessert.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chat chat-left">
                                                <div class="chat-avatar mt-50">
                                                    <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="left" title="" data-original-title="">
                                                        <img src="{{ asset('core/images/portrait/small/avatar-s-5.jpg') }}" alt="avatar" height="40" width="40" />
                                                    </a>
                                                </div>
                                                <div class="chat-body">
                                                    <div class="chat-content">
                                                        <p>Biscuit cake jujubes carrot cake topping sweet cake.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="chat-footer">
                                        <div class="card-body d-flex justify-content-around pt-0">
                                            <input type="text" class="form-control mr-50" placeholder="Type your Message">
                                            <button type="button" class="btn btn-icon btn-primary"><i class="feather icon-navigation"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between pb-0">
                                    <h4 class="card-title">Customers</h4>
                                    <div class="dropdown chart-dropdown">
                                        <button class="btn btn-sm border-0 dropdown-toggle px-0" type="button" id="dropdownItem3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Last 7 Days
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownItem3">
                                            <a class="dropdown-item" href="#">Last 28 Days</a>
                                            <a class="dropdown-item" href="#">Last Month</a>
                                            <a class="dropdown-item" href="#">Last Year</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-content">
                                    <div class="card-body py-0">
                                        <div id="customer-chart"></div>
                                    </div>
                                    <ul class="list-group list-group-flush customer-info">
                                        <li class="list-group-item d-flex justify-content-between ">
                                            <div class="series-info">
                                                <i class="fa fa-circle font-small-3 text-primary"></i>
                                                <span class="text-bold-600">New</span>
                                            </div>
                                            <div class="product-result">
                                                <span>890</span>
                                            </div>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between ">
                                            <div class="series-info">
                                                <i class="fa fa-circle font-small-3 text-warning"></i>
                                                <span class="text-bold-600">Returning</span>
                                            </div>
                                            <div class="product-result">
                                                <span>258</span>
                                            </div>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between ">
                                            <div class="series-info">
                                                <i class="fa fa-circle font-small-3 text-danger"></i>
                                                <span class="text-bold-600">Referrals</span>
                                            </div>
                                            <div class="product-result">
                                                <span>149</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </section>
                <!-- Dashboard Ecommerce ends -->

            </div>
        </div>
    </div>
    <!-- END: Content-->


@endsection


@section('js')


<!-- <script src="{{ asset('core/vendors/js/charts/apexcharts.min.js') }}"></script> -->
@vite('resources/js/pages/dashboard.js')

@endsection
