@extends('layouts.app')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <h2 class="content-header-title float-left mb-0">ملف العميل</h2>
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li class="breadcrumb-item active">{{ $customer->name }}</li>
                </ol>
            </div>
        </div>
    </div>
    
    <div class="content-body">
        <!-- Customer Info Card -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">بيانات العميل</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-primary mr-2 p-1">
                                    <div class="avatar-content"><i class="feather icon-user font-large-1"></i></div>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $customer->name }}</h5>
                                    <small>{{ $customer->address ?? 'لا يوجد عنوان' }}</small>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-success mr-2 p-1">
                                    <div class="avatar-content"><i class="feather icon-phone font-large-1"></i></div>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $customer->phone ?? 'لا يوجد هاتف' }}</h5>
                                    <small>رقم الهاتف</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                             <div class="d-flex align-items-center">
                                <div class="avatar bg-warning mr-2 p-1">
                                    <div class="avatar-content"><i class="feather icon-star font-large-1"></i></div>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $customer->created_at->format('Y-m-d') }}</h5>
                                    <small>تاريخ التسجيل</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
             <div class="col-lg-4 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700 mb-0">{{ number_format($stats['total_spent'], 2) }}</h2>
                            <p>إجمالي المبالغ</p>
                        </div>
                        <div class="avatar bg-rgba-primary p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-dollar-sign text-primary font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700 mb-0">{{ $stats['total_orders'] }}</h2>
                            <p>إجمالي الفواتير</p>
                        </div>
                         <div class="avatar bg-rgba-success p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-file-text text-success font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="col-lg-4 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700 mb-0">{{ $stats['stras_orders'] + $stats['tarter_orders'] + $stats['laser_orders'] + $stats['printer_orders'] }}</h2>
                            <p>إجمالي الطلبات</p>
                        </div>
                         <div class="avatar bg-rgba-warning p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-shopping-cart text-warning font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices List -->
         <div class="card">
            <div class="card-header">
                <h4 class="card-title">سجل الفواتير ({{ count($invoices) }})</h4>
            </div>
            <div class="card-content">
                <div class="table-responsive">
                    <table class="table table-hover-animation mb-0">
                        <thead>
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>التاريخ</th>
                                <th>عدد العناصر</th>
                                <th>الاجمالي</th>
                                <th>حالة الارسال</th>
                                <th>الاجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td>#{{ $invoice->id }}</td>
                                    <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $invoice->items->count() }}</td>
                                    <td>{{ number_format($invoice->total_amount, 2) }} ج.م</td>
                                    <td>
                                        @if($invoice->status == 'sent')
                                            <div class="chip chip-success">
                                                <div class="chip-body"><div class="chip-text">تم الارسال</div></div>
                                            </div>
                                        @else
                                             <div class="chip chip-warning">
                                                <div class="chip-body"><div class="chip-text">{{ $invoice->status }}</div></div>
                                            </div>
                                        @endif
                                    </td>
                                     <td>
                                         <button type="button" class="btn btn-sm btn-primary view-invoice-btn" data-url="{{ route('invoice.invoice_details', $invoice->id) }}"><i class="feather icon-eye"></i> عرض</button>
                                     </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"> الطلبات</h4>
            </div>
            <div class="card-content">
                <div class="table-responsive" id="orders-table-container" data-add-route="{{ route('invoice.add') }}">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>النوع</th>
                                <th>التاريخ</th>
                                <th>التفاصيل</th>
                                <th>الاجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td>
                                    @if($order['type'] == 'Stras') <span class="badge badge-info">استراس</span>
                                    @elseif($order['type'] == 'Tarter') <span class="badge badge-primary">ترتر</span>
                                    @elseif($order['type'] == 'Laser') <span class="badge badge-danger">ليزر</span>
                                    @elseif($order['type'] == 'Printer') <span class="badge badge-success">طباعة</span>
                                    @endif
                                </td>
                                <td>{{ $order['date']->format('Y-m-d') }}</td>
                                <td>{{ $order['details'] }}</td>
                                <td>
                                    <a href="{{ $order['link'] }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="feather icon-external-link"></i> فتح</a>
                                    <button class="btn btn-sm btn-outline-success add-to-invoice" data-id="{{ $order['id'] }}" data-type="{{ strtolower($order['type']) }}">
                                        <i class="feather icon-plus-circle"></i> إضافة للفاتورة
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Invoice Details Modal (Reused) -->
<div class="modal fade" id="invoiceDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل الفاتورة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="invoice-details-content">
                <div class="text-center"><i class="feather icon-loader fa-spin fa-2x"></i></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
    @vite('resources/js/customer-profile.js')
@endsection
