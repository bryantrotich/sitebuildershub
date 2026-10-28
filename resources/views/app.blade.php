<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="height:100%;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Favicon: the company icon from System settings, or the default icon -->
        @php($favicon = data_get($page, 'props.company.icon') ?? asset('favicon.ico'))
        <link rel="icon" href="{{ $favicon }}" id="favicon">
        <link rel="apple-touch-icon" href="{{ $favicon }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @if(str_contains($page['component'],'Landing'))
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/theme.css') }}">
        <!-- <link rel="stylesheet" href="{{ asset('/assets/plugins/aos/aos.min.css') }}"> -->
        <link rel="stylesheet" href="{{ asset('/assets/plugins/fancybox/jquery.fancybox.min.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/plugins/nice-select/nice-select.min.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/plugins/slick/slick.min.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/plugins/ui-range-slider/jquery-ui.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/main.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/brand.css') }}">
        @endif
        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased" style="height:100%;">
        @inertia
        @if(str_contains($page['component'],'Landing'))
        <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/fancybox/jquery.fancybox.min.js') }}" ></script>
        <script src="{{ asset('assets/plugins/nice-select/jquery.nice-select.min.js') }}" ></script>
        <!-- <script src="{{ asset('assets/plugins/aos/aos.min.js') }}"></script> -->
        <script src="{{ asset('assets/plugins/slick/slick.min.js') }}" ></script>
        <script src="{{ asset('assets/plugins/counter-up/jquery.counterup.min.js') }}" ></script>
        <script src="{{ asset('assets/plugins/counter-up/jquery.waypoints.min.js') }}" ></script>
        <script src="{{ asset('assets/plugins/ui-range-slider/jquery-ui.js') }}"></script>
        <script src="{{ asset('assets/js/custom.js') }}"></script>
        @endif
    </body>
</html>
