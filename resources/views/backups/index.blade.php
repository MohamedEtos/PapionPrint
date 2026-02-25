@extends('layouts.app')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">إدارة النسخ الاحتياطي</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item active">النسخ الاحتياطي</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <div class="form-group breadcrum-right">
                    <form action="{{ route('backups.create') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary shadow">
                            <i class="feather icon-plus"></i> إنشاء نسخة جديدة
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="content-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <section id="basic-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">قائمة النسخ الاحتياطية</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body card-dashboard">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>اسم الملف</th>
                                                    <th>الحجم</th>
                                                    <th>التاريخ</th>
                                                    <th class="text-center">الإجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($backups as $backup)
                                                    <tr>
                                                        <td>{{ $backup['file_name'] }}</td>
                                                        <td>{{ $backup['file_size'] }}</td>
                                                        <td>{{ $backup['last_modified'] }}</td>
                                                        <td class="text-center">
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('backups.download', $backup['file_name']) }}" class="btn btn-sm btn-info" title="تحميل">
                                                                    <i class="feather icon-download"></i>
                                                                </a>
                                                                
                                                                <form action="{{ route('backups.send_email', $backup['file_name']) }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-success" title="إرسال عبر الإيميل">
                                                                        <i class="feather icon-mail"></i>
                                                                    </button>
                                                                </form>

                                                                <form action="{{ route('backups.destroy', $backup['file_name']) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه النسخة؟')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                                        <i class="feather icon-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">لا توجد نسخ احتياطية حالياً</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
