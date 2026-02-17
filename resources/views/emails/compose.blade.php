@extends('layouts.app')

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header ">
<div class="content-body">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Compose Email</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('mail.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="to">To:</label>
                                <input type="email" class="form-control" name="to" id="to" required placeholder="recipient@example.com">
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject:</label>
                                <input type="text" class="form-control" name="subject" id="subject" required placeholder="Subject">
                            </div>
                            <div class="form-group">
                                <label for="body">Message:</label>
                                <textarea class="form-control" name="body" id="body" rows="10" required></textarea>
                            </div>
                            <div class="form-group text-right">
                                <a href="{{ route('mail.index') }}" class="btn btn-secondary mr-1">Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="feather icon-send"></i> Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
