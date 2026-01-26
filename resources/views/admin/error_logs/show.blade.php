@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                     <h2 class="content-header-title float-left mb-0">تفاصيل الخطأ #{{ $log->id }}</h2>
                     <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.error_logs.index') }}">تقارير الأخطاء</a>
                            </li>
                            <li class="breadcrumb-item active">تفاصيل الخطأ
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
         <div class="card">
            <div class="card-header">
                <h4 class="card-title">معلومات الخطأ</h4>
                 <form action="{{ route('admin.error_logs.destroy', $log->id) }}" method="POST" class="float-right" onsubmit="return confirm('هل أنت متأكد؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف السجل</button>
                </form>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>المستخدم</th>
                                    <td>{{ $log->username }} (ID: {{ $log->user_id ?? 'N/A' }})</td>
                                </tr>
                                <tr>
                                    <th>IP Address</th>
                                    <td>{{ $log->ip_address }}</td>
                                </tr>
                                <tr>
                                    <th>User Agent</th>
                                    <td>{{ $log->user_agent }}</td>
                                </tr>
                                <tr>
                                    <th>Method</th>
                                    <td><span class="badge badge-primary">{{ $log->request_method }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                             <table class="table table-bordered">
                                <tr>
                                    <th>التاريخ</th>
                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>الرابط</th>
                                    <td style="word-break: break-all;">{{ $log->request_url }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-2">
                        <h5>الرسالة:</h5>
                        <div class="alert alert-danger mb-2">
                            {{ $log->message }}
                        </div>
                    </div>

                    <div class="mt-2">
                        <h5>تتبع الخطأ (Stack Trace):</h5>
                        <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; max-height: 500px; overflow-y: auto; direction: ltr; text-align: left;">{{ $log->stack_trace }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
