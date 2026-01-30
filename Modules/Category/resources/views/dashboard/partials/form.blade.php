<div class="modal fade" id="categoryFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h2 class="fw-bold mb-1" data-modal-title data-title-create="{{ __('category::category.actions.create') }}" data-title-edit="{{ __('category::category.actions.edit') }}">{{ __('category::category.actions.create') }}</h2>
                    <span class="text-muted fs-7">{{ __('category::category.description') }}</span>
                </div>
                <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </button>
            </div>
            <form id="categoryForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="category_id">
                <div class="modal-body pt-0">
                    <div class="alert alert-danger d-none" data-category-errors></div>

                    <div class="row g-7">
                        <div class="col-12">
                            <label class="form-label fw-bold mb-2">{{ __('category::category.form.localization') }}</label>
                            <ul class="nav nav-tabs nav-line-tabs mb-3 fs-6">
                                @foreach($availableLocales as $code => $locale)
                                    <li class="nav-item">
                                        <a class="nav-link @if($loop->first) active @endif"
                                           data-category-locale-tab
                                           data-locale="{{ $code }}"
                                           data-bs-toggle="tab"
                                           href="#category_locale_{{ $code }}">
                                            {{ $locale['native'] ?? strtoupper($code) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($availableLocales as $code => $locale)
                                    <div class="tab-pane fade @if($loop->first) show active @endif" id="category_locale_{{ $code }}">
                                        <div class="mb-4">
                                            <label class="form-label @if($loop->first) required @endif">{{ __('category::category.form.title_' . $code) }}</label>
                                            <input type="text" class="form-control form-control-solid" name="title[{{ $code }}]" placeholder="{{ __('category::category.form.title_' . $code) }}">
                                            <div class="invalid-feedback" data-error-for="title.{{ $code }}"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">{{ __('category::category.form.status') }}</label>
                            <select class="form-select form-select-solid" name="status">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}">{{ __('category::category.statuses.' . $status) }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-error-for="status"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('category::category.form.parent') }}</label>
                            <select class="form-select form-select-solid" name="parent_id" data-category-parent data-control="select2" data-placeholder="{{ __('category::category.form.parent_placeholder') }}"></select>
                            <div class="invalid-feedback" data-error-for="parent_id"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('category::category.form.image') }}</label>
                            <div class="mb-3 d-none" data-category-image-preview-wrapper>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="" alt="Current Image" class="rounded" style="width: 80px; height: 80px; object-fit: cover;" data-category-image-preview>
                                    <span class="text-muted fs-7">{{ __('category::category.form.current_image') }}</span>
                                </div>
                            </div>
                            <input type="file" class="form-control form-control-solid" name="image" accept="image/*">
                            <div class="form-text">{{ __('category::category.form.image_help') }}</div>
                            <div class="invalid-feedback" data-error-for="image"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('category::category.form.position') }}</label>
                            <input type="number" class="form-control form-control-solid" name="position" min="0" max="9999" placeholder="10">
                            <div class="invalid-feedback" data-error-for="position"></div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid mt-4">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="categoryFeaturedSwitch">
                                <label class="form-check-label" for="categoryFeaturedSwitch">{{ __('category::category.form.is_featured') }}</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('category::category.form.featured_order') }}</label>
                            <input type="number" class="form-control form-control-solid" name="featured_order" min="0" max="9999" placeholder="0">
                            <div class="invalid-feedback" data-error-for="featured_order"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('category::category.actions.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" data-category-action="submit-form" data-kt-indicator="off">
                        <span class="indicator-label">{{ __('category::category.form.save') }}</span>
                        <span class="indicator-progress">{{ __('category::category.form.save') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
