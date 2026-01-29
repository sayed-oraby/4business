@extends('layouts.frontend.master')

@section('title', __('frontend.auth.otp_title'))
@section('body-class', 'p-otp page-otp')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/otp.css') }}">
@endpush

@section('content')
    <!-- ========== MAIN CONTENT ========== -->
    <main class="p-otp__main">
        <div class="p-otp__container">
            <div class="p-otp__card">
                <div class="p-otp__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                </div>
                
                <h1 class="p-otp__title">{{ __('frontend.auth.otp_title') }}</h1>
                <p class="p-otp__subtitle">{{ __('frontend.auth.otp_subtitle') }}</p>
                
                @if(session('mobile'))
                    <p class="p-otp__phone">{{ session('mobile') }}</p>
                @endif

                @if(session('dev_otp'))
                    <div style="background: #fdf2f2; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; color: #d32f2f;">
                        <strong>Dev OTP: {{ session('dev_otp') }}</strong>
                    </div>
                @endif

                <div id="ajax-error-container" class="p-otp__error" style="display: none;">
                </div>

                @if($errors->any())
                    <div class="p-otp__error p-otp__error--visible">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form id="otpForm" method="POST" action="{{ route('frontend.otp.verify') }}">
                    @csrf

                    <!-- OTP Input Boxes -->
                    <div class="p-otp__boxes">
                        <input type="text" name="otp[]" class="p-otp__box" maxlength="1" inputmode="numeric" pattern="[0-9]" required autofocus>
                        <input type="text" name="otp[]" class="p-otp__box" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                        <input type="text" name="otp[]" class="p-otp__box" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                        <input type="text" name="otp[]" class="p-otp__box" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    </div>

                    <!-- Timer -->
                    <div class="p-otp__timer" id="timerWrapper">
                        <span class="p-otp__countdown" id="countdown">60</span>
                        <p class="p-otp__timer-label">{{ __('frontend.auth.otp_resend_timer') }}</p>
                    </div>

                    <!-- Resend -->
                    <div class="p-otp__resend">
                        <span class="p-otp__resend-text">{{ __('frontend.auth.otp_not_received') }}</span>
                        <a href="{{ route('frontend.otp.resend') }}" class="p-otp__resend-link p-otp__resend-link--disabled" id="resendLink">{{ __('frontend.auth.otp_resend') }}</a>
                    </div>

                    <!-- Submit -->
                    <div class="p-otp__submit">
                        <button type="submit" class="c-btn c-btn--primary c-btn--lg c-btn--block">
                            {{ __('frontend.auth.otp_verify') }}
                            <svg class="c-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Back to login -->
                <a href="{{ route('frontend.login') }}" class="p-otp__back">
                    <svg class="p-otp__back-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                    {{ __('frontend.auth.back_to_login') }}
                </a>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('frontend/js/pages/otp.js') }}"></script>
@endpush
