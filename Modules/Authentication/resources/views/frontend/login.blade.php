@extends('layouts.frontend.master')

@section('title', __('frontend.auth.login_title'))
@section('body-class', 'p-login page-login')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/login.css') }}">

    <style>
        .p-login__alert--error p {
            color:red
        }
    </style>
@endpush

@section('content')
    <!-- ========== MAIN CONTENT ========== -->
    <main class="p-login__main">
        <div class="l-auth-container">
            <div class="p-login__card">
                <div class="p-login__header">
                    <div class="p-login__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                    </div>
                    <h1 class="p-login__title">{{ __('frontend.auth.login_title') }}</h1>
                    <p class="p-login__subtitle">{{ __('frontend.auth.login_subtitle') }}</p>
                </div>

                @if(session('status'))
                    <div class="p-login__alert p-login__alert--success">
                        {{ session('status') }}
                    </div>
                @endif

                <div id="ajax-error-container" class="p-login__alert p-login__alert--error" style="display: none;">
                </div>

                @if($errors->any())
                    <div class="p-login__alert p-login__alert--error">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form class="p-login__form" id="loginForm" method="POST" action="{{ route('frontend.login.post') }}">
                    @csrf

                    <!-- Phone Number -->
                    <div class="c-form-group">
                        <label for="phone" class="c-form-label">{{ __('frontend.auth.phone') }}</label>
                        <div class="p-login__phone-group">
                            <div class="p-login__country">
                                <div class="p-login__flag">
                                    <img src="{{ asset('frontend/img/kwt.png') }}" alt="Kuwait">
                                </div>
                                +965
                            </div>
                            <input type="tel" id="phone" name="phone" class="c-input p-login__phone-input"
                                   placeholder="5xxx xxxx" inputmode="numeric" required value="{{ old('phone') }}">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="c-form-group">
                        <label for="password" class="c-form-label">{{ __('frontend.auth.password') }}</label>
                        <input type="password" id="password" name="password" class="c-input"
                               placeholder="{{ __('frontend.auth.password_placeholder') }}" required>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="p-login__options">
                        <div class="p-login__remember">
                            <input type="checkbox" id="remember" name="remember" class="p-login__remember-input">
                            <label for="remember" class="p-login__remember-label">{{ __('frontend.auth.remember') }}</label>
                        </div>
                        <a href="{{ route('frontend.password.request') }}" class="p-login__forgot">{{ __('frontend.auth.forgot_password') }}</a>
                    </div>

                    <!-- Submit -->
                    <div class="p-login__submit">
                        <button type="submit" class="c-btn c-btn--primary c-btn--lg c-btn--block">
                            {{ __('frontend.auth.login_btn') }}
                            <svg class="c-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="15 18 9 12 15 6"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                {{-- <div class="p-login__divider">
                    <div class="p-login__divider-line"></div>
                    <span class="p-login__divider-text">{{ __('frontend.auth.or') }}</span>
                    <div class="p-login__divider-line"></div>
                </div> --}}

                <!-- Social Login -->
                {{-- <div class="p-login__social">
                    <a href="{{ route('frontend.social.redirect', 'google') }}" class="p-login__social-btn">
                        <svg class="p-login__social-icon" viewBox="0 0 24 24" fill="#4285F4">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Google
                    </a>
                    <a href="{{ route('frontend.social.redirect', 'apple') }}" class="p-login__social-btn">
                        <svg class="p-login__social-icon" viewBox="0 0 24 24" fill="#000">
                            <path d="M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.989C18.343 21.129 22 16.99 22 12c0-5.523-4.477-10-10-10z"/>
                        </svg>
                        Apple
                    </a>
                </div> --}}

                <div class="p-login__register">
                    {{ __('frontend.auth.no_account') }}
                    <a href="{{ route('frontend.register') }}" class="p-login__register-link">{{ __('frontend.auth.register_now') }}</a>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('frontend/js/pages/login.js') }}"></script>
@endpush

