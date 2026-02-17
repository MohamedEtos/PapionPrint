@extends('layouts.app')

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
<div class="content-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Email System</h4>
                    <div>
                        <a href="{{ route('mail.create') }}" class="btn btn-primary"><i class="feather icon-plus"></i> Compose</a>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-3">
                            <li class="nav-item">
                                <a class="nav-link {{ !isset($box) || $box != 'sent' ? 'active' : '' }}" href="{{ route('mail.index') }}">
                                    <i class="feather icon-inbox"></i> Inbox
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ isset($box) && $box == 'sent' ? 'active' : '' }}" href="{{ route('mail.sent') }}">
                                    <i class="feather icon-send"></i> Sent
                                </a>
                            </li>
                        </ul>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>{{ isset($box) && $box == 'sent' ? 'To' : 'From' }}</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($messages as $message)
                                        <tr>
                                            <td>
                                                <a href="{{ route('mail.show', $message->getUid()) }}" class="font-weight-bold text-body">
                                                    {{ $message->getSubject() }}
                                                </a>
                                            </td>
                                            <td>
                                                @if(isset($box) && $box == 'sent')
                                                    {{ $message->getTo()[0]->mail ?? 'Unknown' }}
                                                @else
                                                    {{ $message->getFrom()[0]->mail ?? 'Unknown' }}
                                                @endif
                                            </td>
                                            <td>{{ optional($message->getDate()->first())->format('M d, Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('mail.show', $message->getUid()) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="feather icon-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No messages found.</td>
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
</div>
@endsection
