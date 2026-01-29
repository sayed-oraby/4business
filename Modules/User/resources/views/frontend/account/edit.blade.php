@extends('layouts.frontend.master')

@section('title', __('frontend.account.edit_profile'))
@section('body-class', 'page-account')

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/css/pages/account.css') }}">

<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
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
            <h1 class="p-static__title">{{ __('frontend.account.edit_profile') }}</h1>
            <p class="p-static__subtitle">{{ __('frontend.account.edit_profile_desc') }}</p>
        </div>

        <div class="p-static__content">
            <div class="p-static__card">
                <form action="{{ route('frontend.account.update') }}" method="POST" enctype="multipart/form-data" id="editProfileForm">
                    @csrf
                    @method('PUT')

                    <!-- Avatar -->
                    <div class="c-form-group" style="text-align: center; margin-bottom: var(--space-24);">
                        <div
                            style="width: 100px; height: 100px; border-radius: 50%; background: var(--c-primary-light); margin: 0 auto var(--space-16); overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                style="width: 100%; height: 100%; object-fit: cover;" id="avatarPreview">
                            @else
                            <svg id="avatarPlaceholder" style="width: 50px; height: 50px; color: var(--c-primary);"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            <img src="" alt="" style="width: 100%; height: 100%; object-fit: cover; display: none;"
                                id="avatarPreview">
                            @endif
                        </div>
                        <label class="c-btn c-btn--outline c-btn--sm" style="cursor: pointer;">
                            {{ __('frontend.account.change_avatar') }}
                            <input type="file" name="avatar" accept="image/*" style="display: none;"
                                onchange="previewAvatar(this)">
                        </label>
                    </div>

                    <!-- Name -->
                    <div class="c-form-group">
                        <label class="c-form-label">{{ __('frontend.auth.name') }}</label>
                        <input type="text" name="name" class="c-input" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <!-- Email -->
                    <div class="c-form-group">
                        <label class="c-form-label">{{ __('frontend.contact.email') }}</label>
                        <input type="email" name="email" class="c-input" value="{{ old('email', $user->email) }}"
                            required>
                    </div>

                    @if($user->account_type === 'office')
                    <!-- Company Name -->
                    <div class="c-form-group">
                        <label class="c-form-label">اسم الشركة</label>
                        <input type="text" name="company_name" class="c-input" value="{{ old('company_name', $user->company_name) }}">
                    </div>

                    <!-- Address -->
                    <div class="c-form-group">
                        <label class="c-form-label">العنوان</label>
                        <textarea name="address" class="c-input" rows="3">{{ old('address', $user->address) }}</textarea>
                    </div>
                    @endif

                    <!-- Phone (readonly) -->
                    <div class="c-form-group">
                        <label class="c-form-label">{{ __('frontend.auth.phone') }}</label>
                        <input type="text" class="c-input" value="{{ $user->mobile }}" readonly disabled
                            style="background: var(--c-bg-soft);">
                        <small style="color: var(--c-muted);">{{ __('frontend.account.phone_readonly') }}</small>
                    </div>

                    <div style="display: flex; gap: var(--space-12); margin-top: var(--space-24);">
                        <a href="{{ route('frontend.account.dashboard') }}" class="c-btn c-btn--outline c-btn--lg">
                            {{ __('frontend.cancel') }}
                        </a>
                        <button type="button" onclick="submitForm()" class="c-btn c-btn--primary c-btn--lg" style="flex: 1;" id="submitBtn">
                            {{ __('frontend.save') }}
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
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatarPreview');
                const placeholder = document.getElementById('avatarPlaceholder');
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    async function submitForm() {
        const form = document.getElementById('editProfileForm');
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
                    text: data.message || 'تم تحديث بياناتك بنجاح',
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