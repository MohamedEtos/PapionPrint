@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تقارير الأخطاء</h2>
                </div>
            </div>
        </div>
    </div>
       <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">

    <div class="content-body">
        <section id="basic-datatable">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">قائمة الأخطاء</h4>
                            <form action="{{ route('admin.error_logs.destroy_all') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف جميع السجلات؟');">
                                @csrf
                                <button type="submit" class="btn btn-danger">حذف جميع الأخطاء</button>
                            </form>
                        </div>
                        <div class="card-content">
                            <div class="card-body card-dashboard">
                                <div class="table-responsive">
                                    <table class="table zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>المستخدم</th>
                                                <th>IP</th>
                                                <th>الرابط</th>
                                                <th>الرسالة</th>
                                                <th>التاريخ</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($logs as $log)
                                            <tr>
                                                <td>{{ $log->id }}</td>
                                                <td>
                                                    @if($log->user_id)
                                                        <a href="{{ route('users.index') }}?search={{ $log->username }}">{{ $log->username }}</a>
                                                    @else
                                                        {{ $log->username ?? 'Guest' }}
                                                    @endif
                                                </td>
                                                <td>{{ $log->ip_address }}</td>
                                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->request_url }}">{{ $log->request_url }}</td>
                                                <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->message }}">{{ $log->message }}</td>
                                                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.error_logs.show', $log->id) }}" class="btn btn-primary btn-sm">عرض</a>
                                                    <form action="{{ route('admin.error_logs.destroy', $log->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>المستخدم</th>
                                                <th>IP</th>
                                                <th>الرابط</th>
                                                <th>الرسالة</th>
                                                <th>التاريخ</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    {{ $logs->links() }}
                                </div>
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
