@extends('layouts.app')

@section('css')
        @vite([

        'resources/core/vendors/css/forms/select/select2.min.css',
        'resources/core/css-rtl/plugins/forms/validation/form-validation.css',
        'resources/core/vendors/css/pickers/pickadate/pickadate.css',
        'resources/core/css-rtl/core/menu/menu-types/vertical-menu.css',
        'resources/core/css-rtl/core/colors/palette-gradient.css',
        ]) 
@endsection

    
@section('content')
<div class="app-content content">
<div class="content-overlay"></div>
<div class="header-navbar-shadow"></div>
<div class="content-wrapper">
<div class="app-content content">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">اعدادات الموقع</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">الرئيسية</a>
                        </li>
                        <li class="breadcrumb-item active"> اعدادات الموقع
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <section id="page-account-settings">
        <div class="row">
            <!-- left menu section -->
            <div class="col-md-3 mb-2 mb-md-0">
                <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                    <li class="nav-item">
                        <a class="nav-link d-flex py-75 active" id="account-pill-general" data-toggle="pill" href="#account-vertical-general" aria-expanded="true">
                            <i class="feather icon-globe mr-50 font-medium-3"></i>
                            عام
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex py-75" id="account-pill-password" data-toggle="pill" href="#account-vertical-password" aria-expanded="false">
                            <i class="feather icon-droplet mr-50 font-medium-3"></i>
                            ألوان القالب
                        </a>
                    </li>
                </ul>
            </div>
            <!-- right content section -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            
                            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" novalidate>
                                @csrf
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="account-vertical-general" aria-labelledby="account-pill-general" aria-expanded="true">
                                        <div class="media">
                                            <a href="javascript: void(0);">
                                                <img src="{{ $setting->site_logo ? asset($setting->site_logo) : asset('core/images/portrait/small/avatar-s-12.jpg') }}" class="rounded mr-75" alt="profile image" height="64" width="64">
                                            </a>
                                            <div class="media-body mt-75">
                                                <div class="col-12 px-0 d-flex flex-sm-row flex-column justify-content-start">
                                                    <label class="btn btn-sm btn-primary ml-50 mb-50 mb-sm-0 cursor-pointer" for="account-upload">رفع لوجو جديد</label>
                                                    <input type="file" id="account-upload" name="site_logo" hidden>
                                                    <!-- <button class="btn btn-sm btn-outline-warning ml-50">Reset</button> -->
                                                </div>
                                                <p class="text-muted ml-75 mt-50"><small>Allowed JPG, GIF or PNG. Max size of 800kB</small></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label for="site-name">اسم الموقع</label>
                                                        <input type="text" class="form-control" id="site-name" name="site_name" placeholder="اسم الموقع" value="{{ $setting->site_name }}" required data-validation-required-message="This field is required">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="account-vertical-password" role="tabpanel" aria-labelledby="account-pill-password" aria-expanded="false">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label for="primary-color">اللون الرئيسي</label>
                                                        <input type="color" class="form-control" id="primary-color" name="primary_color" value="{{ $setting->primary_color }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label for="secondary-color">اللون الثانوي</label>
                                                        <input type="color" class="form-control" id="secondary-color" name="secondary_color" value="{{ $setting->secondary_color }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label for="success-color">لون النجاح (Success)</label>
                                                        <input type="color" class="form-control" id="success-color" name="success_color" value="{{ $setting->success_color ?? '#28C76F' }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label for="danger-color">لون الخطر (Danger)</label>
                                                        <input type="color" class="form-control" id="danger-color" name="danger_color" value="{{ $setting->danger_color ?? '#EA5455' }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label for="warning-color">لون التحذير (Warning)</label>
                                                        <input type="color" class="form-control" id="warning-color" name="warning_color" value="{{ $setting->warning_color ?? '#FF9F43' }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label for="info-color">لون المعلومات (Info)</label>
                                                        <input type="color" class="form-control" id="info-color" name="info_color" value="{{ $setting->info_color ?? '#00CFDD' }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label for="dark-color">اللون الداكن (Dark)</label>
                                                        <input type="color" class="form-control" id="dark-color" name="dark_color" value="{{ $setting->dark_color ?? '#1E1E1E' }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-2">
                                        <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">حفظ التغييرات</button>
                                        <button type="reset" class="btn btn-outline-warning">الغاء</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    </section>
</div>
@endsection

@section('js')
    <script src="{{ asset('core/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
    <script src="{{ asset('core/vendors/js/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('core/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('core/js/scripts/pages/account-setting.js') }}"></script>
@endsection
