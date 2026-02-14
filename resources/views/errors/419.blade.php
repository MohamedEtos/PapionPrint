@extends('layouts.appLogin')
@section('content')
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow "></div>
        <div class="content-wrapper ">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- error 404 -->
                <section class="row flexbox-container justify-content-center mt-5">
                    <div class="col-xl-7 col-md-8 col-12 d-flex justify-content-center">
                        <div class="card auth-card bg-transparent shadow-none rounded-0 mb-0 w-100">
                            <div class="card-content">
                                <div class="card-body text-center">
                                    <img src="{{ asset('core/images/pages/404.png') }}" class="img-fluid align-self-center" alt="branding logo">
                                    <h1 class="font-large-2 my-1">419 -   انتهت الجلسة</h1>
                                    <p class="p-2">
                                        عذرا، انتهت الجلسة
                                    </p>
                                    <a class="btn btn-primary btn-lg mt-2" href="
                                        @if (Auth::user()->can('الاحصائيات'))
                                            {{ route('dashboard') }}
                                        @elseif (Auth::user()->can('مصمم'))
                                            {{ route('AddPrintOrders') }}
                                        @elseif (Auth::user()->can('المكبس'))
                                            {{ route('AddRollpress') }}
                                        @elseif (Auth::user()->can('الطباعه'))
                                            {{ route('AddPrintOrders') }}
                                        @elseif (Auth::user()->can('الفواتير'))
                                            {{ route('invoice.history') }}
                                        @elseif (Auth::user()->can('الليزر'))
                                            {{ route('laser.index') }}
                                        @elseif (Auth::user()->can('الاستراس'))
                                            {{ route('stras.index') }}
                                        @elseif (Auth::user()->can('الترتر'))
                                            {{ route('tarter.index') }}
                                        @else
                                            {{ route('home') }}
                                        @endif
                                    
                                    ">تسجيل الدخول</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- error 404 end -->

            </div>
        </div>

@endsection

