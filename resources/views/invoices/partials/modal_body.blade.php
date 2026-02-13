<div class="row mb-2">
    <div class="col-12">
        <h5 class="mb-1 text-primary">بيانات الفاتورة المعدلة (واتساب)</h5>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>الكمية</th>
                    <th>سعر الوحدة</th>
                    <th>الإجمالي</th>
                    <th>حالة الإرسال</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $archive->quantity }}</td>
                    <td>{{ $archive->unit_price }} ج.م</td>
                    <td>{{ $archive->total_price }} ج.م</td>
                    <td>
                        @if($archive->sent_status == 'pending') <span class="badge badge-warning">قيد الانتظار</span>
                        @elseif($archive->sent_status == 'sent') <span class="badge badge-info">تم الإرسال</span>
                        @elseif($archive->sent_status == 'delivered') <span class="badge badge-success">تم التسليم</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h5 class="mb-1 text-primary">تفاصيل الطلب الأصلي</h5>
        <div class="table-responsive">
            <table class="table data-thumb-view">
                <thead>
                    <tr>
                        @if($type === 'stras')
                            <th>صورة</th>
                            <th>اسم العميل</th>
                            <th>الطول</th>
                            <th>العرض</th>
                            <th>اجمالي القطع</th>
                            <th>المراحل</th>
                            <th>ملاحظات</th>
                            <th>التاريخ</th>
                        @elseif($type === 'tarter')
                            <th>صورة</th>
                            <th>اسم العميل</th>
                            <th>الطول/العرض</th>
                            <th>اجمالي القطع</th>
                            <th>وقت الماكينة</th>
                            <th>المراحل (الإبر)</th>
                            <th>ملاحظات</th>
                            <th>التاريخ</th>
                        @elseif($type === 'printer')
                            <th>صورة</th>
                            <th>اسم العميل</th>
                            <th>التصميم</th>
                            <th>الخامة</th>
                            <th>المقاسات</th>
                            {{-- Add Printer columns --}}
                        @elseif($type === 'laser')
                            <th>صورة</th>
                            <th>اسم العميل</th>
                            <th>المصدر</th>
                            <th>الطول</th>
                            <th>العرض</th>
                            <th>اجمالي القطع</th>
                            <th>التفاصيل</th>
                            <th>ملاحظات</th>
                            <th>التاريخ</th>
                        @elseif($type === 'composite')
                            <th>الوصف</th>
                            <th>تكلفة الليزر</th>
                            <th>تكلفة الترتر</th>
                            <th>تكلفة الطباعة</th>
                            <th>تكلفة الاستراس</th>
                            <th>تكلفة أخرى</th>
                            <th>الإجمالي</th>
                            <th>التاريخ</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if($type === 'stras')
                        @include('stras.partials.details', ['Record' => $item])
                    @elseif($type === 'tarter')
                        @include('tarter.partials.details', ['Record' => $item])
                    @elseif($type === 'laser')
                        @include('laser.partials.details', ['Record' => $item])
                    @elseif($type === 'composite')
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->laser_cost }}</td>
                            <td>{{ $item->tarter_cost }}</td>
                            <td>{{ $item->print_cost }}</td>
                            <td>{{ $item->stras_cost }}</td>
                            <td>{{ $item->other_cost }}</td>
                            <td>{{ $item->total_price }}</td>
                            <td>{{ $item->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
