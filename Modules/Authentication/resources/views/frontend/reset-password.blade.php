@extends('layouts.frontend.master')

@section('title', 'إعادة تعيين كلمة المرور')
@section('body-class', 'p-login page-reset-password')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/login.css') }}">

    <style>
        .p-login__alert--error p {
            color: red
        }

        .p-login__password-strength {
            margin-top: 8px;
            font-size: 12px;
            color: #666;
        }

        .p-login__password-strength.weak {
            color: #f44336;
        }

        .p-login__password-strength.medium {
            color: #ff9800;
        }

        .p-login__password-strength.strong {
            color: #4CAF50;
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
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <h1 class="p-login__title">إعادة تعيين كلمة المرور</h1>
                    <p class="p-login__subtitle">أدخل كلمة المرور الجديدة لحسابك</p>
                </div>

                @if(session('status'))
                    <div class="p-login__alert p-login__alert--success">
                        {{ session('status') }}
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

                <form class="p-login__form" id="resetPasswordForm" method="POST" action="{{ route('frontend.password.update') }}">
                    @csrf
                    <input type="hidden" name="mobile" value="{{ $mobile }}">

                    <!-- New Password -->
                    <div class="c-form-group">
                        <label for="password" class="c-form-label">كلمة المرور الجديدة</label>
                        <input type="password" id="password" name="password" class="c-input"
                               placeholder="أدخل كلمة المرور الجديدة" required minlength="6">
                        <div class="p-login__password-strength" id="passwordStrength"></div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="c-form-group">
                        <label for="password_confirmation" class="c-form-label">تأكيد كلمة المرور</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="c-input"
                               placeholder="أعد إدخال كلمة المرور" required minlength="6">
                    </div>

                    <!-- Submit -->
                    <div class="p-login__submit">
                        <button type="submit" class="c-btn c-btn--primary c-btn--lg c-btn--block">
                            إعادة تعيين كلمة المرور
                            <svg class="c-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const strengthIndicator = document.getElementById('passwordStrength');
            const form = document.getElementById('resetPasswordForm');

            // Password strength checker
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;

                if (password.length >= 6) strength++;
                if (password.length >= 8) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;

                strengthIndicator.className = 'p-login__password-strength';
                
                if (password.length === 0) {
                    strengthIndicator.textContent = '';
                } else if (strength <= 2) {
                    strengthIndicator.textContent = 'ضعيفة';
                    strengthIndicator.classList.add('weak');
                } else if (strength <= 3) {
                    strengthIndicator.textContent = 'متوسطة';
                    strengthIndicator.classList.add('medium');
                } else {
                    strengthIndicator.textContent = 'قوية';
                    strengthIndicator.classList.add('strong');
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('frontend/js/pages/forgot-password.js') }}"></script>
@endpush
