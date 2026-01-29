@extends('layouts.frontend.master')

@section('title', 'نسيت كلمة المرور')
@section('body-class', 'p-login page-forgot-password')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/login.css') }}">

    <style>
        .p-login__alert--error p {
            color: red
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
                            <circle cx="12" cy="12" r="4"></circle>
                            <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"></path>
                        </svg>
                    </div>
                    <h1 class="p-login__title">نسيت كلمة المرور</h1>
                    <p class="p-login__subtitle">أدخل رقم هاتفك لإرسال رمز التحقق</p>
                </div>

                @if(session('status'))
                    <div class="p-login__alert p-login__alert--success">
                        {{ session('status') }}
                    </div>
                @endif

                @if(session('dev_otp'))
                    <div class="p-login__alert p-login__alert--success">
                        <strong>كود التحقق للتطوير:</strong> {{ session('dev_otp') }}
                    </div>
                @endif

                <div id="ajax-error-container" class="p-login__alert p-login__alert--error" style="display: none;"></div>

                @if($errors->any())
                    <div class="p-login__alert p-login__alert--error">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form class="p-login__form" method="POST" action="{{ route('frontend.password.send') }}">
                    @csrf

                    <!-- Phone Number -->
                    <div class="c-form-group">
                        <label for="phone" class="c-form-label">رقم الهاتف</label>
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

                    <!-- Submit -->
                    <div class="p-login__submit">
                        <button type="submit" class="c-btn c-btn--primary c-btn--lg c-btn--block">
                            إرسال رمز التحقق
                            <svg class="c-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="15 18 9 12 15 6"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="p-login__register">
                    تذكرت كلمة المرور؟
                    <a href="{{ route('frontend.login') }}" class="p-login__register-link">تسجيل الدخول</a>
                </div>
            </div>
        </div>
    </main>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('frontend/js/pages/forgot-password.js') }}"></script>
@endpush
@endsection
