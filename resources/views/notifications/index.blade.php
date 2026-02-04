@extends('layouts.app')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Notifications</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="notifications-list">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @forelse ($notifications as $notification)
                                    <li class="list-group-item d-flex justify-content-between align-items-center {{ $notification->status == 'unread' ? 'bg-light' : '' }}">
                                        <div class="media">
                                            <div class="media-left pr-1">
                                                <span class="avatar avatar-md">
                                                    @if($notification->img_path)
                                                        <img src="{{ Str::startsWith($notification->img_path, 'data:') ? $notification->img_path : asset('storage/' . $notification->img_path) }}" alt="avatar" height="40" width="40">
                                                    @else
                                                        <i class="feather icon-bell font-medium-5 primary"></i>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="media-body">
                                                <h6 class="media-heading mb-0 {{ $notification->status == 'unread' ? 'font-weight-bold' : '' }}">{{ $notification->title }}</h6>
                                                <p class="notification-text text-muted mb-0">{{ $notification->body }}</p>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <div class="text-center p-3">
                                        <p class="mb-0">No notifications found.</p>
                                    </div>
                                @endforelse
                            </ul>
                            
                            <div class="d-flex justify-content-center mt-2">
                                {{ $notifications->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
