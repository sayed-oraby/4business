@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $homeUrl = url('/');
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="shortcut icon" href="{{ setting_media_url(setting('favicon') ?? null, asset('frontend/img/favicon.png')) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --c-primary: #5ba3d0;
            --c-primary-dark: #1e3a5f;
            --c-primary-light: #e8f4fc;
            --c-bg: #f8fafc;
            --c-text: #1f2937;
            --c-text-secondary: #6b7280;
            --c-border: #e5e7eb;
            --font-family: {{ $isRtl ? "'Cairo', sans-serif" : "'Plus Jakarta Sans', sans-serif" }};
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background: linear-gradient(135deg, var(--c-primary-light) 0%, var(--c-bg) 50%, #fff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            direction: {{ $isRtl ? 'rtl' : 'ltr' }};
        }

        .error-container {
            max-width: 600px;
            width: 100%;
            text-align: center;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-icon {
            width: 140px;
            height: 140px;
            margin: 0 auto 32px;
            background: var(--c-primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .error-icon::before {
            content: '';
            position: absolute;
            inset: -8px;
            border: 2px dashed var(--c-primary);
            border-radius: 50%;
            opacity: 0.3;
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .error-icon svg {
            width: 64px;
            height: 64px;
            color: var(--c-primary);
        }

        .error-code {
            font-size: 80px;
            font-weight: 700;
            color: var(--c-primary);
            line-height: 1;
            margin-bottom: 16px;
            text-shadow: 2px 2px 0 var(--c-primary-light);
        }

        .error-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--c-primary-dark);
            margin-bottom: 12px;
        }

        .error-message {
            font-size: 16px;
            color: var(--c-text-secondary);
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .error-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--c-primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--c-primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(91, 163, 208, 0.3);
        }

        .btn-secondary {
            background: #fff;
            color: var(--c-text);
            border: 2px solid var(--c-border);
        }

        .btn-secondary:hover {
            border-color: var(--c-primary);
            color: var(--c-primary);
        }

        .btn svg {
            width: 20px;
            height: 20px;
        }

        /* Logo */
        .error-logo {
            margin-bottom: 40px;
        }

        .error-logo img {
            height: 48px;
            width: auto;
        }

        /* Decorative elements */
        .decoration {
            position: fixed;
            border-radius: 50%;
            background: var(--c-primary);
            opacity: 0.05;
            z-index: -1;
        }

        .decoration-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            {{ $isRtl ? 'left' : 'right' }}: -100px;
        }

        .decoration-2 {
            width: 200px;
            height: 200px;
            bottom: -50px;
            {{ $isRtl ? 'right' : 'left' }}: -50px;
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 60px;
            }
            .error-title {
                font-size: 22px;
            }
            .error-icon {
                width: 100px;
                height: 100px;
            }
            .error-icon svg {
                width: 48px;
                height: 48px;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="decoration decoration-1"></div>
    <div class="decoration decoration-2"></div>

    <div class="error-container">
        <!-- Logo -->
        <div class="error-logo">
            <a href="{{ $homeUrl }}">
                <img src="{{ setting_media_url(setting('logo') ?? null, asset('frontend/img/logo.png')) }}" alt="{{ config('app.name') }}">
            </a>
        </div>

        <!-- Icon -->
        <div class="error-icon">
            @yield('icon')
        </div>

        <!-- Error Code -->
        <div class="error-code">@yield('code', '000')</div>

        <!-- Title -->
        <h1 class="error-title">@yield('heading')</h1>

        <!-- Message -->
        <p class="error-message">@yield('message')</p>

        <!-- Actions -->
        <div class="error-actions">
            <a href="{{ $homeUrl }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                {{ $isRtl ? 'العودة للرئيسية' : 'Back to Home' }}
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="{{ $isRtl ? '9 18 15 12 9 6' : '15 18 9 12 15 6' }}"/>
                </svg>
                {{ $isRtl ? 'الصفحة السابقة' : 'Go Back' }}
            </a>
        </div>
    </div>
</body>
</html>
