    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto"><a class="navbar-brand" href="{{route('dashboard')}}">
                        <h2 class="brand-text mb-0">Papion System</h2>
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                
                <li class=" nav-item"><a href="{{route('dashboard')}}"><i class="feather icon-home"></i><span class="menu-title" data-i18n="Dashboard">Papion</span><span class="badge badge badge-warning badge-pill float-right mr-2"></span></a>
                    <ul class="menu-content">

                        <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><a href="{{route('dashboard')}}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="eCommerce">الرئيسية</span></a>
                        </li>
                    </ul>
                </li>

                @if(auth()->user()->can(['الطباعه']))
                <li class=" navigation-header"><span>الطباعة</span>
                </li>
                <li class=" nav-item"><a href="#"><i class="feather icon-printer"></i><span class="menu-title" data-i18n="Ecommerce">الطباعة</span></a>
                    <ul class="menu-content">
                        <li class="{{ request()->routeIs('AddPrintOrders') ? 'active' : '' }}"><a href="{{route('AddPrintOrders')}}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">  التشغيل</span></a>
                        </li>

                        <li class="{{ request()->routeIs('print_log') ? 'active' : '' }}"><a href="{{ route('print_log') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">سجل الطباعة </span></a>
                        </li>
                        <li class="{{ request()->routeIs('printers.trash') ? 'active' : '' }}"><a href="{{ route('printers.trash') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">سلة المحذوفات</span></a>
                        </li>
                        <!-- <li class="{{ request()->routeIs('accounts.index') ? 'active' : '' }}"><a href="{{ route('accounts.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Accounts">الحسابات</span></a>
                        </li> -->
                        @if(auth()->user()->can('الفواتير'))
                        <li class="{{ request()->routeIs('machines.pricing') ? 'active' : '' }}"><a href="{{ route('machines.pricing') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Accounts">اسعار الطباعه</span></a>
                        </li>
                        @endif
                        <!-- <li><a href="app-ecommerce-wishlist.html"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Wish List">المكبس</span></a>
                        </li> -->

                    </ul>
                </li>
                @endif

                @if(auth()->user()->can('المكبس'))
                <li class=" nav-item"><a href="#"><i class="feather icon-sunset"></i><span class="menu-title" data-i18n="Ecommerce">المكبس</span></a>
                    <ul class="menu-content">
               
                        <li class="{{ request()->routeIs('presslist') ? 'active' : '' }}"><a href="{{route('presslist')}}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">اوردات المكبس </span></a>
                        </li>

                        <li class="{{ request()->routeIs('rollpress.archive') ? 'active' : '' }}"><a href="{{route('rollpress.archive')}}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">ارشيف المكبس</span></a>
                        </li>
                        <li class="{{ request()->routeIs('rollpress.trash') ? 'active' : '' }}"><a href="{{route('rollpress.trash')}}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">سلة المحذوفات</span></a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->can(['الاستراس']))
                {{-- Stras Menu Item --}}
                <li class=" nav-item"><a href="#"><i class="feather icon-star"></i><span class="menu-title" data-i18n="Ecommerce">الاستراس</span></a>
                    <ul class="menu-content">
                        <li class="{{ request()->routeIs('stras.index') ? 'active' : '' }}"><a href="{{route('stras.index')}}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">اوردات الاستراس</span></a>
                        </li>
                        @if(auth()->user()->can('الفواتير'))
                        <li class="{{ request()->routeIs('stras.pricing') ? 'active' : '' }}"><a href="{{ route('stras.pricing') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">اسعار الخامات</span></a>
                        </li>
                        @endif
                        <li class="{{ request()->routeIs('stras.trash') ? 'active' : '' }}"><a href="{{ route('stras.trash') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">سلة المحذوفات</span></a>
                        </li>
                    </ul>
                </li>
    
