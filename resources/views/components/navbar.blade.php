    <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-light navbar-shadow">
        <div class="navbar-wrapper">
            <div class="navbar-container content">
                <div class="navbar-collapse" id="navbar-mobile">
                    <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                        <ul class="nav navbar-nav">
                            <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ficon feather icon-menu"></i></a></li>
                        </ul>
                        <ul class="nav navbar-nav bookmark-icons">
                            <!-- li.nav-item.mobile-menu.d-xl-none.mr-auto-->
                            <!--   a.nav-link.nav-menu-main.menu-toggle.hidden-xs(href='#')-->
                            <!--     i.ficon.feather.icon-menu-->
                            <!-- <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-todo.html" data-toggle="tooltip" data-placement="top" title="Todo"><i class="ficon feather icon-check-square"></i></a></li>
                            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-chat.html" data-toggle="tooltip" data-placement="top" title="Chat"><i class="ficon feather icon-message-square"></i></a></li>
                            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-email.html" data-toggle="tooltip" data-placement="top" title="Email"><i class="ficon feather icon-mail"></i></a></li>
                            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-calender.html" data-toggle="tooltip" data-placement="top" title="Calendar"><i class="ficon feather icon-calendar"></i></a></li> -->
                        </ul>
                        <ul class="nav navbar-nav">
                            <li class="nav-item d-none d-lg-block"><a class="nav-link bookmark-star"><i class="ficon feather icon-star warning"></i></a>
                                <div class="bookmark-input search-input">
                                    <div class="bookmark-input-icon"><i class="feather icon-search primary"></i></div>
                                    <input class="form-control input" type="text" placeholder="البحث" tabindex="0" data-search="template-list">
                                    <ul class="search-list search-list-bookmark"></ul>
                                </div>
                                <!-- select.bookmark-select-->
                                <!--   option Chat-->
                                <!--   option email-->
                                <!--   option todo-->
                                <!--   option Calendar-->
                            </li>
                        </ul>
                    </div>
                    <ul class="nav navbar-nav float-right">
               
                        <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i class="ficon feather icon-maximize"></i></a></li>
                        <li class="nav-item nav-search"><a class="nav-link nav-link-search"><i class="ficon feather icon-search"></i></a>
                            <div class="search-input">
                                <div class="search-input-icon"><i class="feather icon-search primary"></i></div>
                                <input class="input" type="text" placeholder="البحث" tabindex="-1">
                                <div class="search-input-close"><i class="feather icon-x"></i></div>
                                <ul class="search-list search-list-main"></ul>
                            </div>
                        </li>

                        @can('الفواتير')
                        <li class="dropdown dropdown-notification nav-item"><a class="nav-link nav-link-label" href="#" data-toggle="dropdown"><i class="ficon feather icon-file-text"></i><span class="badge badge-pill badge-primary badge-up cart-item-count">{{ $cartCount ?? 0 }}</span></a>
                            <ul class="dropdown-menu dropdown-menu-media dropdown-cart dropdown-menu-right">
                                <li class="dropdown-menu-header">
                                    <div class="dropdown-header m-0 p-2">
                                        <h3 class="white"><span class="cart-item-count">{{ $cartCount ?? 0 }}</span><span class="mx-50">فاتوره</span></h3><span class="notification-title">فواتير مجمعه</span>
                                    </div>
                                </li>
                                <li class="scrollable-container media-list" id="cart-dropdown-items">
                                    @include('components.cart_dropdown')
                                </li>
                                <li class="dropdown-menu-footer">
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <a class="dropdown-item p-1 text-center text-primary w-50" href="{{ route('invoice.create') }}"><i class="feather icon-shopping-cart align-middle"></i><span class="align-middle text-bold-600">الذهاب الي الفواتير</span></a>
                                        <a class="dropdown-item p-1 text-center text-danger w-50 clear-cart-btn" href="#"><i class="feather icon-trash align-middle"></i><span class="align-middle text-bold-600">مسح الفواتير</span></a>
                                    </div>
                                </li>  
                            </ul>
                        </li>
                        <li class="dropdown dropdown-notification nav-item"><a class="nav-link nav-link-label" href="#" data-toggle="dropdown"><i class="ficon feather icon-bell"></i><span class="badge badge-pill badge-primary badge-up" id="notification-badge">{{ $unreadNotificationCount ?? 0 }}</span></a>
                            <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                <li class="dropdown-menu-header">
                                    <div class="dropdown-header m-0 p-2">
                                        <h3 class="white" id="notification-header-count">{{ $unreadNotificationCount ?? 0 }} New</h3><span class="notification-title">App Notifications</span>
                                    </div>
                                </li>
                                <li class="scrollable-container media-list" id="notification-list" data-url="{{ route('notifications.latest') }}">
                                    @foreach ($notifications as $notification)
                                        <a class="d-flex justify-content-between" href="{{ $notification->link ?? '#' }}">
                                            <div class="media d-flex align-items-start">
                                                <div class="media-left"><img src="{{ $notification->img_path ? asset('storage/' . $notification->img_path) : asset('assets/images/logo/papion.png') }}" alt="" class="rounded-circle" width="35" height="35"></div>
                                                <div class="media-body">
                                                    <h6 class="primary media-heading">{{ $notification->title  ?? '-'}}</h6>
                                                    <small class="notification-text"> {{ $notification->body  ?? '-'}}</small>
                                                </div>
                                            <small>
                                                <time class="media-meta " datetime="{{ $notification->created_at }}">{{ $notification->created_at->diffForHumans() }}</time>
                                            </small>
                                            </div>
                                    </a>
                                @endforeach
                                </li>
                                <li class="dropdown-menu-footer"><a class="dropdown-item p-1 text-center" href="{{ route('notifications.index') }}">View all notifications</a></li>
                            </ul>
                        </li>

                        @endcan


                        <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
