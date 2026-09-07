@extends('layouts.app')

@section('content')
@php
    use App\Http\Controllers\ActivityLogController;
@endphp

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-body">
            
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6 col-sm-12">
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="avatar bg-light-primary p-50 m-0 mb-1">
                                    <div class="avatar-content">
                                        <i class="feather icon-activity font-medium-5 text-primary"></i>
                                    </div>
                                </div>
                                <h2 class="text-bold-700">{{ number_format($stats['total'] ?? 0) }}</h2>
                                <p class="mb-0 text-muted">إجمالي سجلات الأنشطة</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-sm-12">
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="avatar bg-light-success p-50 m-0 mb-1">
                                    <div class="avatar-content">
                                        <i class="feather icon-plus-circle font-medium-5 text-success"></i>
                                    </div>
                                </div>
                                <h2 class="text-bold-700 text-success">{{ number_format($stats['created'] ?? 0) }}</h2>
                                <p class="mb-0 text-muted">عمليات الإضافة</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-sm-12">
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="avatar bg-light-warning p-50 m-0 mb-1">
                                    <div class="avatar-content">
                                        <i class="feather icon-edit-2 font-medium-5 text-warning"></i>
                                    </div>
                                </div>
                                <h2 class="text-bold-700 text-warning">{{ number_format($stats['updated'] ?? 0) }}</h2>
                                <p class="mb-0 text-muted">عمليات التعديل</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-sm-12">
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="avatar bg-light-danger p-50 m-0 mb-1">
                                    <div class="avatar-content">
                                        <i class="feather icon-trash-2 font-medium-5 text-danger"></i>
                                    </div>
                                </div>
                                <h2 class="text-bold-700 text-danger">{{ number_format($stats['deleted'] ?? 0) }}</h2>
                                <p class="mb-0 text-muted">عمليات الحذف</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Filter & Logs Table Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="feather icon-list mr-50 text-primary"></i> سجل أنشطة وتغييرات النظام
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        
                        <!-- Search & Filter Form -->
                        <form method="GET" action="{{ route('activity-logs.index') }}" class="mb-2">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <div class="form-group mb-md-0">
                                        <label for="user_id" class="font-weight-bold">المستخدم (المُنفذ)</label>
                                        <select name="user_id" id="user_id" class="form-control">
                                            <option value="">كل المستخدمين</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group mb-md-0">
                                        <label for="event" class="font-weight-bold">نوع الحدث / العملية</label>
                                        <select name="event" id="event" class="form-control">
                                            <option value="">جميع العمليات</option>
                                            <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>🟢 إضافة (Create)</option>
                                            <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>🟠 تعديل (Update)</option>
                                            <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>🔴 حذف (Delete)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group mb-md-0">
                                        <label for="subject_type" class="font-weight-bold">العنصر / القسم</label>
                                        <select name="subject_type" id="subject_type" class="form-control">
                                            <option value="">كل العناصر والأقسام</option>
                                            @foreach($subjectTypes as $st)
                                                <option value="{{ $st['class'] }}" {{ request('subject_type') == $st['class'] ? 'selected' : '' }}>
                                                    {{ $st['label'] }} ({{ class_basename($st['class']) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group mb-md-0">
                                        <label for="date" class="font-weight-bold">التاريخ</label>
                                        <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                                    </div>
                                </div>

                                <div class="col-md-1 d-flex">
                                    <button type="submit" class="btn btn-primary mr-1" title="بحث">
                                        <i class="feather icon-search"></i>
                                    </button>
                                    <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary" title="إعادة تعيين">
                                        <i class="feather icon-refresh-cw"></i>
                                    </a>
                                </div>
                            </div>
                        </form>

                        <hr class="my-2">

                        <!-- Activities Table -->
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 170px;">التاريخ والوقت</th>
                                        <th>المستخدم المُنفذ</th>
                                        <th style="width: 130px;">نوع الحدث</th>
                                        <th>العنصر / مكان العملية</th>
                                        <th class="text-center" style="width: 220px;">التحكم والتصفح</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activities as $activity)
                                        @php
                                            $eventInfo = ActivityLogController::getEventInfo($activity);
                                            $subjectInfo = ActivityLogController::getSubjectDetails($activity);
                                            $itemNav = ActivityLogController::getItemNavigation($activity);
                                            $changes = ActivityLogController::formatChanges($activity);
                                        @endphp
                                        <tr>
                                            <!-- Date & Time -->
                                            <td>
                                                <div class="font-weight-bold text-dark">
                                                    {{ $activity->created_at->format('Y-m-d') }}
                                                </div>
                                                <small class="text-muted d-block" style="direction: ltr; text-align: right;">
                                                    <i class="feather icon-clock mr-25"></i> {{ $activity->created_at->format('h:i:s A') }}
                                                </small>
                                                <small class="text-primary font-small-2">
                                                    {{ $activity->created_at->diffForHumans() }}
                                                </small>
                                            </td>

                                            <!-- Causer User -->
                                            <td>
                                                @if($activity->causer)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar bg-light-primary mr-50 p-25">
                                                            <span class="avatar-content">
                                                                <i class="feather icon-user"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <span class="font-weight-bold text-dark d-block">{{ $activity->causer->name }}</span>
                                                            <small class="text-muted">{{ $activity->causer->email ?? '' }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="badge badge-light-secondary">
                                                        <i class="feather icon-cpu mr-25"></i> النظام (System)
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Event Badge -->
                                            <td>
                                                <span class="badge badge-pill {{ $eventInfo['badge_light'] }} font-medium-1 px-1 py-50">
                                                    <i class="{{ $eventInfo['icon'] }} mr-25"></i>
                                                    {{ $eventInfo['label'] }}
                                                </span>
                                                @if($activity->description && !in_array($activity->description, ['created', 'updated', 'deleted']))
                                                    <small class="d-block text-muted mt-25">{{ $activity->description }}</small>
                                                @endif
                                            </td>

                                            <!-- Subject Item & Location -->
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center mb-25">
                                                        <span class="badge badge-light-primary mr-50 font-weight-bold">
                                                            {{ $subjectInfo['model_name'] }}
                                                        </span>
                                                        <span class="font-weight-bold text-dark">
                                                            {{ $subjectInfo['identifier'] }}
                                                        </span>
                                                        @if($subjectInfo['is_deleted'])
                                                            <span class="badge badge-light-danger font-small-1 ml-50">محذوف من النظام</span>
                                                        @elseif($subjectInfo['id'])
                                                            <small class="text-muted ml-50">(ID: #{{ $subjectInfo['id'] }})</small>
                                                        @endif
                                                    </div>
                                                    
                                                    <!-- Location Path in System -->
                                                    <small class="text-primary font-weight-bold">
                                                        <i class="feather icon-map-pin mr-25"></i> {{ $itemNav['location'] }}
                                                    </small>
                                                </div>
                                            </td>

                                            <!-- View Details Modal Button & Open Order Link -->
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="modal" data-target="#activityModal{{ $activity->id }}" title="عرض التعديلات والتفاصيل">
                                                        <i class="feather icon-eye mr-25"></i> التفاصيل
                                                    </button>

                                                    @if($itemNav['url'])
                                                        <a href="{{ $itemNav['url'] }}" target="_blank" class="btn btn-sm btn-primary waves-effect waves-light" title="فتح الاوردر كامل أو صفحة العملية">
                                                            <i class="feather icon-external-link mr-25"></i> فتح العملية
                                                        </a>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-secondary" disabled title="الصفحة غير متاحة">
                                                            <i class="feather icon-slash mr-25"></i> غير متوفر
                                                        </button>
                                                    @endif
                                                </div>

                                                <!-- Activity Details Modal -->
                                                <div class="modal fade text-left" id="activityModal{{ $activity->id }}" tabindex="-1" role="dialog" aria-labelledby="modalTitle{{ $activity->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-light">
                                                                <h5 class="modal-title font-weight-bold" id="modalTitle{{ $activity->id }}">
                                                                    <i class="{{ $eventInfo['icon'] }} mr-50" style="color: {{ $eventInfo['color'] }};"></i>
                                                                    تفاصيل {{ $eventInfo['label'] }} - {{ $subjectInfo['model_name'] }}
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body p-2">
                                                                
                                                                <!-- Location & Direct Link Banner -->
                                                                <div class="card bg-light-primary border-primary mb-2">
                                                                    <div class="card-body p-1 d-flex flex-wrap justify-content-between align-items-center">
                                                                        <div class="text-right">
                                                                            <small class="text-primary font-weight-bold d-block mb-25">📍 مكان العملية والاوردر في النظام:</small>
                                                                            <span class="font-weight-bold text-dark font-medium-1">
                                                                                {{ $itemNav['location'] }}
                                                                            </span>
                                                                        </div>
                                                                        @if($itemNav['url'])
                                                                            <a href="{{ $itemNav['url'] }}" target="_blank" class="btn btn-primary btn-sm waves-effect waves-light mt-sm-0 mt-1">
                                                                                <i class="feather icon-external-link mr-25"></i> فتح الاوردر كامل في صفحة جديدة
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <!-- Event Metadata Banner -->
                                                                <div class="card bg-light-secondary border mb-2">
                                                                    <div class="card-body p-1">
                                                                        <div class="row text-right">
                                                                            <div class="col-md-4">
                                                                                <small class="text-muted d-block">نوع العملية:</small>
                                                                                <span class="badge badge-pill {{ $eventInfo['badge'] }}">
                                                                                    <i class="{{ $eventInfo['icon'] }} mr-25"></i> {{ $eventInfo['label'] }}
                                                                                </span>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <small class="text-muted d-block">المُنفذ:</small>
                                                                                <strong class="text-dark">{{ $activity->causer ? $activity->causer->name : 'النظام (System)' }}</strong>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <small class="text-muted d-block">تاريخ التغيير:</small>
                                                                                <strong class="text-dark">{{ $activity->created_at->format('Y-m-d h:i:s A') }}</strong>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Formatted Changes / Diff Table -->
                                                                @if(count($changes['rows']) > 0)
                                                                    <h6 class="font-weight-bold mb-1">
                                                                        <i class="feather icon-sliders text-primary mr-50"></i>
                                                                        @if($changes['type'] == 'update')
                                                                            بيانات التغييرات والتعديلات:
                                                                        @elseif($changes['type'] == 'create')
                                                                            البيانات التي تم إضافتها:
                                                                        @elseif($changes['type'] == 'delete')
                                                                            البيانات المحذوفة:
                                                                        @else
                                                                            تفاصيل الحقول:
                                                                        @endif
                                                                    </h6>

                                                                    <div class="table-responsive border rounded mb-2">
                                                                        <table class="table table-bordered table-striped mb-0">
                                                                            <thead class="thead-light">
                                                                                <tr>
                                                                                    <th>اسم الحقل</th>
                                                                                    @if($changes['type'] == 'update')
                                                                                        <th class="bg-light-danger text-danger">القيمة القديمة (قبل التعديل)</th>
                                                                                        <th class="bg-light-success text-success">القيمة الجديدة (بعد التعديل)</th>
                                                                                    @elseif($changes['type'] == 'create')
                                                                                        <th class="bg-light-success text-success">القيمة المضافة</th>
                                                                                    @elseif($changes['type'] == 'delete')
                                                                                        <th class="bg-light-danger text-danger">القيمة المحذوفة</th>
                                                                                    @else
                                                                                        <th>القيمة القديمة</th>
                                                                                        <th>القيمة الجديدة</th>
                                                                                    @endif
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($changes['rows'] as $row)
                                                                                    <tr>
                                                                                        <td class="font-weight-bold">
                                                                                            {{ $row['field'] }}
                                                                                            <small class="text-muted d-block font-weight-normal">({{ $row['raw_key'] }})</small>
                                                                                        </td>
                                                                                        
                                                                                        @if($changes['type'] == 'update')
                                                                                            <td class="table-danger text-danger">
                                                                                                <del>{{ $row['old'] }}</del>
                                                                                            </td>
                                                                                            <td class="table-success text-success font-weight-bold">
                                                                                                {{ $row['new'] }}
                                                                                            </td>
                                                                                        @elseif($changes['type'] == 'create')
                                                                                            <td class="table-success text-success font-weight-bold">
                                                                                                {{ $row['new'] }}
                                                                                            </td>
                                                                                        @elseif($changes['type'] == 'delete')
                                                                                            <td class="table-danger text-danger">
                                                                                                {{ $row['old'] }}
                                                                                            </td>
                                                                                        @else
                                                                                            <td>{{ $row['old'] }}</td>
                                                                                            <td>{{ $row['new'] }}</td>
                                                                                        @endif
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                @else
                                                                    <div class="alert alert-info py-1">
                                                                        <i class="feather icon-info mr-50"></i> لا توجد تفاصيل حقول مسجلة لهذا الحدث.
                                                                    </div>
                                                                @endif

                                                                <!-- Collapsible Raw JSON Data -->
                                                                <div class="mt-2 text-right">
                                                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-toggle="collapse" data-target="#jsonCollapse{{ $activity->id }}" aria-expanded="false">
                                                                        <i class="feather icon-code mr-25"></i> عرض بيانات JSON الخام (للمطورين)
                                                                    </button>
                                                                    <div class="collapse mt-1" id="jsonCollapse{{ $activity->id }}">
                                                                        <pre class="bg-dark text-white p-1 rounded" style="max-height: 250px; overflow-y: auto; text-align: left; direction: ltr;"><code>{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                @if($itemNav['url'])
                                                                    <a href="{{ $itemNav['url'] }}" target="_blank" class="btn btn-primary">
                                                                        <i class="feather icon-external-link mr-25"></i> فتح صفحة العملية كاملة
                                                                    </a>
                                                                @endif
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-3 text-muted">
                                                <i class="feather icon-inbox font-large-2 d-block mb-1"></i>
                                                لا توجد سجلات أنشطة مطابقة لشروط البحث
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-2">
                            {{ $activities->links() }}
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
