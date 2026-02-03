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
@endsection

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">نظام الحضور بالبصمة</h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="content-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" id="attendance-tab" data-toggle="tab" href="#attendance" role="tab" aria-selected="true">سجل الحضور</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="payroll-tab" data-toggle="tab" href="#payroll" role="tab" aria-selected="false">تقرير الرواتب</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="employees-tab" data-toggle="tab" href="#employees" role="tab" aria-selected="false">الموظفين واعدادات الورديات</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Attendance Tab -->
                    <div class="tab-pane active" id="attendance" role="tabpanel">
                        <!-- Upload Section -->
                        <section class="card mb-2">
                            <div class="card-header">
                                <h4 class="card-title">رفع ملف البصمة (.dat)</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('biometric.upload') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>اختر الملف</label>
                                                <input type="file" name="attendance_file" class="form-control" accept=".dat,.txt" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary mt-2">رفع ومعالجة</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </section>

                        <!-- Filter Section -->
                        <section class="card mb-2">
                            <div class="card-body">
                                <form action="{{ route('biometric.index') }}" method="GET">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select name="biometric_user_id" class="form-control">
                                                <option value="">كل الموظفين</option>
                                                @foreach($biometricUsers as $u)
                                                    <option value="{{ $u->id }}" {{ request('biometric_user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="month" class="form-control">
                                                @for($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}" {{ request('month', now()->month) == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="year" class="form-control">
                                                @for($i = 2024; $i <= 2030; $i++)
                                                    <option value="{{ $i }}" {{ request('year', now()->year) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary btn-block">فلتر</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </section>

                        <!-- Data Table -->
                        <div class="card">
                           <div class="card-body">
                                 <div class="table-responsive">
                                    <table class="table data-list-view">
                                        <thead>
                                            <tr>
                                                <th>التاريخ</th>
                                                <th>الموظف</th>
                                                <th>الوردية</th>
                                                <th>حضور</th>
                                                <th>انصراف</th>
                                                <th>تأخير (دقيقة)</th>
                                                <th>اضافي (دقيقة)</th>
                                                <th>قيمة الاضافي</th>
                                                <th>الحالة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($attendances as $row)
                                                <tr class="{{ $row->status == 'absent' ? 'table-danger' : '' }} {{ $row->is_friday ? 'table-warning' : '' }}">
                                                    <td>{{ $row->date->format('Y-m-d') }}</td>
                                                    <td>{{ $row->biometricUser->name ?? $row->biometric_user_id }}</td>
                                                    <td>
                                                        <small>{{ $row->shift_start }} - {{ $row->shift_end }}</small>
                                                    </td>
                                                    <td>{{ $row->check_in ? $row->check_in->format('H:i') : '-' }}</td>
                                                    <td>{{ $row->check_out ? $row->check_out->format('H:i') : '-' }}</td>
                                                    
                                                    <td class="{{ $row->delay_minutes > 0 ? 'text-danger' : '' }}">{{ $row->delay_minutes }}</td>
                                                    
                                                    <td class="{{ $row->overtime_minutes > 0 ? 'text-success' : '' }}">{{ $row->overtime_minutes }}</td>
                                                    <td>{{ $row->overtime_pay }}</td>
                                                    
                                                    <td>
                                                        @if($row->is_friday)
                                                            <span class="badge badge-warning">جمعة/عطلة</span>
                                                        @elseif($row->status == 'present')
                                                            <span class="badge badge-success">حضور</span>
                                                        @elseif($row->status == 'absent')
                                                            <span class="badge badge-danger">غياب</span>
                                                        @else
                                                            <span class="badge badge-secondary">{{ $row->status }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                           </div>
                        </div>
                    </div>

                    <!-- Employees Tab -->
                    <div class="tab-pane" id="employees" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">قائمة الموظفين (من جهاز البصمة)</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID (البصمة)</th>
                                                <th>الاسم</th>
                                                <th>بداية الشفت</th>
                                                <th>نهاية الشفت</th>
                                                <th>الراتب الاساسي</th>
                                                <th>معدل الاضافي</th>
                                                <th>اجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($biometricUsers as $u)
                                                <tr>
                                                    <form action="{{ route('biometric.users.update', $u->id) }}" method="POST">
                                                        @csrf
                                                        <td>{{ $u->biometric_id }}</td>
                                                        <td>
                                                            <input type="text" name="name" class="form-control" value="{{ $u->name }}">
                                                        </td>
                                                        <td>
                                                            <input type="time" name="shift_start" class="form-control" value="{{ $u->shift_start }}">
                                                        </td>
                                                        <td>
                                                            <input type="time" name="shift_end" class="form-control" value="{{ $u->shift_end }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" name="base_salary" class="form-control" value="{{ $u->base_salary }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.1" name="overtime_rate" class="form-control" value="{{ $u->overtime_rate }}">
                                                        </td>
                                                        <td>
                                                            <button type="submit" class="btn btn-sm btn-primary">حفظ</button>
                                                        </td>
                                                    </form>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payroll Tab -->
                    <div class="tab-pane" id="payroll" role="tabpanel">
                         <!-- Absences Generation Button -->
                        <div class="row mb-2">
                             <div class="col-12">
                                <form action="{{ route('biometric.generate_absences') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="month" value="{{ request('month', now()->month) }}">
                                    <input type="hidden" name="year" value="{{ request('year', now()->year) }}">
                                    <button type="submit" class="btn btn-warning">توليد ايام الغياب وخصمها</button>
                                </form>
                             </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">تقرير الرواتب الشهرية</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table data-list-view">
                                        <thead>
                                            <tr>
                                                <th>الموظف</th>
                                                <th>الراتب الاساسي</th>
                                                <th>اجمالي التأخير (دقيقة)</th>
                                                <th>اجمالي الخصومات</th>
                                                <th>اجمالي الاضافي (دقيقة)</th>
                                                <th>قيمة الاضافي</th>
                                                <th>صافي الراتب</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payrollData as $data)
                                                <tr>
                                                    <td>{{ $data['user']->name }}</td>
                                                    <td>{{ number_format($data['user']->base_salary, 2) }}</td>
                                                    <td class="text-danger">{{ $data['total_delay_minutes'] }}</td>
                                                    <td class="text-danger">{{ number_format($data['total_deductions'], 2) }}</td>
                                                    <td class="text-success">{{ $data['total_overtime_minutes'] }}</td>
                                                    <td class="text-success">{{ number_format($data['total_overtime_pay'], 2) }}</td>
                                                    <td class="font-weight-bold">{{ number_format($data['net_salary'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
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
    
    <script>
        $(document).ready(function() {
            $('.data-list-view').DataTable({
                responsive: false,
                dom: '<"top"<"actions action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
                order: [[0, "desc"]],
                bInfo: false,
                pageLength: 50,
                buttons: [
                    {
                        extend: 'print',
                        text: '<i class="feather icon-printer"></i> طباعة',
                        className: 'btn btn-white btn-sm'
                    }
                ]
            });
        });
    </script>
@endsection
