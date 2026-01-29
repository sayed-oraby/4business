<div class="modal fade" id="packageFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h2 class="fw-bold mb-1" id="packageFormModalTitle">{{ __('post::post.packages.form.create_title') }}</h2>
                    <span class="text-muted fs-7">{{ __('post::post.packages.description') }}</span>
                </div>
                <div class="btn btn-sm btn-icon btn-light" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="packageForm" class="form" action="#" enctype="multipart/form-data">
                    <input type="hidden" name="id">
                    
                    <!-- Free Package Toggle -->
                    <div class="d-flex flex-column mb-8">
                        <div class="d-flex align-items-center gap-3 p-4 rounded bg-light-primary border border-primary border-dashed" id="freePackageSection">
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" name="is_free" id="isFreePackage" />
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label fw-bold mb-0 cursor-pointer" for="isFreePackage">{{ __('post::post.packages.form.is_free') }}</label>
                                <p class="text-muted fs-7 mb-0">{{ __('post::post.packages.form.is_free_help') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Free Credits (shown only when is_free is checked) -->
                    <div class="d-flex flex-column mb-8 fv-row" id="freeCreditsSection" style="display: none;">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">{{ __('post::post.packages.form.free_credits') }}</span>
                        </label>
                        <input type="number" class="form-control form-control-solid" placeholder="6" name="free_credits_per_user" min="1" />
                        <div class="form-text">{{ __('post::post.packages.form.free_credits_help') }}</div>
                    </div>

                    <div class="d-flex flex-column mb-8">
                        <label class="form-label fw-bold mb-2">{{ __('category::category.form.localization') }}</label>
                        <ul class="nav nav-tabs nav-line-tabs mb-3 fs-6">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#package_locale_en">English</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#package_locale_ar">Arabic</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="package_locale_en">
                                <div class="fv-row mb-8">
                                    <label class="form-label required">{{ __('post::post.packages.form.title_en') }}</label>
                                    <input type="text" class="form-control form-control-solid" placeholder="{{ __('post::post.packages.form.title_en') }}" name="title[en]" />
                                </div>
                                <div class="fv-row">
                                    <label class="form-label">{{ __('post::post.packages.form.description_en') }}</label>
                                    <textarea class="form-control form-control-solid" rows="3" name="description[en]" placeholder="{{ __('post::post.packages.form.description_en') }}"></textarea>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="package_locale_ar">
                                <div class="fv-row mb-8">
                                    <label class="form-label required">{{ __('post::post.packages.form.title_ar') }}</label>
                                    <input type="text" class="form-control form-control-solid" placeholder="{{ __('post::post.packages.form.title_ar') }}" name="title[ar]" />
                                </div>
                                <div class="fv-row">
                                    <label class="form-label">{{ __('post::post.packages.form.description_ar') }}</label>
                                    <textarea class="form-control form-control-solid" rows="3" name="description[ar]" placeholder="{{ __('post::post.packages.form.description_ar') }}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price (hidden when free) -->
                    <div class="row mb-8" id="priceSection">
                        <div class="col-md-12 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">{{ __('post::post.packages.form.price') }}</span>
                            </label>
                            <input type="number" class="form-control form-control-solid" placeholder="0.00" name="price" step="0.01" />
                        </div>
                    </div>

                    <!-- Duration Settings -->
                    <div class="row mb-8">
                        <div class="col-md-4 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">{{ __('post::post.packages.form.period') }}</span>
                            </label>
                            <input type="number" class="form-control form-control-solid" placeholder="40" name="period_days" id="periodDays" />
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>{{ __('post::post.packages.form.top_days') }}</span>
                            </label>
                            <input type="number" class="form-control form-control-solid" placeholder="10" name="top_days" id="topDays" />
                            <div class="form-text">{{ __('post::post.packages.form.top_days_help') }}</div>
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>{{ __('post::post.packages.form.free_days') }}</span>
                            </label>
                            <input type="number" class="form-control form-control-solid bg-light" placeholder="30" id="freeDays" readonly />
                        </div>
                    </div>

                    <!-- Color Pickers -->
                    <div class="row mb-8" id="colorSection">
                        <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">{{ __('post::post.packages.form.label_color') }}</label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="color" class="form-control form-control-color" name="label_color" value="#3b82f6" style="width: 60px; height: 40px;" />
                                <input type="text" class="form-control form-control-solid" id="labelColorText" value="#3b82f6" maxlength="7" style="width: 100px;" />
                            </div>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">{{ __('post::post.packages.form.card_color') }}</label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="color" class="form-control form-control-color" name="card_color" value="#eff6ff" style="width: 60px; height: 40px;" />
                                <input type="text" class="form-control form-control-solid" id="cardColorText" value="#eff6ff" maxlength="7" style="width: 100px;" />
                            </div>
                        </div>
                    </div>

                    <!-- Preview Card -->
                    <div class="mb-8">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">{{ __('post::post.packages.form.color') }} Preview</label>
                        <div class="p-4 rounded" id="packagePreview" style="background-color: #eff6ff; border: 2px solid #3b82f6;">
                            <span class="badge" id="previewLabel" style="background-color: #3b82f6; color: white;">VIP Package</span>
                            <p class="mb-0 mt-2 text-gray-700">This is how the package card will look</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">{{ __('post::post.packages.form.icon') }}</label>
                        <input type="file" class="form-control form-control-solid" name="cover_image" accept="image/*" />
                    </div>

                    <div class="row mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">{{ __('post::post.packages.form.status') }}</label>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" name="status" checked="checked" />
                                <label class="form-check-label fw-semibold text-gray-400 ms-3">{{ __('post::post.statuses.active') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">{{ __('post::post.packages.form.is_featured') }}</label>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" name="is_featured" />
                                <label class="form-check-label fw-semibold text-gray-400 ms-3">{{ __('post::post.packages.form.is_featured') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('post::post.actions.cancel') }}</button>
                        <button type="submit" class="btn btn-primary" id="packageFormSubmit">
                            <span class="indicator-label">{{ __('post::post.packages.form.save') }}</span>
                            <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
