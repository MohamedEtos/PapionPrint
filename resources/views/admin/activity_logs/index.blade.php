@extends('layouts.app')

@section('content')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">سجل الأنشطة</h4>
        </div>
        <div class="card-content">
            <div class="card-body">
                <form method="GET" action="{{ route('activity-logs.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="user_id">المستخدم</label>
                                <select name="user_id" id="user_id" class="form-control">
                                    <option value="">الكل</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="subject_type">نوع العنصر</label>
                                <input type="text" name="subject_type" id="subject_type" class="form-control" value="{{ request('subject_type') }}" placeholder="App\Models\...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date">التاريخ</label>
                                <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" style="padding-top: 25px;">
                                <button type="submit" class="btn btn-primary">بحث</button>
                                <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الوقت</th>
                                <th>المستخدم</th>
                                <th>الحدث</th>
                                <th>العنصر</th>
                                <th>التفاصيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr>
                                    <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $activity->causer ? $activity->causer->name : 'System' }}</td>
                                    <td>{{ $activity->description }}</td>
                                    <td>{{ $activity->subject_type }} (ID: {{ $activity->subject_id }})</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#activityModal{{ $activity->id }}">
                                            عرض
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="activityModal{{ $activity->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">تفاصيل النشاط</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6>البيانات القديمة</h6>
                                                                <pre>{{ json_encode($activity->properties['old'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6>البيانات الجديدة</h6>
                                                                <pre>{{ json_encode($activity->properties['attributes'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $activities->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