@endif
                @if(auth()->user()->can(['الترتر']))
                {{-- Tarter Menu Item --}}
                <li class=" nav-item"><a href="#"><i class="feather icon-disc"></i><span class="menu-title" data-i18n="Ecommerce">الترتر</span></a>
                    <ul class="menu-content">
                        <li class="{{ request()->routeIs('tarter.index') ? 'active' : '' }}"><a href="{{route('tarter.index')}}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">اوردات الترتر</span></a>
                        </li>
                        @if(auth()->user()->can('الفواتير'))
                        <li class="{{ request()->routeIs('tarter.pricing') ? 'active' : '' }}"><a href="{{ route('tarter.pricing') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">اسعار الخامات</span></a>
                        </li>
                        @endif
                        <li class="{{ request()->routeIs('tarter.trash') ? 'active' : '' }}"><a href="{{ route('tarter.trash') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">سلة المحذوفات</span></a>
                        </li>
                    </ul>
                </li>
                @endif
                @if(auth()->user()->can(['الليزر']))
                {{-- Laser Menu Item --}}
                <li class=" nav-item"><a href="#"><i class="feather icon-loader"></i><span class="menu-title" data-i18n="Ecommerce">الليزر</span></a>
                    <ul class="menu-content">
                        <li class="{{ request()->routeIs('laser.index') ? 'active' : '' }}"><a href="{{route('laser.index')}}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">اوردات الليزر</span></a>
                        </li>
                        @if(auth()->user()->can(['الفواتير']))
                        <li class="{{ request()->routeIs('laser.pricing') ? 'active' : '' }}"><a href="{{ route('laser.pricing') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">اسعار الخامات</span></a>
                        </li>
                        @endif
                        <li class="{{ request()->routeIs('laser.trash') ? 'active' : '' }}"><a href="{{ route('laser.trash') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Shop">سلة المحذوفات</span></a>
                        </li>
                    </ul>
                </li>
                @endif
                @if(auth()->user()->can(['المخزن']))
                <li class=" nav-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                    <a href="{{ route('inventory.index') }}">
                        <i class="feather icon-box"></i>
                        <span class="menu-title" data-i18n="Inventory">المخازن</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->can(['الفواتير']))
                <li class=" nav-item"><a href="#"><i class="feather icon-file-text"></i><span class="menu-title" data-i18n="Ecommerce">الفواتير</span></a>
                    <ul class="menu-content">

                        <li class="{{ request()->routeIs('invoice.create') ? 'active' : '' }} nav-item">
                            <a href="{{ route('invoice.create') }}">
                                <i class="feather icon-file-text"></i>
                                <span class="menu-title" data-i18n="Invoice">الفاتورة المجمعة</span>
                            </a>
                        </li>

                        <li class="{{ request()->routeIs('invoice.history') ? 'active' : '' }} nav-item">
                            <a href="{{ route('invoice.history') }}">
                                <i class="feather icon-archive"></i>
                                <span class="menu-title" data-i18n="Invoice History">سجل الفواتير</span>
                            </a>
                        </li>

                    </ul>
                </li>
                @endif
                @if(auth()->user()->hasRole('super-admin'))
                <li class=" nav-item"><a href="#"><i class="feather icon-user"></i><span class="menu-title" data-i18n="Ecommerce">المستخدمين والصلاحيات</span></a>
                    <ul class="menu-content">
                        <li class="{{ request()->routeIs('users.index') ? 'active' : '' }} nav-item"><a href="{{ route('users.index') }}"><i class="feather icon-users"></i><span class="menu-title" data-i18n="Users">المستخدمين</span></a>
                        </li>

                <li class="{{ request()->routeIs('roles.index') ? 'active' : '' }} nav-item"><a href="{{ route('roles.index') }}"><i class="feather icon-lock"></i><span class="menu-title" data-i18n="Roles">الصلاحيات & الأدوار</span></a>
                </li>

                    </ul>
                </li>
                @endif
                @if(auth()->user()->can(['الرواتب']))
                <li class="ac nav-item"><a href="#"><i class="feather icon-dollar-sign"></i><span class="menu-title" data-i18n="Ecommerce">الرواتب</span></a>
                    <ul class="menu-content">
                        <li class=" nav-item {{ request()->routeIs('payroll.*') ? 'active' : '' }}">
                            <a href="{{ route('payroll.index') }}">
                                <i class="feather icon-dollar-sign"></i>
                                <span class="menu-title" data-i18n="Payroll">الرواتب</span>
                            </a>
                        </li>

                        <li class=" nav-item {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                            <a href="{{ route('attendance.index') }}">
                                <i class="feather icon-clock"></i>
                                <span class="menu-title" data-i18n="Attendance">الحضور والانصراف</span>
                            </a>
                        </li>
                        <li class=" nav-item {{ request()->routeIs('biometric.*') ? 'active' : '' }}">
                            <a href="{{ route('biometric.index') }}">
                                <i class="feather icon-fingerprint"></i>
                                <span class="menu-title" data-i18n="Biometric">نظام البصمة</span>
                            </a>
                        </li>
       
                    </ul>
                </li>
                @endif


     





 
            @if( auth()->user()->hasRole('super-admin'))

                <li class=" nav-item {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                    <a href="{{ route('settings.index') }}">
                        <i class="feather icon-settings"></i>
                        <span class="menu-title" data-i18n="Settings">اعدادات الموقع</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can(['تقارير الاخطاء']))
                <li class=" nav-item {{ request()->routeIs('admin.error_logs.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.error_logs.index') }}">
                        <i class="feather icon-alert-triangle"></i>
                        <span class="menu-title" data-i18n="Error Logs">تقارير الأخطاء</span>
                    </a>
                </li>
                @endif
                            


    
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->