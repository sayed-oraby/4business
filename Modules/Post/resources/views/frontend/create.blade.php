@extends('layouts.frontend.master')

@section('title', __('frontend.new_listing.title'))
@section('body-class', 'p-new-listing page-new-listing')

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/css/pages/new-listing.css') }}?v=1.5.0">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('frontend/css/pages/listing_select.css') }}?v=1.5.0">
<style>
@-webkit-keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<!-- ========== HERO ========== -->
<section class="p-new-listing__hero">
    <div class="l-container">
        <div class="p-new-listing__hero-content">
            @if(isset($freePackage) && isset($freeCreditsRemaining) && $freeCreditsRemaining > 0)
            <div class="p-new-listing__free-credits">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                    <circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/>
                </svg>
                <span>لديك {{ $freeCreditsRemaining }} إعلان مجاني متبقي من أصل {{ $freePackage->free_credits_per_user }}</span>
            </div>
            @endif
            <h1 class="p-new-listing__title">{{ __('frontend.new_listing.title') }}</h1>
            <p class="p-new-listing__subtitle">{{ __('frontend.new_listing.subtitle') }}</p>

            <!-- Progress Steps -->
            <div class="p-new-listing__progress">
                <div class="p-new-listing__progress-step">
                    <div class="p-new-listing__progress-dot p-new-listing__progress-dot--active" id="dot1">1</div>
                </div>
                <div class="p-new-listing__progress-line" id="line1"></div>
                <div class="p-new-listing__progress-step">
                    <div class="p-new-listing__progress-dot" id="dot2">2</div>
                </div>
                <div class="p-new-listing__progress-line" id="line2"></div>
                <div class="p-new-listing__progress-step">
                    <div class="p-new-listing__progress-dot" id="dot3">3</div>
                </div>
            </div>
            <div class="p-new-listing__progress-label">
                <span>{{ __('frontend.new_listing.step_type') }}</span>
                <span>{{ __('frontend.new_listing.step_details') }}</span>
                <span>{{ __('frontend.new_listing.step_publish') }}</span>
            </div>
        </div>
    </div>
</section>

