@extends('layouts.app')

@section('css')
    @vite([
        'resources/core/vendors/css/tables/datatable/datatables.min.css',
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
                            <h2 class="content-header-title float-left mb-0">سجل الحضور والانصراف</h2>
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
                                    <th>التاريخ</th>
                                    <th>الموظف</th>
                                    <th>وقت الحضور</th>
                                    <th>وقت الانصراف</th>
                                    <th>ساعات العمل</th>
                                    <th>ساعات اضافية</th>
                                    <th>تأخير (دقيقة)</th>
                                    <th>الحالة</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->date }}</td>
                                    <td>{{ $attendance->user->name }}</td>
                                    <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '-' }}</td>
                                    <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '-' }}</td>
                                    <td>{{ $attendance->total_hours }}</td>
                                    <td>{{ $attendance->overtime_hours }}</td>
                                    <td>{{ $attendance->delay_minutes }}</td>
                                    <td>
                                        <div class="chip chip-{{ $attendance->status == 'present' ? 'success' : 'danger' }}">
                                            <div class="chip-body">
                                                <div class="chip-text">{{ $attendance->status == 'present' ? 'حاضر' : 'غائب' }}</div>
                                            </div>
                                        </div>
                                        @if($attendance->status_note)
                                            <div class="text-warning small mt-1">
                                                <i class="feather icon-alert-circle"></i> {{ $attendance->status_note }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->ip_address }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
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
            var table = $('.data-thumb-view').DataTable({
                responsive: false,
                dom: '<"top"<"actions action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
                oLanguage: {
                    sLengthMenu: "_MENU_",
                    sSearch: ""
                },
                aLengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
                order: [[0, "desc"]],
                bInfo: false,
                pageLength: 20,
                buttons: [],
                initComplete: function (settings, json) {
                    $(".dt-buttons .btn").removeClass("btn-secondary")
                }
            });

            // Check In
            $('#checkInBtn').click(function() {
                var btn = $(this);
                btn.prop('disabled', true);
                $.ajax({
                    url: "{{ route('attendance.checkIn') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'تم!',
                            text: response.success + ' الساعة: ' + response.time,
                            type: 'success',
                            confirmButtonText: 'حسناً'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire('خطأ!', xhr.responseJSON.error, 'error');
                        btn.prop('disabled', false);
                    }
                });
            });

            // Check Out
            $('#checkOutBtn').click(function() {
                var btn = $(this);
                btn.prop('disabled', true);
                $.ajax({
                    url: "{{ route('attendance.checkOut') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'تم!',
                            text: response.success + ' الساعة: ' + response.time,
                            type: 'success',
                            confirmButtonText: 'حسناً'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire('خطأ!', xhr.responseJSON.error, 'error');
                        btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
