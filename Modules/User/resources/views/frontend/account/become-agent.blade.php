@extends('layouts.frontend.master')

@section('title', __('frontend.account.become_agent'))
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
            <h1 class="p-static__title">{{ __('frontend.account.become_agent') }}</h1>
            <p class="p-static__subtitle">{{ __('frontend.account.become_agent_desc') }}</p>
        </div>

        <div class="p-static__content">
            <div class="p-static__card">
                <!-- Benefits -->
                <div
                    style="background: var(--c-primary-light); padding: var(--space-20); border-radius: var(--radius-lg); margin-bottom: var(--space-24);">
                    <h3
                        style="font-weight: var(--font-weight-semibold); margin-bottom: var(--space-12); color: var(--c-primary);">
                        {{ __('frontend.account.agent_benefits_title') }}</h3>
                    <ul
                        style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: var(--space-8);">
                        <li style="display: flex; align-items: center; gap: var(--space-8);">
                            <svg style="width: 20px; height: 20px; color: var(--c-primary);" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            {{ __('frontend.account.agent_benefit_1') }}
                        </li>
                        <li style="display: flex; align-items: center; gap: var(--space-8);">
                            <svg style="width: 20px; height: 20px; color: var(--c-primary);" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            {{ __('frontend.account.agent_benefit_2') }}
                        </li>
                        <li style="display: flex; align-items: center; gap: var(--space-8);">
                            <svg style="width: 20px; height: 20px; color: var(--c-primary);" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            {{ __('frontend.account.agent_benefit_3') }}
                        </li>
                    </ul>
                </div>

                @if($user->office_request_status === 'pending')
                <div style="background: #FEF3C7; color: #92400E; padding: var(--space-20); border-radius: var(--radius-lg); text-align: center; margin-bottom: var(--space-24);">
                    <svg style="width: 48px; height: 48px; margin-bottom: var(--space-12); display: inline-block;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h3 style="margin-bottom: var(--space-8); font-weight: bold;">طلبك قيد المراجعة</h3>
                    <p>شكراً لتقديمك الطلب. يقوم فريقنا حالياً بمراجعة بياناتك وسيتم الرد عليك قريباً.</p>
                    <a href="{{ route('frontend.account.dashboard') }}" class="c-btn c-btn--outline" style="margin-top: var(--space-16); display: inline-block;">العودة للوحة التحكم</a>
                </div>
                @else
                    @if($user->office_request_status === 'rejected')
                    <div style="background: #FEE2E2; color: #B91C1C; padding: var(--space-20); border-radius: var(--radius-lg); margin-bottom: var(--space-24);">
                        <h3 style="font-weight: bold; margin-bottom: var(--space-8);">تم رفض طلبك</h3>
                        <p>نأسف لإبلاغك بأن طلب تحويل حسابك إلى مكتب عقاري قد تم رفضه. يمكنك تعديل البيانات وإعادة الإرسال.</p>
                        @if($user->office_rejection_reason)
                        <div style="margin-top: var(--space-12); background: rgba(255,255,255,0.5); padding: var(--space-12); border-radius: var(--radius-sm);">
                            <strong>سبب الرفض:</strong>
                            {{ $user->office_rejection_reason }}
                        </div>
                        @endif
                    </div>
                    @endif
                    
                    <form action="{{ route('frontend.account.become-agent.store') }}" method="POST" id="becomeAgentForm">
                        @csrf

                    <!-- Company Name -->
                    <div class="c-form-group">
                        <label class="c-form-label">{{ __('frontend.account.company_name') }} <span
                                style="color: #dc2626;">*</span></label>
                        <input type="text" name="company_name" class="c-input" value="{{ old('company_name') }}"
                            required placeholder="{{ __('frontend.account.company_name_placeholder') }}">
                    </div>

                    <!-- License Number -->
                    {{-- <div class="c-form-group">
                        <label class="c-form-label">{{ __('frontend.account.license_number') }}</label>
                        <input type="text" name="license_number" class="c-input" value="{{ old('license_number') }}"
                            placeholder="{{ __('frontend.account.license_number_placeholder') }}">
                    </div> --}}

                    <!-- Address -->
                    <div class="c-form-group">
                        <label class="c-form-label">{{ __('frontend.account.office_address') }}</label>
                        <textarea name="address" class="c-input" rows="3"
                            placeholder="{{ __('frontend.account.office_address_placeholder') }}">{{ old('address') }}</textarea>
                    </div>

                    <div style="display: flex; gap: var(--space-12); margin-top: var(--space-24);">
                        <a href="{{ route('frontend.account.dashboard') }}" class="c-btn c-btn--outline c-btn--lg">
                            {{ __('frontend.cancel') }}
                        </a>
                        <button type="button" onclick="submitBecomeAgentForm()" class="c-btn c-btn--primary c-btn--lg" style="flex: 1;" id="submitBtn">
                            {{ __('frontend.account.submit_request') }}
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    async function submitBecomeAgentForm() {
        const form = document.getElementById('becomeAgentForm');
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
                    text: data.message || 'تم تقديم الطلب بنجاح',
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