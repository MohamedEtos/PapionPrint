@extends('layouts.app')

@section('content')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header ">
<div class="content-body">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">{{ $message->getSubject() }}</h4>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"><i class="feather icon-arrow-left"></i> Back</a>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="email-meta mb-2">
                            <p><strong>From:</strong> {{ $message->getFrom()[0]->mail ?? 'Unknown' }}</p>
                            <p><strong>To:</strong> {{ $message->getTo()[0]->mail ?? 'Unknown' }}</p>
                            <p><strong>Date:</strong> {{ optional($message->getDate()->first())->format('M d, Y H:i') }}</p>
                        </div>
                        <hr>
                        <div class="email-body mt-3">
                            @if($message->hasHTMLBody())
                                {!! $message->getHTMLBody() !!}
                            @else
                                <pre>{{ $message->getTextBody() }}</pre>
                            @endif
                        </div>
                        
                        <div class="mt-4">
                             {{-- Attachments could go here --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