<!-- ========== MAIN ========== -->
<main class="p-new-listing__main">
    <div class="l-container">
        <form action="{{ route('frontend.posts.store') }}" method="POST" enctype="multipart/form-data"
            class="p-new-listing__card" id="formCard">
            @csrf



            <!-- STEP 1: Property Type -->
            <div class="p-new-listing__step p-new-listing__step--active" id="step1">
                <div class="p-new-listing__step-header">
                    <div class="p-new-listing__step-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                    </div>
                    <h2 class="p-new-listing__step-title">{{ __('frontend.new_listing.select_type') }}</h2>
                    <p class="p-new-listing__step-desc">{{ __('frontend.new_listing.select_type_hint') }}</p>
                </div>

                <div class="p-new-listing__type-grid">
                    @foreach ($categories as $category)
                    <div class="p-new-listing__type-card">
                        <input type="radio" name="category_id" value="{{ $category->id }}" id="type{{ $category->id }}"
                            class="p-new-listing__type-input" @if ($loop->first) checked @endif>
                        <label for="type{{ $category->id }}" class="p-new-listing__type-label">
                            <div class="p-new-listing__type-icon">
                                @if ($category->image_path)
                                <img src="{{ setting_media_url($category->image_path) }}" alt="{{ $category->name }}" class="p-new-listing__type-img">
                                @elseif ($category->icon)
                                <i class="{{ $category->icon }}"></i>
                                @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                </svg>
                                @endif
                            </div>
                            <span class="p-new-listing__type-name">{{ $category->name }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>

                <!-- Purpose Selection -->
                <h3 class="p-new-listing__label" style="margin-top: var(--space-24); margin-bottom: var(--space-16);">
                    {{ __('frontend.new_listing.select_purpose') }}</h3>
                <div class="p-new-listing__purpose-group">
                    @foreach ($postTypes as $postType)
                    <div class="p-new-listing__purpose">
                        <input type="radio" name="post_type_id" value="{{ $postType->id }}"
                            id="purpose{{ $postType->id }}" class="p-new-listing__purpose-input" @if ($loop->first)
                        checked @endif>
                        <label for="purpose{{ $postType->id }}" class="p-new-listing__purpose-label">
                            @if ($postType->image_path)
                            <img src="{{ setting_media_url($postType->image_path) }}" alt="{{ $postType->name }}" class="p-new-listing__purpose-img">
                            @else
                            <span class="p-new-listing__purpose-emoji">{{ $postType->icon ?? '🏠' }}</span>
                            @endif
                            <span class="p-new-listing__purpose-text">{{ $postType->name }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>

                <div class="p-new-listing__nav">
                    <button type="button" class="p-new-listing__btn p-new-listing__btn--next" onclick="nextStep(2)">
                        {{ __('frontend.new_listing.next') }}
                        <svg class="p-new-listing__btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="15 18 9 12 15 6" />
                        </svg>
                    </button>
                </div>
            </div>



            <!-- STEP 2: Details -->
            <div class="p-new-listing__step" id="step2">
                <div class="p-new-listing__step-header">
                    <div class="p-new-listing__step-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                        </svg>
                    </div>
                    <h2 class="p-new-listing__step-title">{{ __('frontend.new_listing.enter_details') }}</h2>
                    <p class="p-new-listing__step-desc">{{ __('frontend.new_listing.enter_details_hint') }}</p>
                </div>

                <div class="p-new-listing__fields">
                    <!-- Title -->
                    <div class="p-new-listing__field">
                        <label class="p-new-listing__label">{{ __('post::post.title') }}</label>
                        <input type="text" name="title" class="p-new-listing__input" required
                            value="{{ old('title') }}">
                        @error('title')
                        <span class="p-new-listing__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div class="p-new-listing__field">
                        <label class="p-new-listing__label"> المناطق </label>
                        <select name="state_id" class="p-new-listing__select" required>
                            <option value=""> المحافظات </option>
                            @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected(old('state_id')==$location->id)>
                                {{ $location->name }}</option>
                            @endforeach
                        </select>
                        @error('state_id')
                        <span class="p-new-listing__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- City -->
                    <div class="p-new-listing__field">
                        <label class="p-new-listing__label">المناطق</label>
                        <select name="city_id" class="p-new-listing__select" required disabled>
                            <option value="">اختر المنطقة</option>
                        </select>
                        @error('city_id')
                        <span class="p-new-listing__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div class="p-new-listing__field">
                        <label class="p-new-listing__label">{{ __('frontend.new_listing.price_optional') }}</label>
                        <input type="text" name="price" class="p-new-listing__input"
                            placeholder="{{ __('frontend.new_listing.price_placeholder') }}" value="{{ old('price') }}">
                        @error('price')
                        <span class="p-new-listing__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="p-new-listing__field">
                        <label class="p-new-listing__label">{{ __('frontend.new_listing.description') }}</label>
                        <textarea name="description" class="p-new-listing__textarea"
                            placeholder="{{ __('frontend.new_listing.description_placeholder') }}" maxlength="2000"
                            id="descTextarea" required>{{ old('description') }}</textarea>
                        <div class="p-new-listing__counter"><span id="charCount">{{ strlen(old('description', ''))
                                }}</span> / 2000</div>
                        @error('description')
                        <span class="p-new-listing__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Images -->
                    <div class="p-new-listing__field">
                        <label class="p-new-listing__label">{{ __('frontend.new_listing.images_optional') }}</label>
                        <label for="imageUpload" class="p-new-listing__upload">
                            <div class="p-new-listing__upload-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" />
                                    <circle cx="8.5" cy="8.5" r="1.5" />
                                    <path d="m21 15-5-5L5 21" />
                                </svg>
                            </div>
                            <div class="p-new-listing__upload-title">{{ __('frontend.new_listing.images_hint') }}
                            </div>
                            <div class="p-new-listing__upload-hint">{{ __('frontend.new_listing.images_format') }}
                                (Max
                                10 images)</div>
                            <input type="file" name="images[]" class="p-new-listing__upload-input" id="imageUpload"
                                accept="image/png, image/jpeg, image/jpg" multiple>
                        </label>
                        <div id="imagePreview" class="p-new-listing__image-preview"></div>
                    </div>
                </div>

                <div class="p-new-listing__nav">
                    <button type="button" class="p-new-listing__btn p-new-listing__btn--back" onclick="prevStep(1)">
                        <svg class="p-new-listing__btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                        {{ __('frontend.new_listing.previous') }}
                    </button>
                    <button type="button" class="p-new-listing__btn p-new-listing__btn--next" onclick="nextStep(3)">
                        {{ __('frontend.new_listing.next') }}
                        <svg class="p-new-listing__btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="15 18 9 12 15 6" />
                        </svg>
                    </button>
                </div>
            </div>



            <!-- STEP 3: Contact & Publish -->
            <div class="p-new-listing__step" id="step3">
                <div class="p-new-listing__step-header">
                    <div class="p-new-listing__step-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13" />
                            <polygon points="22 2 15 22 11 13 2 9 22 2" />
                        </svg>
                    </div>
                    <h2 class="p-new-listing__step-title">{{ __('frontend.new_listing.contact_info') }}</h2>
                    <p class="p-new-listing__step-desc">{{ __('frontend.new_listing.contact_hint') }}</p>
                </div>

                <div class="p-new-listing__fields">
                    <!-- Phone -->
                    <div class="p-new-listing__field">
                        <label class="p-new-listing__label">{{ __('frontend.new_listing.phone') }}</label>
                        <div class="p-new-listing__phone-wrapper">
                            <div class="p-new-listing__phone-code">
                                <div class="p-new-listing__flag">
                                    <img src="{{ asset('frontend/img/kwt.png') }}" alt="Kuwait">
                                </div>
                                +965
                            </div>
                            <input type="tel" name="mobile_number"
                                class="p-new-listing__input p-new-listing__phone-input" placeholder="5xxx xxxx"
                                inputmode="numeric" required value="{{ str_replace('+965', '', auth('admin')->user()->mobile) ?? old('phone') }}">
                        </div>
                        @error('mobile_number')
                        <span class="p-new-listing__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Package Selection -->
                    <h3 class="p-new-listing__label" style="margin-top: var(--space-24); margin-bottom: var(--space-16);">اختر الباقة المناسبة</h3>
                    @if($packages->isNotEmpty())
                    <div class="p-new-listing__packages">
                        @foreach($packages as $package)
                        @php
                            $isDisabled = $package->is_free && isset($freeCreditsRemaining) && $freeCreditsRemaining <= 0;
                            $cardColor = $package->card_color ?? '#eff6ff';
                            $labelColor = $package->label_color ?? '#3b82f6';
                        @endphp
                        <div class="p-new-listing__package {{ $isDisabled ? 'p-new-listing__package--disabled' : '' }}" style="background-color: {{ $cardColor }}; border-color: {{ $labelColor }};">
                            <input type="radio" name="package_id" value="{{ $package->id }}" id="package_{{ $package->id }}" class="p-new-listing__package-input" {{ $isDisabled ? 'disabled' : '' }} {{ $loop->first && !$isDisabled ? 'checked' : '' }}>
                            <label for="package_{{ $package->id }}" class="p-new-listing__package-label">
                                <div class="p-new-listing__package-check">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12" /></svg>
                                </div>
                                <div class="p-new-listing__package-icon" style="background-color: {{ $labelColor }};">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" /></svg>
                                </div>
                                <div class="p-new-listing__package-content">
                                    <div class="p-new-listing__package-header">
                                        <span class="p-new-listing__package-title">{{ $package->title }}</span>
                                        @if($package->is_free)
                                        <span class="p-new-listing__package-badge p-new-listing__package-badge--free">مجاني</span>
                                        @elseif($package->top_days > 0)
                                        <span class="p-new-listing__package-badge p-new-listing__package-badge--top" style="background-color: {{ $labelColor }};">{{ $package->top_days }} يوم مثبت</span>
                                        @endif
                                    </div>
                                    @if($package->description)
                                    <div class="p-new-listing__package-desc">{{ $package->description }}</div>
                                    @endif
                                    <div class="p-new-listing__package-features">
                                        <span class="p-new-listing__package-feature">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            {{ $package->period_days }} يوم
                                        </span>
                                        @if($package->top_days > 0)
                                        <span class="p-new-listing__package-feature p-new-listing__package-feature--highlight">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            {{ $package->top_days }} يوم في المقدمة
                                        </span>
                                        @endif
                                        @if($package->is_free && isset($freeCreditsRemaining))
                                        <span class="p-new-listing__package-feature p-new-listing__package-feature--credits">
                                            متبقي <strong>{{ $freeCreditsRemaining }}</strong> إعلان مجاني
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="p-new-listing__package-price" style="color: {{ $labelColor }};">
                                    @if($package->is_free)
                                    <span class="p-new-listing__package-price-value">مجاني</span>
                                    @else
                                    <span class="p-new-listing__package-price-value">{{ number_format($package->price, 2) }}</span>
                                    <span class="p-new-listing__package-price-currency">د.ك</span>
                                    @endif
                                </div>
                            </label>
                            @if($isDisabled)
                            <div class="p-new-listing__package-overlay">
                                <span>استنفذت رصيدك المجاني</span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @error('package_id')
                    <span class="p-new-listing__error">{{ $message }}</span>
                    @enderror
                    @endif
                </div>

                <div class="p-new-listing__nav">
                    <button type="button" class="p-new-listing__btn p-new-listing__btn--back" onclick="prevStep(2)">
                        <svg class="p-new-listing__btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                        {{ __('frontend.new_listing.previous') }}
                    </button>
                    <button type="button" onclick="submitForm()" class="p-new-listing__btn p-new-listing__btn--submit">
                        <svg class="p-new-listing__btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13" />
                            <polygon points="22 2 15 22 11 13 2 9 22 2" />
                        </svg>
                        {{ __('frontend.new_listing.publish') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('frontend/js/pages/new-listing.js') }}"></script>
<script>
    const locationsData = @json($locations);

    $(document).ready(function() {
        const stateSelect = $('select[name="state_id"]');
        const citySelect = $('select[name="city_id"]');

        stateSelect.select2({
            dir: document.dir || 'rtl',
            placeholder: "{{ __('frontend.new_listing.area_select') }}",
            allowClear: true,
            width: '100%'
        });

        citySelect.select2({
            dir: document.dir || 'rtl',
            placeholder: "اختر المنطقة",
            allowClear: true,
            width: '100%',
            disabled: true // Initially disabled
        });

        stateSelect.on('change', function() {
            const stateId = $(this).val();
            citySelect.empty().append('<option value="">اختر المحافظة</option>');

            if (stateId) {
                const selectedState = locationsData.find(state => state.id == stateId);

                if (selectedState && selectedState.cities) {
                    selectedState.cities.forEach(city => {
                        const option = new Option(city.name_ar, city.id, false, false);
                        citySelect.append(option);
                    });
                    citySelect.prop('disabled', false);
                } else {
                    citySelect.prop('disabled', true);
                }
            } else {
                citySelect.prop('disabled', true);
            }
            citySelect.trigger('change');
        });

        // Trigger change if executing with old values (old input)
        if (stateSelect.val()) {
            const oldCityId = "{{ old('city_id') }}";
            stateSelect.trigger('change');
            if (oldCityId) {
                 citySelect.val(oldCityId).trigger('change');
            }
        }
    });
</script>
<script>
    let currentStep = 1;

        function updateProgress(step) {
            for (let i = 1; i <= 3; i++) {
                const dot = document.getElementById('dot' + i);
                dot.classList.remove('p-new-listing__progress-dot--active', 'p-new-listing__progress-dot--done');
                if (i < step) {
                    dot.classList.add('p-new-listing__progress-dot--done');
                    dot.innerHTML = '✓';
                } else if (i === step) {
                    dot.classList.add('p-new-listing__progress-dot--active');
                    dot.innerHTML = i;
                } else {
                    dot.innerHTML = i;
                }
            }

            for (let i = 1; i <= 2; i++) {
                const line = document.getElementById('line' + i);
                if (i < step) {
                    line.classList.add('p-new-listing__progress-line--done');
                } else {
                    line.classList.remove('p-new-listing__progress-line--done');
                }
            }
        }

        function showStep(step) {
            document.querySelectorAll('.p-new-listing__step').forEach(el => {
                el.classList.remove('p-new-listing__step--active');
            });
            document.getElementById('step' + step).classList.add('p-new-listing__step--active');
            updateProgress(step);
            document.getElementById('formCard').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        function nextStep(step) {
            // Validate current step before proceeding forward
            if (step > currentStep) {
                const currentStepEl = document.getElementById('step' + currentStep);
                const inputs = currentStepEl.querySelectorAll('input, select, textarea');

                for (const input of inputs) {
                    if (!input.checkValidity()) {
                        input.reportValidity();
                        return;
                    }
                }
            }

            currentStep = step;
            showStep(step);
        }

        async function submitForm() {
            const form = document.getElementById('formCard');

            // Client-side validation
            if (!form.checkValidity()) {
                const invalidInput = form.querySelector(':invalid');
                if (invalidInput) {
                    const stepDiv = invalidInput.closest('.p-new-listing__step');
                    if (stepDiv) {
                        const stepNum = parseInt(stepDiv.id.replace('step', ''));
                        if (currentStep !== stepNum) {
                            currentStep = stepNum;
                            showStep(stepNum);
                        }
                        setTimeout(() => {
                            invalidInput.focus();
                            invalidInput.reportValidity();
                        }, 100);
                    }
                }
                return;
            }

            // AJAX Submission
            const submitBtn = document.querySelector('.p-new-listing__btn--submit');
            const originalBtnContent = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="p-new-listing__btn-icon" style="animation: spin 1s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                </svg>
                جارى الحفظ...
            `;

            // Clear previous errors
            document.querySelectorAll('.p-new-listing__error').forEach(el => el.remove());

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

                if (response.ok) {
                    // Check if payment is required
                    if (data.payment_required && data.payment_url) {
                        // Redirect to MyFatoorah payment page
                        Swal.fire({
                            title: 'جارٍ التوجيه للدفع',
                            text: 'سيتم توجيهك لإتمام عملية الدفع...',
                            icon: 'info',
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                        }).then(() => {
                            window.location.href = data.payment_url;
                        });
                    } else {
                        // Free package - show success message
                        Swal.fire({
                            title: 'تم بنجاح!',
                            text: data.message || 'تم إضافة الإعلان بنجاح',
                            icon: 'success',
                            confirmButtonText: 'موافق',
                            confirmButtonColor: '#10B981',
                            timer: 2500,
                            timerProgressBar: true,
                        }).then(() => {
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            } else {
                                window.location.reload();
                            }
                        });
                    }
                } else {
                    if (response.status === 422) {
                        const errors = data.errors;
                        let firstErrorInput = null;

                        for (const [key, messages] of Object.entries(errors)) {
                            // Handle array names or dot notation
                            let inputName = key;
                            if (key.includes('.')) {
                                const parts = key.split('.');
                                inputName = `${parts[0]}[]`; // Approximation for images.0 -> images[]
                            }

                            let input = form.querySelector(`[name="${inputName}"]`) || form.querySelector(`[name="${key}"]`);

                            if (input) {
                                const errorSpan = document.createElement('span');
                                errorSpan.className = 'p-new-listing__error';
                                errorSpan.textContent = messages[0];
                                scroll = true;

                                const field = input.closest('.p-new-listing__field') || input.parentElement;
                                if (field) {
                                    field.appendChild(errorSpan);
                                }

                                if (!firstErrorInput) firstErrorInput = input;
                            }
                        }

                        if (firstErrorInput) {
                            const stepDiv = firstErrorInput.closest('.p-new-listing__step');
                            if (stepDiv) {
                                const stepNum = parseInt(stepDiv.id.replace('step', ''));
                                if (currentStep !== stepNum) {
                                    currentStep = stepNum;
                                    showStep(stepNum);
                                }
                                setTimeout(() => firstErrorInput.scrollIntoView({ behavior: 'smooth', block: 'center' }), 100);
                            }
                        }
                    } else {
                        alert(data.message || 'حدث خطأ ما، يرجى المحاولة مرة أخرى.');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('حدث خطأ في الاتصال، يرجى المحاولة مرة أخرى.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnContent;
            }
        }

        function prevStep(step) {
            currentStep = step;
            showStep(step);
        }

        // Character counter
        const textarea = document.getElementById('descTextarea');
        const counter = document.getElementById('charCount');
        if (textarea && counter) {
            textarea.addEventListener('input', function() {
                counter.textContent = this.value.length;
            });
        }

        // Image preview
        // Image preview with Delete functionality
        const MAX_IMAGES = 10;
        const uploadedFiles = new DataTransfer();
        const fileInput = document.getElementById('imageUpload');
        const previewContainer = document.getElementById('imagePreview');
        const hintText = document.querySelector('.p-new-listing__upload-hint');

        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const newFiles = Array.from(e.target.files);
                let totalImages = uploadedFiles.items.length + newFiles.length;

                if (totalImages > MAX_IMAGES) {
                    hintText.classList.add('error');
                    hintText.innerText = `Maximum allowed images is ${MAX_IMAGES}. You selected ${totalImages}.`;
                    // Don't add if it exceeds limit (or we could add up to limit)
                    // Let's add up to the limit:
                    const slotsLeft = MAX_IMAGES - uploadedFiles.items.length;
                    if (slotsLeft > 0) {
                        newFiles.slice(0, slotsLeft).forEach(file => uploadedFiles.items.add(file));
                        hintText.innerText = `Added first ${slotsLeft} images. Limit reached.`;
                    }
                } else {
                    hintText.classList.remove('error');
                    // Reset hint
                    hintText.innerText = `{{ __('frontend.new_listing.images_format') }} (Max 10 images)`;

                    newFiles.forEach(file => {
                        // Check for duplicates based on name and size to avoid exact same file
                        let isDuplicate = false;
                        for (let i = 0; i < uploadedFiles.items.length; i++) {
                            const f = uploadedFiles.items[i].getAsFile();
                            if (f.name === file.name && f.size === file.size) isDuplicate = true;
                        }
                        if (!isDuplicate) uploadedFiles.items.add(file);
                    });
                }

                fileInput.files = uploadedFiles.files; // Sync input with DataTransfer
                renderPreviews();
            });
        }

        function renderPreviews() {
            previewContainer.innerHTML = '';

            if (uploadedFiles.files.length === 0) return;

            Array.from(uploadedFiles.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const item = document.createElement('div');
                    item.className = 'p-new-listing__preview-item';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'p-new-listing__preview-img';

                    const removeBtn = document.createElement('div');
                    removeBtn.className = 'p-new-listing__preview-remove';
                    removeBtn.innerHTML =
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
                    removeBtn.onclick = function() {
                        removeFile(index);
                    };

                    item.appendChild(img);
                    item.appendChild(removeBtn);
                    previewContainer.appendChild(item);
                };
                reader.readAsDataURL(file);
            });
        }

        function removeFile(index) {
            uploadedFiles.items.remove(index);
            fileInput.files = uploadedFiles.files;
            renderPreviews();

            // Reset hint if we are back within limits
            if (uploadedFiles.items.length < MAX_IMAGES) {
                hintText.classList.remove('error');
                hintText.innerText = `{{ __('frontend.new_listing.images_format') }} (Max 10 images)`;
            }
        }
</script>
@endpush
