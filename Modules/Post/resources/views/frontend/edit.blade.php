@extends('layouts.frontend.master')

@section('title', 'تعديل الإعلان')
@section('body-class', 'p-new-listing page-new-listing')

@push('styles')

<link rel="stylesheet" href="{{ asset('frontend/css/pages/new-listing.css') }}">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<link rel="stylesheet" href="{{ asset('frontend/css/pages/listing_select.css') }}">

@endpush

<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .existing-images {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }
    
    .existing-image-item {
        position: relative;
        width: 100px;
        height: 100px;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .existing-image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .existing-image-remove {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .existing-image-remove:hover {
        background: #DC2626;
        transform: scale(1.1);
    }
</style>

@section('content')
<!-- ========== HERO ========== -->
<section class="p-new-listing__hero">
    <div class="l-container">
        <div class="p-new-listing__hero-content">
            <span class="p-new-listing__badge">
                <svg class="p-new-listing__badge-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                تعديل الإعلان
            </span>
            <h1 class="p-new-listing__title">تعديل إعلانك</h1>
            <p class="p-new-listing__subtitle">قم بتعديل بيانات إعلانك وسيتم مراجعته مرة أخرى</p>
        </div>
    </div>
</section>

<!-- ========== MAIN ========== -->
<main class="p-new-listing__main">
    <div class="l-container">
        <form action="{{ route('frontend.posts.update', $post->uuid) }}" method="POST" enctype="multipart/form-data"
            class="p-new-listing__card" id="editFormCard">
            @csrf
            @method('PUT')

            <div class="p-new-listing__step p-new-listing__step--active" id="editStep">
                <!-- Property Type -->
                <div class="p-new-listing__step-header">
                    <div class="p-new-listing__step-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                    </div>
                    <h2 class="p-new-listing__step-title">{{ __('frontend.new_listing.select_type') }}</h2>
                </div>

                <div class="p-new-listing__type-grid">
                    @foreach ($categories as $category)
                    <div class="p-new-listing__type-card">
                        <input type="radio" name="category_id" value="{{ $category->id }}" id="type{{ $category->id }}"
                            class="p-new-listing__type-input" @if ($post->category_id == $category->id) checked @endif>
                        <label for="type{{ $category->id }}" class="p-new-listing__type-label">
                            <div class="p-new-listing__type-icon">
                                @if ($category->icon)
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
                            id="purpose{{ $postType->id }}" class="p-new-listing__purpose-input" 
                            @if ($post->post_type_id == $postType->id) checked @endif>
                        <label for="purpose{{ $postType->id }}" class="p-new-listing__purpose-label">
                            <span class="p-new-listing__purpose-emoji">{{ $postType->icon ?? '🏠' }}</span>
                            <span class="p-new-listing__purpose-text">{{ $postType->name }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>

                <hr style="margin: 24px 0; border: none; border-top: 1px solid #E5E7EB;">

                <!-- Details Section -->
                <div class="p-new-listing__fields">
                    <!-- Title -->
                    <div class="p-new-listing__field">
                        <label class="p-new-listing__label">{{ __('post::post.title') }}</label>
                        <input type="text" name="title" class="p-new-listing__input" required
                            value="{{ old('title', $post->title) }}">
                        @error('title')
                        <span class="p-new-listing__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div class="p-new-listing__field">
                        <label class="p-new-listing__label">المحافظات</label>
                        <select name="state_id" class="p-new-listing__select" required>
                            <option value=""> اختر المحافظة </option>
                            @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected(old('state_id', $post->state_id) == $location->id)>
                                {{ $location->name }}</option>
                            @endforeach
                        </select>
                        @error('state_id')
                        <span class="p-new-listing__error">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- City -->
                    <div class="p-new-listing__field">
                        <label class="p-new-listing__label"> المناطق </label>
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
                            placeholder="{{ __('frontend.new_listing.price_placeholder') }}" 
                            value="{{ old('price', $post->price) }}">
                        @error('price')
                        <span class="p-new-listing__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="p-new-listing__field">
                        <label class="p-new-listing__label">{{ __('frontend.new_listing.description') }}</label>
                        <textarea name="description" class="p-new-listing__textarea"
                            placeholder="{{ __('frontend.new_listing.description_placeholder') }}" maxlength="2000"
                            id="descTextarea" required>{{ old('description', $post->description) }}</textarea>
                        <div class="p-new-listing__counter"><span id="charCount">{{ strlen(old('description', $post->description)) }}</span> / 2000</div>
                        @error('description')
                        <span class="p-new-listing__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Existing Images -->
                    @if($post->attachments->count() > 0)
                    <div class="p-new-listing__field">
                        <label class="p-new-listing__label">الصور الحالية</label>
                        <div class="existing-images" id="existingImages">
                            @foreach($post->attachments as $attachment)
                            <div class="existing-image-item" data-id="{{ $attachment->id }}">
                                <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="صورة">
                                <div class="existing-image-remove" onclick="removeExistingImage({{ $attachment->id }}, this)">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- New Images -->
                    <div class="p-new-listing__field">
                        <label class="p-new-listing__label">{{ __('frontend.new_listing.images_optional') }} (إضافة صور جديدة)</label>
                        <label for="imageUpload" class="p-new-listing__upload">
                            <div class="p-new-listing__upload-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" />
                                    <circle cx="8.5" cy="8.5" r="1.5" />
                                    <path d="m21 15-5-5L5 21" />
                                </svg>
                            </div>
                            <div class="p-new-listing__upload-title">{{ __('frontend.new_listing.images_hint') }}</div>
                            <div class="p-new-listing__upload-hint">{{ __('frontend.new_listing.images_format') }} (Max 10 images)</div>
                            <input type="file" name="images[]" class="p-new-listing__upload-input" id="imageUpload"
                                accept="image/png, image/jpeg, image/jpg" multiple>
                        </label>
                        <div id="imagePreview" class="p-new-listing__image-preview"></div>
                    </div>

                    <hr style="margin: 24px 0; border: none; border-top: 1px solid #E5E7EB;">

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
                                inputmode="numeric" required 
                                value="{{ str_replace('+965', '', old('mobile_number', $post->mobile_number)) }}">
                        </div>
                        @error('mobile_number')
                        <span class="p-new-listing__error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="p-new-listing__nav" style="margin-top: 24px;">
                    <a href="{{ route('frontend.account.dashboard') }}" class="p-new-listing__btn p-new-listing__btn--back">
                        <svg class="p-new-listing__btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                        إلغاء
                    </a>
                    <button type="button" onclick="submitEditForm()" class="p-new-listing__btn p-new-listing__btn--submit">
                        <svg class="p-new-listing__btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        حفظ التعديلات
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

        // Trigger change for initial load (edit mode)
        if (stateSelect.val()) {
            const initialCityId = "{{ old('city_id', $post->city_id) }}";
            const stateId = stateSelect.val();
            
            // Manually populate city options first because trigger('change') is async-ish or might be cleared
             if (stateId) {
                const selectedState = locationsData.find(state => state.id == stateId);
                if (selectedState && selectedState.cities) {
                    citySelect.empty().append('<option value="">اختر المحافظة</option>');
                    selectedState.cities.forEach(city => {
                        const option = new Option(city.name_ar, city.id, false, false);
                        citySelect.append(option);
                    });
                    citySelect.prop('disabled', false);
                    
                    if (initialCityId) {
                        citySelect.val(initialCityId).trigger('change');
                    }
                }
            }
        }
    });
</script>

<script>
    // Remove existing image via AJAX
    async function removeExistingImage(attachmentId, element) {
        const result = await Swal.fire({
            title: 'هل تريد حذف هذه الصورة؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/posts/{{ $post->id }}/attachments/${attachmentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    element.closest('.existing-image-item').remove();
                    Swal.fire({
                        title: 'تم الحذف!',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء حذف الصورة',
                        icon: 'error'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    }

    // Submit edit form via AJAX
    async function submitEditForm() {
        const form = document.getElementById('editFormCard');
        
        // Client-side validation
        if (!form.checkValidity()) {
            const invalidInput = form.querySelector(':invalid');
            if (invalidInput) {
                invalidInput.focus();
                invalidInput.reportValidity();
            }
            return;
        }

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

            if (response.ok && data.success) {
                Swal.fire({
                    title: 'تم بنجاح!',
                    text: data.message || 'تم تحديث الإعلان بنجاح',
                    icon: 'success',
                    confirmButtonColor: '#10B981'
                }).then(() => {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.href = '{{ route("frontend.account.dashboard") }}';
                    }
                });
            } else {
                if (response.status === 422) {
                    const errors = data.errors;
                    for (const [key, messages] of Object.entries(errors)) {
                        let input = form.querySelector(`[name="${key}"]`);
                        
                        if (input) {
                            const errorSpan = document.createElement('span');
                            errorSpan.className = 'p-new-listing__error';
                            errorSpan.textContent = messages[0];

                            const field = input.closest('.p-new-listing__field') || input.parentElement;
                            if (field) {
                                field.appendChild(errorSpan);
                            }
                        }
                    }
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: data.message || 'حدث خطأ ما، يرجى المحاولة مرة أخرى.',
                        icon: 'error',
                        confirmButtonColor: '#EF4444'
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'خطأ!',
                text: 'حدث خطأ في الاتصال، يرجى المحاولة مرة أخرى.',
                icon: 'error',
                confirmButtonColor: '#EF4444'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnContent;
        }
    }

    // Character counter
    const textarea = document.getElementById('descTextarea');
    const counter = document.getElementById('charCount');
    if (textarea && counter) {
        textarea.addEventListener('input', function() {
            counter.textContent = this.value.length;
        });
    }

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
                const slotsLeft = MAX_IMAGES - uploadedFiles.items.length;
                if (slotsLeft > 0) {
                    newFiles.slice(0, slotsLeft).forEach(file => uploadedFiles.items.add(file));
                    hintText.innerText = `Added first ${slotsLeft} images. Limit reached.`;
                }
            } else {
                hintText.classList.remove('error');
                hintText.innerText = `{{ __('frontend.new_listing.images_format') }} (Max 10 images)`;

                newFiles.forEach(file => {
                    let isDuplicate = false;
                    for (let i = 0; i < uploadedFiles.items.length; i++) {
                        const f = uploadedFiles.items[i].getAsFile();
                        if (f.name === file.name && f.size === file.size) isDuplicate = true;
                    }
                    if (!isDuplicate) uploadedFiles.items.add(file);
                });
            }

            fileInput.files = uploadedFiles.files;
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

        if (uploadedFiles.items.length < MAX_IMAGES) {
            hintText.classList.remove('error');
            hintText.innerText = `{{ __('frontend.new_listing.images_format') }} (Max 10 images)`;
        }
    }
</script>
@endpush
