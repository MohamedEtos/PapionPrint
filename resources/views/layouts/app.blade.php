<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">

    <title>{{ isset($site_settings) ? $site_settings->site_name : config('app.name', 'Laravel') }}</title>


    <link rel="apple-touch-icon" href=" {{  asset('core/images/ico/apple-icon-120.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ isset($site_settings) && $site_settings->site_logo ? asset($site_settings->site_logo) : asset('core/images/ico/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Alexandria:300,400,500,600" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@100..900&display=swap" rel="stylesheet">


    <!-- Scripts -->
    <script src="{{ asset('core/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('core/vendors/js/extensions/toastr.min.js') }}"></script>




    @vite([  'resources/css/app.css','resources/js/app.js'])



    @yield('css')

    @if(isset($site_settings))
    <style>
        :root {
            --primary-color: {{ $site_settings->primary_color ?? '#7367F0' }};
            --secondary-color: {{ $site_settings->secondary_color ?? '#EA5455' }};
            --success-color: {{ $site_settings->success_color ?? '#28C76F' }};
            --danger-color: {{ $site_settings->danger_color ?? '#EA5455' }};
            --warning-color: {{ $site_settings->warning_color ?? '#FF9F43' }};
            --info-color: {{ $site_settings->info_color ?? '#00CFDD' }};
            --dark-color: {{ $site_settings->dark_color ?? '#1E1E1E' }};
        }
        
        /* Primary */
        .text-primary { color: var(--primary-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .btn-primary { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; }

        /* Secondary */
        .text-secondary { color: var(--secondary-color) !important; }
        .bg-secondary { background-color: var(--secondary-color) !important; }
        .btn-secondary { background-color: var(--secondary-color) !important; border-color: var(--secondary-color) !important; }

        /* Success */
        .text-success { color: var(--success-color) !important; }
        .bg-success { background-color: var(--success-color) !important; }
        .btn-success { background-color: var(--success-color) !important; border-color: var(--success-color) !important; }

        /* Danger */
        .text-danger { color: var(--danger-color) !important; }
        .bg-danger { background-color: var(--danger-color) !important; }
        .btn-danger { background-color: var(--danger-color) !important; border-color: var(--danger-color) !important; }

        /* Warning */
        .text-warning { color: var(--warning-color) !important; }
        .bg-warning { background-color: var(--warning-color) !important; }
        .btn-warning { background-color: var(--warning-color) !important; border-color: var(--warning-color) !important; }

        /* Info */
        .text-info { color: var(--info-color) !important; }
        .bg-info { background-color: var(--info-color) !important; }
        .btn-info { background-color: var(--info-color) !important; border-color: var(--info-color) !important; }

        /* Dark */
        .text-dark { color: var(--dark-color) !important; }
        .bg-dark { background-color: var(--dark-color) !important; }
        .btn-dark { background-color: var(--dark-color) !important; border-color: var(--dark-color) !important; }
        
        /* Add more overrides as necessary for the template to respect these vars */
    </style>
    @endif


</head>
<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

    @include('components.navbar')
    @include('components.aside')
    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

        <main class="">
            @yield('content')
        </main>

    @include('components.footer')

    @yield('js')


</body>
</html>
