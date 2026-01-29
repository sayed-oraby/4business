@extends('layouts.frontend.master')

@section('title', __('frontend.auth.register_title'))
@section('body-class', 'p-login page-register')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/login.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/static.css') }}">
@endpush

@section('content')
    <!-- ========== MAIN CONTENT ========== -->
    <main class="p-login__main">
        <div class="l-auth-container">
            <div class="p-login__card">
                <div class="p-login__header">
                    <div class="p-login__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4"/>
                            <line x1="20" y1="8" x2="20" y2="14"/>
                            <line x1="23" y1="11" x2="17" y2="11"/>
                        </svg>
                    </div>
                    <h1 class="p-login__title">{{ __('frontend.auth.register_title') }}</h1>
                    <p class="p-login__subtitle">{{ __('frontend.auth.register_subtitle') }}</p>
                </div>

                <div id="ajax-error-container" class="p-login__alert p-login__alert--error" style="display: none;">
                </div>

                @if($errors->any())
                    <div class="p-login__alert p-login__alert--error">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form class="p-login__form" id="registerForm" method="POST" action="{{ route('frontend.register.post') }}">
                    @csrf

                    <!-- Account Type Selection -->
                    <div class="p-register__types">
                        <div class="p-register__type">
                            <input type="radio" name="account_type" required value="individual" id="typeIndividual" class="p-register__type-input" @checked(old('account_type', 'individual') == 'individual')>
                            <label for="typeIndividual" class="p-register__type-label">
                            <div class="p-register__type-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <span class="p-register__type-text">فرد</span>
                            </label>
                        </div>
                        <div class="p-register__type">
                            <input type="radio" name="account_type" required value="office" id="typeOffice" class="p-register__type-input" @checked(old('account_type') == 'office')>
                            <label for="typeOffice" class="p-register__type-label">
                            <div class="p-register__type-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            </div>
                            <span class="p-register__type-text">مكتب عقاري</span>
                            </label>
                        </div>
                    </div>
                    @error('account_type')
                        <div class="text-danger" style="color: red; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror

                    <!-- Name -->
                    <div class="c-form-group">
                        <label for="name" class="c-form-label">{{ __('frontend.auth.name') }}</label>
                        <input type="text" id="name" name="name" class="c-input @error('name') is-invalid @enderror"
                               placeholder="{{ __('frontend.auth.name_placeholder') }}" required value="{{ old('name') }}">
                        @error('name')
                            <div class="text-danger" style="color: red; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

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
                            <input type="tel" id="phone" name="phone" class="c-input p-login__phone-input @error('phone') is-invalid @enderror"
                                   placeholder="5xxx xxxx" inputmode="numeric" required value="{{ old('phone') }}">
                        </div>
                        @error('phone')
                            <div class="text-danger" style="color: red; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="c-form-group">
                        <label for="email" class="c-form-label">البريد الإلكتروني (اختياري)</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="c-input @error('email') is-invalid @enderror" placeholder="example@email.com">
                        @error('email')
                            <div class="text-danger" style="color: red; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="c-form-group">
                        <label for="password" class="c-form-label">{{ __('frontend.auth.password') }}</label>
                        <input type="password" id="password" name="password" class="c-input @error('password') is-invalid @enderror"
                               placeholder="{{ __('frontend.auth.password_placeholder') }}" required>
                        @error('password')
                            <div class="text-danger" style="color: red; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="c-form-group">
                        <label for="password_confirmation" class="c-form-label">{{ __('frontend.auth.confirm_password') }}</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="c-input"
                               placeholder="{{ __('frontend.auth.password_placeholder') }}" required>
                    </div>

                    <!-- Terms -->
                    <div class="p-register__terms">
                        <input type="checkbox" id="terms" class="p-register__terms-input" required>
                        <label for="terms">
                        أوافق على 
                        <a href="{{ route('frontend.page.terms') }}">الشروط والأحكام</a> و <a href="{{ route('frontend.page.privacy') }}">سياسة الخصوصية</a>
                        </label>
                    </div>

                    <!-- Submit -->
                    <div class="p-login__submit">
                        <button type="submit" class="c-btn c-btn--primary c-btn--lg c-btn--block">
                            {{ __('frontend.auth.register_btn') }}
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
                    {{ __('frontend.auth.has_account') }}
                    <a href="{{ route('frontend.login') }}" class="p-login__register-link">{{ __('frontend.auth.login_now') }}</a>
                </div>
            </div>
        </div>
    </main>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('frontend/js/pages/register.js') }}"></script>
@endpush
@endsection

