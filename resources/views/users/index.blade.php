@extends('layouts.app')

@section('css')
    @vite([
        'resources/core/vendors/css/tables/datatable/datatables.min.css',
        'resources/core/vendors/css/tables/datatable/extensions/dataTables.checkboxes.css',
        'resources/core/css-rtl/core/menu/menu-types/vertical-menu.css',
        'resources/core/css-rtl/core/colors/palette-gradient.css',
        'resources/core/css-rtl/pages/data-list-view.css',
        'resources/core/css-rtl/custom-rtl.css',
    ])
    <style>
        .role-checkbox {
            margin-right: 5px;
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
                            <h2 class="content-header-title float-left mb-0">المستخدمين</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item active">ادارة المستخدمين
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
                    <div class="action-btns d-none">
                        <div class="btn-dropdown mr-1 mb-1">
                            <div class="btn-group dropdown actions-dropodown">
                                <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light close_modal" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item text-danger" href="#"><i class="feather icon-trash "></i>حذف</a>
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
                                    <th>الاسم</th>
                                    <th>اسم المستخدم</th>
                                    <th>البريد الالكتروني</th>
                                    <th>الأدوار</th>
                                    <th>تاريخ الانشاء</th>
                                    <th>اجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td><input type="hidden" class="user_id" value="{{ $user->id }}"></td>
                                    <td class="product-name user-name">{{ $user->name }}</td>
                                    <td class="product-category user-username">{{ $user->username }}</td>
                                    <td class="product-category user-email">{{ $user->email }}</td>
                                    <td class="product-category">
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-success">{{ $role->name }}</span>
                                        @endforeach
                                        <div class="d-none user-roles">
                                            @foreach($user->roles as $role)
                                                <span class="role-item">{{ $role->name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="product-price">{{ $user->created_at->format('Y-m-d') }}</td>
                                    <td class="product-action">
                                        <span class="hover_action action-edit"
                                              data-id="{{ $user->id }}"
                                              data-name="{{ $user->name }}"
                                              data-username="{{ $user->username }}"
                                              data-email="{{ $user->email }}"
                                              data-base_salary="{{ $user->base_salary }}"
                                              data-working_hours="{{ $user->working_hours }}"
                                              data-shift_start="{{ $user->shift_start }}"
                                              data-shift_end="{{ $user->shift_end }}"
                                              data-overtime_rate="{{ $user->overtime_rate }}"
                                              data-joining_date="{{ $user->joining_date }}"
                                              data-resignation_date="{{ $user->resignation_date }}"
                                              >
                                            <i class="feather icon-edit"></i>
                                        </span>
                                        <span class="hover_action action-delete text-danger"><i class="feather icon-trash"></i></span>
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
                                    <h4 class="text-uppercase">ادارة المستخدم</h4>
                                </div>
                                <div class="hide-data-sidebar">
                                    <i class="feather icon-x"></i>
                                </div>
                            </div>
                            <div class="data-items pb-3">
                                <div class="data-fields px-2 mt-3">
                                    <div class="row">
                                        <input type="hidden" id="data-id">
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-name">اسم المستخدم</label>
                                            <input type="text" class="form-control" id="data-name">
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-username">Username</label>
                                            <input type="text" class="form-control" id="data-username">
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-email">البريد الالكتروني</label>
                                            <input type="email" class="form-control" id="data-email">
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-password">كلمة المرور</label>
                                            <input type="password" class="form-control" id="data-password" placeholder="اتركه فارغا اذا كنت لا تريد تغييره">
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-base-salary">الراتب الأساسي</label>
                                            <input type="number" class="form-control" id="data-base-salary" step="0.01">
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-overtime-rate">معدل الوقت الإضافي (x)</label>
                                            <input type="number" class="form-control" id="data-overtime-rate" step="0.1" value="1.5">
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-working-hours">ساعات العمل</label>
                                            <input type="number" class="form-control" id="data-working-hours" value="8">
                                        </div>
                                        <div class="col-sm-6 data-field-col">
                                            <label for="data-shift-start">بداية الشفت</label>
                                            <input type="time" class="form-control" id="data-shift-start">
                                        </div>
                                        <div class="col-sm-6 data-field-col">
                                            <label for="data-shift-end">نهاية الشفت</label>
                                            <input type="time" class="form-control" id="data-shift-end">
                                        </div>
                                        <div class="col-sm-6 data-field-col">
                                            <label for="data-joining-date">تاريخ الانضمام</label>
                                            <input type="date" class="form-control" id="data-joining-date">
                                        </div>
                                        <div class="col-sm-6 data-field-col">
                                            <label for="data-resignation-date">تاريخ الاستقالة</label>
                                            <input type="date" class="form-control" id="data-resignation-date">
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label class="mb-1">الأدوار</label>
                                            <div class="row">
                                            @foreach($roles as $role)
                                                <div class="col-md-6">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input role-checkbox" id="role_{{ $role->id }}" value="{{ $role->name }}">
                                                        <label class="custom-control-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="add-data-footer d-flex justify-content-around px-3 mt-2">
                                <div class="add-data-btn">
                                    <button class="btn btn-primary" id="saveDataBtn">حفظ البيانات</button>
                                </div>
                                <div class="cancel-data-btn">
                                    <button class="btn btn-outline-danger">الغاء</button>
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

@endsection

@section('js')
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.checkboxes.min.js') }}"></script>
        <script src="{{ asset('core/js/scripts/ui/data-list-view.js') }}"></script>

        @vite('resources/js/pages/users.js')
@endsection
