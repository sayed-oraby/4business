@extends('layouts.frontend.master')

@section('title', 'التحقق من رمز OTP')
@section('body-class', 'p-login page-verify-otp')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/login.css') }}">

    <style>
        .p-login__alert--error p {
            color: red
        }

        .p-login__otp-inputs {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 24px;
            direction: ltr;
        }

        .p-login__otp-input {
            width: 60px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .p-login__otp-input:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .p-login__otp-input.filled {
            border-color: #4CAF50;
            background-color: #f1f8f4;
        }

        .p-login__resend {
            text-align: center;
            margin-top: 16px;
            color: #666;
        }

        .p-login__resend strong {
            color: #4CAF50;
            font-size: 18px;
        }

        .p-login__resend-link {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .p-login__resend-link:hover {
            text-decoration: underline;
            color: #45a049;
        }

        .p-login__resend-link:disabled {
            color: #999;
            cursor: not-allowed;
            text-decoration: none;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.6;
            }
        }

        #timerText strong {
            animation: pulse 1s ease-in-out infinite;
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
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </div>
                    <h1 class="p-login__title">تحقق من رمز OTP</h1>
                    <p class="p-login__subtitle">أدخل الرمز المكون من 4 أرقام المرسل إلى {{ substr($mobile, -8) }}</p>
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

                <form class="p-login__form" id="otpForm" method="POST" action="{{ route('frontend.password.otp.verify') }}">
                    @csrf
                    <input type="hidden" name="mobile" value="{{ $mobile }}">

                    <!-- OTP Inputs -->
                    <div class="p-login__otp-inputs">
                        <input type="text" class="p-login__otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required autocomplete="off">
                        <input type="text" class="p-login__otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required autocomplete="off">
                        <input type="text" class="p-login__otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required autocomplete="off">
                        <input type="text" class="p-login__otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required autocomplete="off">
                    </div>

                    <input type="hidden" name="otp" id="otpValue">

                    <!-- Submit -->
                    <div class="p-login__submit">
                        <button type="submit" class="c-btn c-btn--primary c-btn--lg c-btn--block">
                            تحقق
                            <svg class="c-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </button>
                    </div>

                </form>

                <div class="p-login__resend">
                    <form method="POST" action="{{ route('frontend.password.otp.resend') }}" role="form" id="resendForm">
                        @csrf
                        <input type="hidden" name="mobile" value="{{ $mobile }}">
                        <button type="submit" class="p-login__resend-link" id="resendBtn" form="resendForm" disabled style="background: none; border: none; cursor: pointer; padding: 0;">
                            إعادة إرسال الرمز (<span id="countdown">60</span>)
                        </button>
                    </form>
                </div>

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
            const inputs = document.querySelectorAll('.p-login__otp-input');
            const otpValue = document.getElementById('otpValue');
            const form = document.getElementById('otpForm');

            inputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;

                    // Only allow numbers
                    if (!/^\d*$/.test(value)) {
                        e.target.value = '';
                        return;
                    }

                    // Add filled class
                    if (value) {
                        e.target.classList.add('filled');
                    } else {
                        e.target.classList.remove('filled');
                    }

                    // Move to next input
                    if (value && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }

                    // Update hidden input
                    updateOtpValue();
                });

                input.addEventListener('keydown', function(e) {
                    // Move to previous input on backspace
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });

                // Handle paste
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text');
                    const digits = pastedData.replace(/\D/g, '').slice(0, 4);

                    digits.split('').forEach((digit, i) => {
                        if (inputs[i]) {
                            inputs[i].value = digit;
                            inputs[i].classList.add('filled');
                        }
                    });

                    // Focus last filled input
                    const lastIndex = Math.min(digits.length, inputs.length - 1);
                    inputs[lastIndex].focus();

                    updateOtpValue();
                });
            });

            function updateOtpValue() {
                const otp = Array.from(inputs).map(input => input.value).join('');
                otpValue.value = otp;
            }

            // Auto-focus first input
            inputs[0].focus();

            // Countdown Timer
            let timeLeft = 60;
            const resendBtn = document.getElementById('resendBtn');
            const countdownElement = document.getElementById('countdown');

            function updateTimer() {
                if (timeLeft > 0) {
                    countdownElement.textContent = timeLeft;
                    timeLeft--;
                } else {
                    // Enable button
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = 'إعادة إرسال الرمز';
                    clearInterval(timerInterval);
                }
            }

            // Start countdown
            const timerInterval = setInterval(updateTimer, 1000);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('frontend/js/pages/forgot-password.js') }}"></script>
@endpush
