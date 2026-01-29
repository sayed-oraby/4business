@php($direction = is_rtl() === 'rtl' ? 'rtl' : 'ltr')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $direction }}" direction="{{ $direction }}">
<head>
    <meta charset="utf-8" />
    <title>{{ __('authentication::messages.password.forgot_title') }} - {{ $appSettings['app_name'] ?? config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" />
    <link href="{{ asset('metronic/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('metronic/css/style.bundle.css') }}" rel="stylesheet" />
    @if($direction === 'rtl')
        <style>
            body {
                font-family: 'Cairo', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            }
        </style>
    @endif
</head>
<body class="app-blank">
<div class="d-flex flex-column flex-root" id="kt_app_root">
    <div class="d-flex flex-column flex-lg-row flex-column-fluid">
        <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
            <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                <div class="w-lg-500px p-10">
                    <div class="text-center mb-11">
                        <h1 class="text-gray-900 fw-bolder mb-3">{{ __('authentication::messages.password.forgot_title') }}</h1>
                        <div class="text-gray-500 fw-semibold fs-6">{{ __('authentication::messages.password.forgot_subtitle') }}</div>
                    </div>

                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <form class="form w-100" method="POST" action="{{ route('dashboard.password.email') }}">
                        @csrf
                        <div class="fv-row mb-8">
                            <label class="form-label fs-6 fw-bolder text-gray-900">{{ __('authentication::messages.fields.email') }}</label>
                            <input type="email" name="email" value="{{ old('email', config('auth.super_admin_email')) }}" class="form-control bg-transparent @error('email') is-invalid @enderror" required />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid mb-10">
                            <button type="submit" class="btn btn-primary text-white fw-bold">
                                <span class="indicator-label">{{ __('authentication::messages.password.send_otp') }}</span>
                            </button>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('dashboard.login') }}" class="link-primary fw-semibold">{{ __('authentication::messages.login.button') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2" style="background-image: url({{ asset('metronic/media/misc/auth-bg.png') }})"></div>
    </div>
</div>
</body>
</html>
