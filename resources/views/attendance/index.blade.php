@extends('layouts.app')

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">سجل الحضور والانصراف</h4>
                <div class="heading-elements">
                    @if(!$todayAttendance)
                        <button id="checkInBtn" class="btn btn-success">تسجيل الحضور <i class="feather icon-log-in"></i></button>
                    @elseif(!$todayAttendance->check_out)
                        <button id="checkOutBtn" class="btn btn-danger">تسجيل الانصراف <i class="feather icon-log-out"></i></button>
                    @else
                        <button class="btn btn-secondary" disabled>تم الانتهاء اليوم</button>
                    @endif
                </div>
            </div>
            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="table-responsive">
                        <table class="table table-striped dataex-html5-selectors">
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
                        {{ $attendances->links() }}
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

@section('script')
<script>
    $(document).ready(function() {
        // Check In
        $('#checkInBtn').click(function() {
            $(this).prop('disabled', true);
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
                    $('#checkInBtn').prop('disabled', false);
                }
            });
        });

        // Check Out
        $('#checkOutBtn').click(function() {
            $(this).prop('disabled', true);
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
                    $('#checkOutBtn').prop('disabled', false);
                }
            });
        });
    });
</script>
@endsection
