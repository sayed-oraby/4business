@php($direction = is_rtl() === 'rtl' ? 'rtl' : 'ltr')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $direction }}" direction="{{ $direction }}">
<head>
    <meta charset="utf-8" />
    <title>{{ __('authentication::messages.login.title', ['app' => $appSettings['app_name'] ?? config('app.name', 'Kit')]) }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $appSettings['app_description'] ?? 'Admin dashboard login' }}" />
    <meta name="keywords" content="{{ $appSettings['app_keywords'] ?? 'admin, dashboard, login' }}" />

    <!-- Favicon -->
    @if($appSettings['favicon'] ?? null)
        <link rel="shortcut icon" href="{{ Storage::url($appSettings['favicon']) }}" />
    @else
        <link rel="shortcut icon" href="{{ asset('metronic/media/logos/favicon.ico') }}" />
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" />

    <!-- Stylesheets -->
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

<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center">

    <!-- Theme mode setup -->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>

    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">

            <!-- Left side - Login Form -->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <div class="w-lg-500px p-10">

                        <!-- Logo -->
                        <div class="text-center mb-11">
                            @if($appSettings['logo'] ?? null)
                                <a href="{{ url('/') }}" class="mb-7 d-inline-block">
                                    <img alt="{{ $appSettings['app_name'] ?? 'Logo' }}"
                                         src="{{ Storage::url($appSettings['logo']) }}"
                                         class="h-75px" />
                                </a>
                            @endif

                            <h1 class="text-gray-900 fw-bolder mb-3">{{ __('authentication::messages.login.title', ['app' => $appSettings['app_name'] ?? 'Dashboard']) }}</h1>
                            <div class="text-gray-500 fw-semibold fs-6">{{ __('authentication::messages.login.subtitle') }}</div>
                        </div>

                        <!-- Flash Messages -->
                        @if(session('info'))
                            <div class="alert alert-info d-flex align-items-center p-5 mb-10">
                                <i class="ki-duotone ki-shield-tick fs-2hx text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span>{{ session('info') }}</span>
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                                <i class="ki-duotone ki-information-5 fs-2hx text-danger me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span>{{ session('error') }}</span>
                                </div>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                                <i class="ki-duotone ki-check-circle fs-2hx text-success me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span>{{ session('success') }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Login Form -->
                        <form class="form w-100" method="POST" action="{{ route('dashboard.login') }}">
                            @csrf

                            <!-- Email -->
                            <div class="fv-row mb-8">
                                <label class="form-label fs-6 fw-bolder text-gray-900">{{ __('authentication::messages.fields.email') }}</label>
                                <input type="email"
                                       name="email"
                                       autocomplete="off"
                                       value="{{ old('email', config('auth.super_admin_email')) }}"
                                       placeholder="{{ config('auth.super_admin_email') }}"
                                       class="form-control bg-transparent @error('email') is-invalid @enderror"
                                       required
                                       autofocus />
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="fv-row mb-3">
                                <label class="form-label fw-bolder text-gray-900 fs-6 mb-0">{{ __('authentication::messages.fields.password') }}</label>
                                <input type="password"
                                       name="password"
                                       autocomplete="off"
                                       placeholder="••••••••"
                                       class="form-control bg-transparent @error('password') is-invalid @enderror"
                                       required />
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked />
                                    <label class="form-check-label" for="remember">
                                        {{ __('authentication::messages.login.remember') }}
                                    </label>
                                </div>
                                <div>
                                    <a href="{{ route('dashboard.password.request') }}" class="link-primary fs-6 fw-semibold">
                                        {{ __('authentication::messages.login.forgot') }}
                                    </a>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-10">
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">{{ __('authentication::messages.login.button') }}</span>
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

                <!-- Footer -->
                <div class="d-flex flex-center flex-wrap px-5">
                    <div class="d-flex fw-semibold text-primary fs-base">
                        <span class="text-muted me-1">{{ date('Y') }} ©</span>
                        <a href="{{ $appSettings['app_url'] ?? url('/') }}" class="text-hover-primary">{{ $appSettings['app_name'] ?? config('app.name') }}</a>
                    </div>
                </div>
            </div>

            <!-- Right side - Background Image -->
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2"
                 style="background-image: url({{ asset('metronic/media/misc/auth-bg.png') }})">
                <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">

                    @php($authLogo = setting_media_url($appSettings['logo_white'] ?? $appSettings['logo'] ?? null))
                    @if($authLogo)
                        <a href="{{ url('/') }}" class="mb-12">
                            <img alt="{{ $appSettings['app_name'] ?? 'Logo' }}"
                                 src="{{ $authLogo }}"
                                 class="h-100px" />
                        </a>
                    @else
                        <img class="d-none d-lg-block mx-auto w-275px w-md-50 w-xl-500px mb-10 mb-lg-20"
                             src="{{ asset('metronic/media/misc/auth-screens.png') }}" alt="" />
                    @endif

                    <h1 class="d-none d-lg-block text-white fs-2qx fw-bolder text-center mb-7">
                        {{ $appSettings['app_slogan'] ?? 'Fast, Efficient and Productive' }}
                    </h1>

                    <div class="d-none d-lg-block text-white fs-base text-center">
                        {{ $appSettings['app_description'] ?? 'Modern dashboard built with Laravel & Metronic' }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('metronic/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('metronic/js/scripts.bundle.js') }}"></script>
</body>
</html>
