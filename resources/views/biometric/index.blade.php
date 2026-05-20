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
        /* Loan system custom styles */
        .loan-badge {
            background: linear-gradient(135deg, #7367f0, #7367f0);
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
        }
        .salary-card {
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 8px;
            border-left: 4px solid #7367f0;
            background: #f8f7ff;
        }
        .loan-row-card {
            background: #fff5f5;
            border: 1px solid #ffcccc;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .net-salary-highlight {
            font-size: 1.05rem;
            font-weight: 700;
            color: #28c76f;
        }
        .net-salary-negative {
            color: #7367f0 !important;
        }
        .loan-deduction-col {
            color: #7367f0;
            font-weight: 600;
        }
        .tab-loans-indicator {
            background: #7367f0;
            color: white;
            border-radius: 50%;
            padding: 1px 6px;
            font-size: 0.7rem;
            margin-right: 4px;
        }
    </style>
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
                        <a class="nav-link" id="loans-tab" data-toggle="tab" href="#loans" role="tab" aria-selected="false">
                            @if($allLoans->count() > 0)
                                <span class="tab-loans-indicator">{{ $allLoans->count() }}</span>
                            @endif
                             السلف
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="employees-tab" data-toggle="tab" href="#employees" role="tab" aria-selected="false">الموظفين واعدادات الورديات</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- =================== Attendance Tab =================== -->
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
                                        <div class="col-md-6 d-flex align-items-center">
                                            <button type="submit" class="btn btn-primary mt-2 mr-1">رفع ومعالجة</button>
                                        </div>
                                    </div>
                                </form>
                                <form action="{{ route('biometric.clear') }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل انت متأكد من مسح جميع سجلات الحضور؟ لا يمكن التراجع عن هذا الاجراء.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger mt-2">مسح الجدول بالكامل</button>
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
                                                        
                                                        @if($row->missing_punch == 'check_in')
                                                            <div class="badge badge-warning mt-1">نسيان حضور</div>
                                                        @elseif($row->missing_punch == 'check_out')
                                                            <div class="badge badge-warning mt-1">نسيان انصراف</div>
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

                    <!-- =================== Employees Tab =================== -->
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

                    <!-- =================== Payroll Tab =================== -->
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
                                <h4 class="card-title">
                                    تقرير الرواتب الشهرية &mdash;
                                    <span class="text-primary">{{ date('F', mktime(0,0,0,$filterMonth,1)) }} {{ $filterYear }}</span>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table data-list-view">
                                        <thead>
                                            <tr>
                                                <th>الموظف</th>
                                                <th>الراتب الاساسي</th>
                                                <th>ايام الحضور</th>
                                                <th>ايام الغياب</th>
                                                <th>اجمالي التأخير (دقيقة)</th>
                                                <th>خصومات التأخير/الغياب</th>
                                                <th>اجمالي الاضافي (دقيقة)</th>
                                                <th>قيمة الاضافي</th>
                                                <th class="text-danger">خصم السلف</th>
                                                <th>صافي الراتب</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payrollData as $data)
                                                <tr>
                                                    <td><strong>{{ $data['user']->name }}</strong></td>
                                                    <td>{{ number_format($data['user']->base_salary, 2) }}</td>
                                                    <td class="text-success">{{ $data['total_attendance_days'] }}</td>
                                                    <td class="text-danger">{{ $data['total_absence_days'] }}</td>
                                                    <td class="text-warning">{{ $data['total_delay_minutes'] }}</td>
                                                    <td class="text-warning">{{ number_format($data['total_deductions'], 2) }}</td>
                                                    <td class="text-success">{{ $data['total_overtime_minutes'] }}</td>
                                                    <td class="text-success">{{ number_format($data['total_overtime_pay'], 2) }}</td>
                                                    <td class="loan-deduction-col">
                                                        @if($data['total_loan_deduction'] > 0)
                                                            <span class="loan-badge">- {{ number_format($data['total_loan_deduction'], 2) }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="net-salary-highlight {{ $data['net_salary'] < 0 ? 'net-salary-negative' : '' }}">
                                                            {{ number_format($data['net_salary'], 2) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =================== LOANS TAB =================== -->
                    <div class="tab-pane" id="loans" role="tabpanel">
                        <div class="row">
                            <!-- Add Loan Form -->
                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-header" style="background: linear-gradient(135deg,#7367f0,#7367f0); border-radius: 8px 8px 0 0;">
                                        <h4 class="card-title text-white mb-1">
                                            <i class="feather icon-plus-circle mr-1 mb-2"></i> إضافة سلفة جديدة
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('biometric.loans.store') }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label class="font-weight-bold">الموظف <span class="text-danger">*</span></label>
                                                <select name="biometric_user_id" class="form-control" required>
                                                    <option value="">-- اختر الموظف --</option>
                                                    @foreach($biometricUsers as $u)
                                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold">مبلغ السلفة (ج.م) <span class="text-danger">*</span></label>
                                                <input type="number" name="amount" class="form-control" step="0.01" min="1" placeholder="مثال: 500" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold">الشهر المراد الخصم فيه <span class="text-danger">*</span></label>
                                                <select name="month" class="form-control" required>
                                                    @for($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}" {{ $filterMonth == $i ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold">السنة <span class="text-danger">*</span></label>
                                                <select name="year" class="form-control" required>
                                                    @for($i = 2024; $i <= 2030; $i++)
                                                        <option value="{{ $i }}" {{ $filterYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold">ملاحظات</label>
                                                <textarea name="notes" class="form-control" rows="2" placeholder="سبب السلفة (اختياري)..."></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-block text-white" style="background: linear-gradient(135deg,#7367f0,#7367f0); border-radius: 8px 8px ;">
                                                <i class="feather icon-check-circle  mr-1"></i> حفظ السلفة وخصمها من الراتب
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Loans Listing -->
                            <div class="col-md-8">
                                <div class="card shadow-sm">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title mb-0">
                                            سلف شهر {{ date('F', mktime(0,0,0,$filterMonth,1)) }} {{ $filterYear }}
                                        </h4>
                                        <span class="badge badge-danger badge-pill">{{ $allLoans->count() }} سلفة</span>
                                    </div>
                                    <div class="card-body">
                                        @if($allLoans->isEmpty())
                                            <div class="text-center py-4">
                                                <i class="feather icon-inbox" style="font-size:3rem; color:#ccc;"></i>
                                                <p class="text-muted mt-2">لا توجد سلف مسجلة لهذا الشهر.</p>
                                            </div>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-hover table-bordered">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>الموظف</th>
                                                            <th>المبلغ</th>
                                                            <th>الشهر / السنة</th>
                                                            <th>الملاحظات</th>
                                                            <th>التاريخ</th>
                                                            <th>حذف</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($allLoans as $loan)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>
                                                                    <strong>{{ $loan->biometricUser->name ?? '—' }}</strong>
                                                                </td>
                                                                <td>
                                                                    <span class="loan-badge">{{ number_format($loan->amount, 2) }} ج.م</span>
                                                                </td>
                                                                <td>
                                                                    {{ date('F', mktime(0,0,0,$loan->month,1)) }} {{ $loan->year }}
                                                                </td>
                                                                <td>{{ $loan->notes ?: '—' }}</td>
                                                                <td>{{ $loan->created_at->format('Y-m-d') }}</td>
                                                                <td>
                                                                    <form action="{{ route('biometric.loans.destroy', $loan->id) }}" method="POST"
                                                                          onsubmit="return confirm('هل تريد حذف هذه السلفة؟ سيتأثر الراتب.');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                                            <i class="feather icon-trash-2"></i>
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-danger">
                                                            <td colspan="2"><strong>الإجمالي</strong></td>
                                                            <td colspan="5"><strong class="text-danger">{{ number_format($allLoans->sum('amount'), 2) }} ج.م</strong></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Per-employee summary -->
                                @if($allLoans->isNotEmpty())
                                    <div class="card mt-2 shadow-sm">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">ملخص السلف لكل موظف</h5>
                                        </div>
                                        <div class="card-body p-2">
                                            @foreach($allLoans->groupBy('biometric_user_id') as $userId => $userLoans)
                                                <div class="salary-card">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>{{ $userLoans->first()->biometricUser->name ?? '—' }}</strong>
                                                            <small class="text-muted d-block">{{ $userLoans->count() }} سلفة</small>
                                                        </div>
                                                        <span class="loan-badge" style="font-size:1rem; padding: 5px 14px;">
                                                            {{ number_format($userLoans->sum('amount'), 2) }} ج.م
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- =================== END LOANS TAB =================== -->

                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
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
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="feather icon-file"></i> Excel',
                        className: 'btn btn-white btn-sm'
                    }
                ]
            });

            // Auto-switch to loans tab if 'loans' anchor in URL
            if (window.location.hash === '#loans') {
                $('#loans-tab').tab('show');
            }

            // Show loans tab if success flash came from a loan store/delete
            @if(session('success') && str_contains(session('success'), 'سلف'))
                $('#loans-tab').tab('show');
            @endif
        });
    </script>
@endsection
