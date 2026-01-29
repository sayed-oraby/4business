<div class="modal fade" id="typeFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h2 class="fw-bold mb-1" id="typeFormModalTitle">{{ __('post::post.types.form.create_title') }}</h2>
                    <span class="text-muted fs-7">{{ __('post::post.types.description') }}</span>
                </div>
                <div class="btn btn-sm btn-icon btn-light" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="typeForm" class="form" action="#" enctype="multipart/form-data">
                    <input type="hidden" name="id">
                    
                    <!-- Image Upload -->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">{{ __('post::post.types.form.image') }}</label>
                        <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url('{{ asset('metronic/media/svg/files/folder-document.svg') }}')">
                            <div class="image-input-wrapper w-125px h-125px" id="typeImagePreview" style="background-image: url('{{ asset('metronic/media/svg/files/folder-document.svg') }}')"></div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="{{ __('post::post.types.form.change_image') }}">
                                <i class="ki-duotone ki-pencil fs-7"><span class="path1"></span><span class="path2"></span></i>
                                <input type="file" name="image" accept=".png, .jpg, .jpeg, .webp" />
                                <input type="hidden" name="image_remove" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="{{ __('post::post.types.form.cancel') }}">
                                <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                            </span>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="{{ __('post::post.types.form.remove_image') }}">
                                <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                            </span>
                        </div>
                        <div class="form-text">{{ __('post::post.types.form.image_help') }}</div>
                    </div>

                    <div class="d-flex flex-column mb-8">
                        <label class="form-label fw-bold mb-2">{{ __('category::category.form.localization') }}</label>
                        <ul class="nav nav-tabs nav-line-tabs mb-3 fs-6">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#type_locale_en">English</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#type_locale_ar">Arabic</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="type_locale_en">
                                <div class="fv-row">
                                    <label class="form-label required">{{ __('post::post.types.form.name_en') }}</label>
                                    <input type="text" class="form-control form-control-solid" placeholder="{{ __('post::post.types.form.name_en') }}" name="name[en]" />
                                </div>
                            </div>
                            <div class="tab-pane fade" id="type_locale_ar">
                                <div class="fv-row">
                                    <label class="form-label required">{{ __('post::post.types.form.name_ar') }}</label>
                                    <input type="text" class="form-control form-control-solid" placeholder="{{ __('post::post.types.form.name_ar') }}" name="name[ar]" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">{{ __('post::post.types.form.slug') }}</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Auto-generated" name="slug" readonly />
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">{{ __('post::post.types.form.status') }}</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="1" name="status" checked="checked" />
                            <label class="form-check-label fw-semibold text-gray-400 ms-3">{{ __('post::post.types.form.active') }}</label>
                        </div>
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('post::post.types.form.discard') }}</button>
                        <button type="submit" class="btn btn-primary" id="typeFormSubmit">
                            <span class="indicator-label">{{ __('post::post.types.form.save') }}</span>
                            <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
