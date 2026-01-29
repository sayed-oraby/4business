@extends('layouts.dashboard.master')

@section('title', __('post::post.posts.edit_title'))
@section('page-title', __('post::post.posts.edit_title'))

@section('content')
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
            data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">{{ __('post::post.posts.edit_title') }}</h3>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('dashboard.posts.show', $post->id) }}" class="btn btn-sm btn-light">
                    {{ __('post::post.actions.cancel') }}
                </a>
            </div>
        </div>
        <div id="kt_account_settings_profile_details" class="collapse show">
            <form action="{{ route('dashboard.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data"
                class="form">
                @csrf
                @method('PUT')
                <div class="card-body border-top p-9">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Tabs -->
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold mb-10">
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 active" data-bs-toggle="tab"
                                href="#tab_main_info">{{ __('post::post.posts.tabs.main_info') }}</a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10" data-bs-toggle="tab"
                                href="#tab_personal_details">{{ __('post::post.posts.tabs.personal_details') }}</a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10" data-bs-toggle="tab"
                                href="#tab_attachments">{{ __('post::post.posts.tabs.attachments') }}</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Main Info Tab -->
                        <div class="tab-pane fade show active" id="tab_main_info" role="tabpanel">
                            <!-- Localization -->
                            <div class="row mb-6">
                                <label
                                    class="col-lg-4 col-form-label required fw-semibold fs-6">{{ __('category::category.form.localization') }}</label>
                                <div class="col-lg-8">
                                    <ul class="nav nav-tabs nav-line-tabs mb-3 fs-6">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab"
                                                href="#post_locale_en">{{ __('post::post.posts.tabs.english') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab"
                                                href="#post_locale_ar">{{ __('post::post.posts.tabs.arabic') }}</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="post_locale_en">
                                            <div class="fv-row mb-7">
                                                <label
                                                    class="required form-label">{{ __('post::post.posts.form.title_en') }}</label>
                                                <input type="text" name="title[en]"
                                                    class="form-control form-control-lg form-control-solid"
                                                    value="{{ old('title.en', $post->getTranslation('title', 'en')) }}"
                                                    required />
                                            </div>
                                            <div class="fv-row mb-7">
                                                <label
                                                    class="required form-label">{{ __('post::post.posts.form.description_en') }}</label>
                                                <textarea name="description[en]" class="form-control form-control-lg form-control-solid" rows="5" required>{{ old('description.en', $post->getTranslation('description', 'en')) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="post_locale_ar">
                                            <div class="fv-row mb-7">
                                                <label
                                                    class="required form-label">{{ __('post::post.posts.form.title_ar') }}</label>
                                                <input type="text" name="title[ar]"
                                                    class="form-control form-control-lg form-control-solid"
                                                    value="{{ old('title.ar', $post->getTranslation('title', 'ar')) }}"
                                                    required />
                                            </div>
                                            <div class="fv-row mb-7">
                                                <label
                                                    class="required form-label">{{ __('post::post.posts.form.description_ar') }}</label>
                                                <textarea name="description[ar]" class="form-control form-control-lg form-control-solid" rows="5" required>{{ old('description.ar', $post->getTranslation('description', 'ar')) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Category & Type -->
                            <div class="row mb-6">
                                <label
                                    class="col-lg-4 col-form-label required fw-semibold fs-6">{{ __('post::post.posts.form.category') }}
                                    & {{ __('post::post.posts.form.type') }}</label>
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-6 fv-row">
                                            <select name="category_id"
                                                class="form-select form-select-solid form-select-lg fw-semibold"
                                                data-control="select2" required>
                                                <option value="">
                                                    {{ __('post::post.posts.placeholders.select_category') }}
                                                </option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $post->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6 fv-row">
                                            <select name="post_type_id"
                                                class="form-select form-select-solid form-select-lg fw-semibold"
                                                data-control="select2" required>
                                                <option value="">
                                                    {{ __('post::post.posts.placeholders.select_type') }}
                                                </option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ $post->post_type_id == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Package & City -->
                            <div class="row mb-6">
                                <label
                                    class="col-lg-4 col-form-label required fw-semibold fs-6">{{ __('post::post.posts.form.package') }}
                                    & {{ __('post::post.posts.form.city') }}</label>
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-6 fv-row">
                                            <select name="package_id"
                                                class="form-select form-select-solid form-select-lg fw-semibold"
                                                data-control="select2" required>
                                                <option value="">
                                                    {{ __('post::post.posts.placeholders.select_package') }}
                                                </option>
                                                @foreach ($packages as $package)
                                                    <option value="{{ $package->id }}"
                                                        {{ $post->package_id == $package->id ? 'selected' : '' }}>
                                                        {{ $package->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6 fv-row">
                                            <select name="state_id"
                                                class="form-select form-select-solid form-select-lg fw-semibold"
                                                data-control="select2">
                                                <option value="">
                                                    {{ __('post::post.posts.placeholders.select_state') }}
                                                </option>
                                                @foreach ($states as $state)
                                                    <option value="{{ $state->id }}"
                                                        {{ $post->state_id == $state->id ? 'selected' : '' }}>
                                                        {{ $state->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Experience & Status -->
                            <div class="row mb-6">
                                <label
                                    class="col-lg-4 col-form-label fw-semibold fs-6">{{ __('post::post.posts.form.price') }}
                                    & {{ __('post::post.posts.form.status') }}</label>
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-6 fv-row">
                                            <input type="number" name="price"
                                                class="form-control form-control-lg form-control-solid"
                                                value="{{ old('price', $post->price) }}" min="0" />
                                        </div>
                                        <div class="col-lg-6 fv-row">
                                            <select name="status"
                                                class="form-select form-select-solid form-select-lg fw-semibold" required>
                                                <option value="pending"
                                                    {{ $post->status == 'pending' ? 'selected' : '' }}>
                                                    {{ __('post::post.statuses.pending') }}</option>
                                                <option value="approved"
                                                    {{ $post->status == 'approved' ? 'selected' : '' }}>
                                                    {{ __('post::post.statuses.approved') }}</option>
                                                <option value="rejected"
                                                    {{ $post->status == 'rejected' ? 'selected' : '' }}>
                                                    {{ __('post::post.statuses.rejected') }}</option>
                                                <option value="expired"
                                                    {{ $post->status == 'expired' ? 'selected' : '' }}>
                                                    {{ __('post::post.statuses.expired') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <!-- Cover Image -->
                            <div class="row mb-6">
                                <label
                                    class="col-lg-4 col-form-label fw-semibold fs-6">{{ __('post::post.posts.form.cover_image') }}</label>
                                <div class="col-lg-8">
                                    <div class="image-input image-input-outline" data-kt-image-input="true"
                                        style="background-image: url('{{ asset('metronic/media/svg/avatars/blank.svg') }}')">
                                        <div class="image-input-wrapper w-125px h-125px"
                                            style="background-image: url('{{ $post->cover_image ? asset('storage/' . $post->cover_image) : asset('metronic/media/svg/avatars/blank.svg') }}')">
                                        </div>
                                        <label
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                            title="Change avatar">
                                            <i class="ki-duotone ki-pencil fs-7"><span class="path1"></span><span
                                                    class="path2"></span></i>
                                            <input type="file" name="cover_image" accept=".png, .jpg, .jpeg" />
                                            <input type="hidden" name="avatar_remove" />
                                        </label>
                                        <span
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                            title="Cancel avatar">
                                            <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span
                                                    class="path2"></span></i>
                                        </span>
                                        <span
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                            title="Remove avatar">
                                            <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span
                                                    class="path2"></span></i>
                                        </span>
                                    </div>
                                    <div class="form-text">{{ __('post::post.posts.messages.allowed_file_types') }}</div>
                                </div>
                            </div>

                            <!-- Paid Status -->
                            <div class="row mb-6">
                                <label
                                    class="col-lg-4 col-form-label fw-semibold fs-6">{{ __('post::post.posts.form.is_paid') }}</label>
                                <div class="col-lg-8 d-flex align-items-center">
                                    <div class="form-check form-check-solid form-switch fv-row">
                                        <input class="form-check-input w-45px h-30px" type="checkbox" id="is_paid"
                                            name="is_paid" value="1" {{ $post->is_paid ? 'checked' : '' }} />
                                        <label class="form-check-label" for="is_paid"></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Details Tab -->
                        <div class="tab-pane fade" id="tab_personal_details" role="tabpanel">
                            {{-- <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">{{
                                __('post::post.posts.form.full_name') }}</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="full_name"
                                    class="form-control form-control-lg form-control-solid"
                                    value="{{ old('full_name', $post->full_name) }}" required />
                            </div>
                        </div> --}}
                            <div class="row mb-6">
                                <label
                                    class="col-lg-4 col-form-label required fw-semibold fs-6">{{ __('post::post.posts.form.mobile') }}</label>
                                <div class="col-lg-8 fv-row">
                                    <input type="text" name="mobile_number"
                                        class="form-control form-control-lg form-control-solid"
                                        value="{{ old('mobile_number', $post->mobile_number) }}" required />
                                </div>
                            </div>
                            {{-- <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">{{
                                __('post::post.posts.form.gender') }}</label>
                            <div class="col-lg-8 fv-row">
                                <select name="gender" class="form-select form-select-solid form-select-lg fw-semibold"
                                    required>
                                    <option value="male" {{ $post->gender == 'male' ? 'selected' : '' }}>{{
                                        __('post::post.posts.gender_male') }}</option>
                                    <option value="female" {{ $post->gender == 'female' ? 'selected' : '' }}>{{
                                        __('post::post.posts.gender_female') }}</option>
                                    <option value="both" {{ $post->gender == 'both' ? 'selected' : '' }}>{{
                                        __('post::post.posts.gender_both') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">{{
                                __('post::post.posts.form.birthdate') }}</label>
                            <div class="col-lg-8 fv-row">
                                <input type="date" name="birthdate"
                                    class="form-control form-control-lg form-control-solid"
                                    value="{{ $post->birthdate ? $post->birthdate->format('Y-m-d') : '' }}" />
                            </div>
                        </div> --}}
                            <div class="row mb-6">
                                <label
                                    class="col-lg-4 col-form-label fw-semibold fs-6">{{ __('post::post.posts.form.display_personal_details') }}</label>
                                <div class="col-lg-8 d-flex align-items-center">
                                    <div class="form-check form-check-solid form-switch fv-row">
                                        <input class="form-check-input w-45px h-30px" type="checkbox" value="1"
                                            id="display_personal_details" name="display_personal_details"
                                            {{ $post->display_personal_details ? 'checked' : '' }} />
                                        <label class="form-check-label" for="display_personal_details"></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attachments Tab -->
                        <div class="tab-pane fade" id="tab_attachments" role="tabpanel">
                            <!-- Existing Attachments -->
                            <div class="mb-10">
                                <h3 class="fw-bold mb-5">{{ __('post::post.posts.headers.existing_attachments') }}</h3>
                                @if ($post->attachments->count() > 0)
                                    <div class="row g-5">
                                        @foreach ($post->attachments as $attachment)
                                            <div class="col-md-4 col-lg-3" id="attachment-{{ $attachment->id }}">
                                                <div class="card card-bordered h-100">
                                                    <div
                                                        class="card-body d-flex flex-column align-items-center justify-content-center text-center p-5">
                                                        <i class="ki-duotone ki-file fs-3x text-primary mb-3"><span
                                                                class="path1"></span><span class="path2"></span></i>
                                                        <div class="text-gray-800 fw-bold fs-7 text-truncate w-100 mb-3">
                                                            {{ $attachment->title ?? basename($attachment->file_path) }}
                                                        </div>
                                                        <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-light-primary mb-2 w-100">View</a>
                                                        <button type="button" class="btn btn-sm btn-light-danger w-100"
                                                            onclick="markAttachmentForDeletion({{ $attachment->id }})">{{ __('post::post.posts.buttons.delete') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div id="deleted-attachments-container"></div>
                                @else
                                    <div
                                        class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <div class="fw-semibold">
                                                <h4 class="text-gray-900 fw-bold">
                                                    {{ __('post::post.posts.messages.no_attachments') }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Upload New Attachments -->
                            <div>
                                <h3 class="fw-bold mb-5">{{ __('post::post.posts.headers.upload_new_attachments') }}</h3>
                                <div id="kt_repeater_1">
                                    <div class="form-group row">
                                        <div data-repeater-list="attachments" class="col-lg-12">
                                            <div data-repeater-item class="form-group row align-items-center mb-5">
                                                <div class="col-md-5">
                                                    <input type="text" name="attachment_title"
                                                        class="form-control form-control-solid"
                                                        placeholder="{{ __('post::post.posts.placeholders.attachment_title') }}" />
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="file" name="file" accept=".png, .jpg, .jpeg"
                                                        class="form-control form-control-solid" />
                                                </div>
                                                <div class="col-md-2">
                                                    <a href="javascript:;" data-repeater-delete
                                                        class="btn btn-sm btn-light-danger">
                                                        <i class="ki-duotone ki-trash fs-5"><span
                                                                class="path1"></span><span class="path2"></span><span
                                                                class="path3"></span><span class="path4"></span><span
                                                                class="path5"></span></i>
                                                        {{ __('post::post.posts.buttons.delete') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mt-5">
                                        <a href="javascript:;" data-repeater-create class="btn btn-light-primary">
                                            <i class="ki-duotone ki-plus fs-3"></i>
                                            {{ __('post::post.posts.buttons.add_attachment') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <button type="reset"
                        class="btn btn-light btn-active-light-primary me-2">{{ __('post::post.actions.cancel') }}</button>
                    <button type="submit" class="btn btn-primary"
                        id="kt_account_profile_details_submit">{{ __('post::post.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('metronic/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
    <script>
        $('#kt_repeater_1').repeater({
            initEmpty: false,
            defaultValues: {
                'text-input': 'foo'
            },
            show: function() {
                $(this).slideDown();
            },
            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            },
            isFirstItemUndeletable: true
        });

        function markAttachmentForDeletion(id) {
            Swal.fire({
                text: "Are you sure you want to delete this attachment?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function(result) {
                if (result.value) {
                    // Add hidden input to form
                    const container = document.getElementById('deleted-attachments-container');
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'deleted_attachments[]';
                    input.value = id;
                    container.appendChild(input);

                    // Hide the attachment card
                    document.getElementById('attachment-' + id).style.display = 'none';
                }
            });
        }

        // Handle Repeater File Inputs to use Title as Key
        document.querySelector('form').addEventListener('submit', function(e) {
            // This is a bit tricky with repeater. 
            // The repeater sends data as attachments[0][key] = value.
            // But our backend expects attachments[Title] = File.
            // Since we can't easily change the backend logic without breaking API compatibility or making it complex,
            // let's adjust the backend to handle the repeater format if possible, OR
            // we can use a workaround here.

            // Actually, the backend code I wrote:
            // foreach ($request->file('attachments') as $title => $file)
            // This expects keys to be titles.

            // With repeater, the input names are like attachments[0][file] and attachments[0][title_input].
            // We need to intercept the submission and restructure this, OR
            // simpler: update the backend to handle array of objects structure which is cleaner.

            // Let's update the backend to be more flexible. I will update PostController again to handle both formats.
        });
    </script>
@endpush