<div class="user-nav d-sm-flex d-none"><span class="user-name text-bold-600">{{ Auth::user()->name ?? '-'}}</span><span class="user-status">{{ Auth::user()->roles->pluck('name')[0] ?? '' }}
</span></div>
                                <span>
                                    <div class="round d-flex justify-content-center align-items-center bg-primary text-white" style="width: 40px; height: 40px; font-weight: bold; border-radius: 50%;">
                                        @php
                                            $nameParts = explode(' ', Auth::user()->name);
                                            $initials = mb_substr($nameParts[0], 0, 1);
                                            if (count($nameParts) > 1) {
                                                $initials .= ' ' . mb_substr($nameParts[1], 0, 1);
                                            }
                                        @endphp
                                        {{ $initials }}
                                    </div>
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <!-- <a class="dropdown-item" href="page-user-profile.html"><i class="feather icon-user"></i> Edit Profile</a><a class="dropdown-item" href="app-email.html"><i class="feather icon-mail"></i> My Inbox</a><a class="dropdown-item" href="app-todo.html"><i class="feather icon-check-square"></i> Task</a><a class="dropdown-item" href="app-chat.html"><i class="feather icon-message-square"></i> Chats</a> -->
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="feather icon-power"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    @vite('resources/js/notifications.js')
    <script>
        // Inline script moved to resources/js/notifications.js

        // Customer Search Logic
        $(document).ready(function() {
            var searchInput = $('.navbar-nav .nav-search .input');
            var searchList = $('.navbar-nav .nav-search .search-list-main');
            var searchContainer = $('.navbar-nav .nav-search .search-input');
            
            // Debounce function
            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            // Input Handler
            searchInput.on('input', debounce(function() {
                var query = $(this).val();
                
                if (query.length < 2) {
                    searchList.html('').hide();
                    searchContainer.removeClass('open');
                    return;
                }

                $.get("{{ route('customers.search') }}", { q: query }, function(data) {
                     var html = '';
                     if (data.length > 0) {
                         data.forEach(function(customer) {
                             var url = "{{ route('customers.show', ':id') }}".replace(':id', customer.id);
                             html += '<li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer" onclick="window.location.href=\'' + url + '\'">' +
                                     '<a class="d-flex align-items-center justify-content-between w-100" href="' + url + '">' +
                                        '<div class="d-flex align-items-center justify-content-start">' +
                                            '<span class="mr-2"><i class="feather icon-user"></i></span>' +
                                            '<span>' + customer.name + '</span>' +
                                        '</div>' +
                                        '<span class="text-muted small">' + (customer.phone || '') + '</span>' +
                                     '</a>' +
                                     '</li>';
                         });
                     } else {
                         html = '<li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer"><a><div class="d-flex align-items-center justify-content-start"><span class="mr-2"><i class="feather icon-alert-circle"></i></span><span>لا توجد نتائج</span></div></a></li>';
                     }
                     
                     searchList.html(html).show();
                     searchContainer.addClass('open');
                });
            }, 300));

            // Close when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-input').length && !$(e.target).closest('.nav-link-search').length) {
                    searchList.hide();
                    searchContainer.removeClass('open');
                }
            });
            
             // Close on Escape
             $(document).on('keydown', function(e) {
                if (e.key === "Escape") {
                     searchList.hide();
                     searchContainer.removeClass('open');
                }
            });
        });
    </script>