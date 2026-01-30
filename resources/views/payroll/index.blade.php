@extends('layouts.app')

@section('css')
        'resources/core/vendors/css/tables/datatable/datatables.min.css',
        'resources/core/vendors/css/tables/datatable/extensions/dataTables.checkboxes.css',
        'resources/core/css-rtl/core/menu/menu-types/vertical-menu.css',
        'resources/core/css-rtl/core/colors/palette-gradient.css',
        'resources/core/css-rtl/plugins/file-uploaders/dropzone.css',
        'resources/core/css-rtl/pages/data-list-view.css',
        'resources/core/css-rtl/custom-rtl.css',
@endsection

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">تقرير الرواتب الشهرية</h4>
            </div>
            <div class="card-content">
                <div class="card-body card-dashboard">
                    <form action="{{ route('payroll.index') }}" method="GET" class="mb-2">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="month" class="form-control">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="year" class="form-control">
                                    @for($i = 2024; $i <= 2030; $i++)
                                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-block">عرض</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped dataex-html5-selectors">
                            <thead>
                                <tr>
                                    <th>الموظف</th>
                                    <th>أيام الحضور</th>
                                    <th>أيام الجمعة (أجازة)</th>
                                    <th>إجمالي أيام العمل</th>
                                    <th>تأخير (دقيقة)</th>
                                    <th>خصم التأخير</th>
                                    <th>ساعات اضافية</th>
                                    <th>قيمة الاضافي</th>
                                    <th>الراتب الأساسي</th>
                                    <th>صافي الراتب</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payrollData as $data)
                                <tr>
                                    <td>{{ $data['user']->name }}</td>
                                    <td>{{ $data['present_days'] }}</td>
                                    <td>{{ $data['fridays'] }}</td>
                                    <td>{{ $data['present_days'] + $data['fridays'] }}</td>
                                    <td>{{ $data['delay_minutes'] }}</td>
                                    <td>{{ $data['delay_deduction'] }}</td>
                                    <td>{{ $data['overtime_hours'] }}</td>
                                    <td>{{ $data['overtime_pay'] }}</td>
                                    <td>{{ number_format($data['user']->base_salary, 2) }}</td>
                                    <td>{{ $data['total_salary'] }}</td>
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
@endsection

