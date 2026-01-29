@php($currentLocaleMeta = available_locales()[app()->getLocale()] ?? ['dir' => 'ltr'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $currentLocaleMeta['dir'] ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ setting('branding.application_name', config('app.name')) }}</title>
    @vite('resources/css/app.css')
</head>
<body class="antialiased">
    @yield('content')
</body>
</html>
