<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}" dir="{{ ($locale ?? 'en') === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('authentication::messages.emails.otp.subject', [], $locale ?? 'en') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f5f5f5; 
            margin: 0; 
            padding: 40px 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 480px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            padding: 32px;
            text-align: center;
        }
        .logo {
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 40px 32px;
        }
        .title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 16px;
        }
        .intro {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 32px;
        }
        .otp-box {
            background: #f8fafc;
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            margin-bottom: 24px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #6366f1;
            font-family: 'Courier New', monospace;
        }
        .expires {
            font-size: 14px;
            color: #6b7280;
            text-align: center;
            margin-bottom: 32px;
        }
        .warning {
            background: #fef3c7;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .warning-text {
            font-size: 14px;
            color: #92400e;
        }
        .outro {
            font-size: 14px;
            color: #6b7280;
            margin-top: 24px;
        }
        .footer {
            background: #f9fafb;
            padding: 24px 32px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-text {
            font-size: 13px;
            color: #9ca3af;
        }
        .team {
            font-size: 15px;
            font-weight: 600;
            color: #374151;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header with Logo --}}
        <div class="header">
            <div class="logo">{{ config('app.name', '4Jobs') }}</div>
        </div>
        
        {{-- Content --}}
        <div class="content">
            <h1 class="title">
                {{ __('authentication::messages.emails.otp.title', [], $locale ?? 'en') }}
            </h1>
            
            <p class="intro">
                {{ __('authentication::messages.emails.otp.intro', [], $locale ?? 'en') }}
            </p>
            
            {{-- OTP Code Box --}}
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>
            
            <p class="expires">
                {{ __('authentication::messages.emails.otp.expires', ['minutes' => $expires], $locale ?? 'en') }}
            </p>
            
            {{-- Security Warning --}}
            <div class="warning">
                <p class="warning-text">
                    {{ __('authentication::messages.emails.otp.security', [], $locale ?? 'en') }}
                </p>
            </div>
            
            <p class="outro">
                {{ __('authentication::messages.emails.otp.outro', [], $locale ?? 'en') }}
            </p>
            
            <p class="team">
                {{ __('authentication::messages.emails.otp.team', ['app' => config('app.name', '4Jobs')], $locale ?? 'en') }}
            </p>
        </div>
        
        {{-- Footer --}}
        <div class="footer">
            <p class="footer-text">
                © {{ date('Y') }} {{ config('app.name', '4Jobs') }}. {{ __('authentication::messages.emails.otp.rights', [], $locale ?? 'en') }}
            </p>
        </div>
    </div>
</body>
</html>
