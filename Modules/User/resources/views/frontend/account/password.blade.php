@extends('layouts.frontend.master')

@section('title', __('frontend.account.change_password'))
@section('body-class', 'page-account')

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/css/pages/account.css') }}">
<style>
    .c-form-error {
        color: #DC2626;
        font-size: 0.85rem;
        margin-top: 4px;
        display: block;
    }
    
    .c-input.is-invalid {
        border-color: #DC2626 !important;
    }
</style>
@endpush

@section('content')
<main class="p-static">
    <div class="l-container">
        <div class="p-static__header">
            <h1 class="p-static__title">{{ __('frontend.account.change_password') }}</h1>
            <p class="p-static__subtitle">{{ __('frontend.account.change_password_desc') }}</p>
        </div>

        <div class="p-static__content">
            <div class="p-static__card">
                <form action="{{ route('frontend.account.password.update') }}" method="POST" id="passwordForm">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div class="c-form-group">
                        <label class="c-form-label">{{ __('frontend.account.current_password') }}</label>
                        <input type="password" name="current_password" class="c-input" required>
                    </div>

                    <!-- New Password -->
                    <div class="c-form-group">
                        <label class="c-form-label">{{ __('frontend.account.new_password') }}</label>
                        <input type="password" name="password" class="c-input" required minlength="6">
                    </div>

                    <!-- Confirm Password -->
                    <div class="c-form-group">
                        <label class="c-form-label">{{ __('frontend.account.confirm_password') }}</label>
                        <input type="password" name="password_confirmation" class="c-input" required minlength="6">
                    </div>

                    <div style="display: flex; gap: var(--space-12); margin-top: var(--space-24);">
                        <a href="{{ route('frontend.account.dashboard') }}" class="c-btn c-btn--outline c-btn--lg">
                            {{ __('frontend.cancel') }}
                        </a>
                        <button type="button" onclick="submitPasswordForm()" class="c-btn c-btn--primary c-btn--lg" style="flex: 1;" id="submitBtn">
                            {{ __('frontend.account.update_password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    async function submitPasswordForm() {
        const form = document.getElementById('passwordForm');
        const submitBtn = document.getElementById('submitBtn');
        const originalBtnText = submitBtn.innerHTML;
        
        // Clear previous errors
        document.querySelectorAll('.c-form-error').forEach(el => el.remove());
        document.querySelectorAll('.c-input.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg style="animation: spin 1s linear infinite; width: 20px; height: 20px; margin-left: 8px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
            </svg>
            جارى الحفظ...
        `;

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                Swal.fire({
                    title: 'تم بنجاح!',
                    text: data.message || 'تم تحديث كلمة المرور بنجاح',
                    icon: 'success',
                    confirmButtonColor: '#10B981',
                    confirmButtonText: 'حسناً'
                }).then(() => {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    }
                });
            } else if (response.status === 422) {
                // Validation errors
                const errors = data.errors;
                let firstErrorInput = null;

                for (const [key, messages] of Object.entries(errors)) {
                    const input = form.querySelector(`[name="${key}"]`);
                    
                    if (input) {
                        input.classList.add('is-invalid');
                        
                        const errorSpan = document.createElement('span');
                        errorSpan.className = 'c-form-error';
                        errorSpan.textContent = messages[0];

                        const formGroup = input.closest('.c-form-group');
                        if (formGroup) {
                            formGroup.appendChild(errorSpan);
                        }
                        
                        if (!firstErrorInput) firstErrorInput = input;
                    }
                }

                if (firstErrorInput) {
                    firstErrorInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstErrorInput.focus();
                }

                Swal.fire({
                    title: 'خطأ في البيانات',
                    text: 'يرجى مراجعة البيانات المدخلة',
                    icon: 'error',
                    confirmButtonColor: '#EF4444',
                    confirmButtonText: 'حسناً'
                });
            } else {
                Swal.fire({
                    title: 'خطأ!',
                    text: data.message || 'حدث خطأ ما، يرجى المحاولة مرة أخرى',
                    icon: 'error',
                    confirmButtonColor: '#EF4444',
                    confirmButtonText: 'حسناً'
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'خطأ!',
                text: 'حدث خطأ في الاتصال، يرجى المحاولة مرة أخرى',
                icon: 'error',
                confirmButtonColor: '#EF4444',
                confirmButtonText: 'حسناً'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    }
</script>
@endpush