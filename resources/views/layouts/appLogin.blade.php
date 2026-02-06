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

    <title>Papion System</title>


    <link rel="apple-touch-icon" href=" {{  asset('core/images/ico/apple-icon-120.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href=" {{  asset('core/images/ico/favicon.ico')}}">
    <link href="https://fonts.googleapis.com/css?family=Alexandria:300,400,500,600" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@100..900&display=swap" rel="stylesheet">

    <!-- Scripts -->
        <script src="{{ asset('core/vendors/js/vendors.min.js') }}"></script>

    <!-- @vite([  'resources/css/app.css','resources/js/app.js']) -->

    @vite([
        'resources/core/vendors/css/vendors-rtl.min.css',
        'resources/core/css-rtl/bootstrap.css',
        'resources/core/css-rtl/bootstrap-extended.css',
        'resources/core/css-rtl/colors.css',
        'resources/core/css-rtl/components.css',
        'resources/core/css-rtl/themes/dark-layout.css',
        'resources/core/css-rtl/themes/semi-dark-layout.css',
        'resources/core/css-rtl/core/menu/menu-types/vertical-menu.css',
        'resources/core/css-rtl/core/colors/palette-gradient.css',
        'resources/core/css-rtl/pages/authentication.css',
        'resources/core/css-rtl/custom-rtl.css',

        'resources/core/js/core/app-menu.js',
        'resources/core/js/core/app.js',
        'resources/core/js/scripts/components.js',
    ])

    @yield('css')

</head>
<body class="">


            @yield('content')


    @yield('js')

</body>
</html>
