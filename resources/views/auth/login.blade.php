@extends('layouts.appLogin')

@section('content')
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow "></div>
        <div class="content-wrapper ">
            <div class="content-header row">
            </div>
            <div class="content-body mt-5 pt-5">
                <section class="row flexbox-container justify-content-center ">
                    <div class="col-xl-8 col-11 d-flex justify-content-center  ">
                        <div class="card bg-authentication rounded-0 mb-0">
                            <div class="row m-0">
                                <div class="col-lg-6 d-lg-block d-none text-center align-self-center px-1 py-0">
                                    <img src="core/images/pages/login.png" alt="branding logo">
                                </div>
                                <div class="col-lg-6 col-12 p-0">
                                    <div class="card rounded-0 mb-0 px-2">
                                        <div class="card-header pb-1">
                                            <div class="card-title">
                                                <h4 class="mb-0">تسجيل الدخول</h4>
                                            </div>
                                        </div>
                                        <p class="px-2">مرحبا بك، يرجى تسجيل الدخول إلى حسابك.</p>
                                        <div class="card-content">
                                            <div class="card-body pt-1">
                                              <form method="POST" action="{{ route('login') }}">
                                                @csrf
                                                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                                                       <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                                                        @error('username')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                        <div class="form-control-position">
                                                            <i class="feather icon-user"></i>
                                                        </div>
                                                        <label for="username">اسم المستخدم</label>
                                                    </fieldset>

                                                    <fieldset class="form-label-group position-relative has-icon-left">
                                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror                                                    
                                                            <div class="form-control-position">
                                                            <i class="feather icon-lock"></i>
                                                        </div>
                                                        <label for="user-password">كلمة المرور</label>
                                                    </fieldset>
                                                    <div class="form-group d-flex justify-content-between align-items-center">
                                                        <div class="text-left">
                                                            <fieldset class="checkbox">
                                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                                    <span class="vs-checkbox">
                                                                        <span class="vs-checkbox--check">
                                                                            <i class="vs-icon feather icon-check"></i>
                                                                        </span>
                                                                    </span>
                                                                    <label class="" for="remember">
                                                                        {{ __('Remember Me') }}
                                                                    </label>                                              
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary float-right btn-inline mb-1">تسجيل الدخول</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>

@endsection

