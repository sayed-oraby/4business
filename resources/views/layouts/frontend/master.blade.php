<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ is_rtl() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta-description', setting_localized('app_description', __('frontend.meta_description')))">

    <title>@yield('title') - {{ setting_localized('app_name', config('app.name', 'دليل العقار')) }}</title>

    <!-- Favicon -->
    @php($favicon = setting_media_url($appSettings['favicon'] ?? null, asset('frontend/img/favicon.png')))
    <link rel="icon" type="image/png" href="{{ $favicon }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Core CSS -->
    @php($assetVersion = '1.5.0')
    <link rel="stylesheet" href="{{ asset('frontend/css/tokens.css') }}?v={{ $assetVersion }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/base.css') }}?v={{ $assetVersion }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/layout.css') }}?v={{ $assetVersion }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/components.css') }}?v={{ $assetVersion }}">

    <!-- Page Specific CSS -->
    @stack('styles')

    <style>
        body {
            overflow-x:hidden !important
        }
    </style>

    @if($customHeadCss = data_get($appSettings, 'custom_code.head_css'))
        <style>{!! $customHeadCss !!}</style>
    @endif

    @if($customHeadJs = data_get($appSettings, 'custom_code.head_js'))
        <script>{!! $customHeadJs !!}</script>
    @endif
</head>
<body class="@yield('body-class')">
    <!-- Navbar -->
    @include('layouts.frontend.partials.navbar')

    <!-- Mobile Drawer -->
    @include('layouts.frontend.partials.drawer')

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    @include('layouts.frontend.partials.footer')

    <!-- Core JavaScript -->
    <script src="{{ asset('frontend/js/storage.js') }}?v={{ $assetVersion }}"></script>
    <script src="{{ asset('frontend/js/components/drawer.js') }}?v={{ $assetVersion }}"></script>
    <script src="{{ asset('frontend/js/components/filters.js') }}?v={{ $assetVersion }}"></script>
    <script src="{{ asset('frontend/js/components/cards.js') }}?v={{ $assetVersion }}"></script>

    <script>
        // Global configuration
        window.Dalel = window.Dalel || {};
        window.Dalel.config = {
            csrfToken: '{{ csrf_token() }}',
            baseUrl: '{{ url('/') }}',
            apiUrl: '{{ url('/api') }}',
            locale: '{{ app()->getLocale() }}',
            isRtl: {{ is_rtl() == 'rtl' ? 'true' : 'false' }},
            translations: {
                loading: '{{ __('frontend.loading') }}',
                error: '{{ __('frontend.error') }}',
                success: '{{ __('frontend.success') }}',
                loadMore: '{{ __('frontend.load_more') }}',
            }
        };
    </script>

    <!-- Page Specific JS -->
    @stack('scripts')

    <!-- Main App JS -->
    <script src="{{ asset('frontend/js/app.js') }}?v={{ $assetVersion }}"></script>

    @if($customBodyJs = data_get($appSettings, 'custom_code.body_js'))
        <script>{!! $customBodyJs !!}</script>
    @endif
</body>
</html>

