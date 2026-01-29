<div class="modal fade" id="brandFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h2 class="fw-bold mb-1" data-modal-title>{{ __('brand::brand.actions.create') }}</h2>
                    <span class="text-muted fs-7">{{ __('brand::brand.description') }}</span>
                </div>
                <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </button>
            </div>
            <form id="brandForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="brand_id">
                <div class="modal-body pt-0">
                    <div class="alert alert-danger d-none" data-brand-errors></div>

                    <div class="row g-5">
                        <div class="col-12">
                            <label class="form-label fw-bold mb-2">{{ __('brand::brand.form.localization') }}</label>
                            <ul class="nav nav-tabs nav-line-tabs mb-3 fs-6">
                                @foreach($availableLocales as $code => $locale)
                                    <li class="nav-item">
                                        <a class="nav-link @if($loop->first) active @endif"
                                           data-brand-locale-tab
                                           data-locale="{{ $code }}"
                                           data-bs-toggle="tab"
                                           href="#brand_locale_{{ $code }}">
                                            {{ $locale['native'] ?? strtoupper($code) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($availableLocales as $code => $locale)
                                    <div class="tab-pane fade @if($loop->first) show active @endif" id="brand_locale_{{ $code }}">
                                        <div class="mb-4">
                                            <label class="form-label @if($loop->first) required @endif">{{ __('brand::brand.form.title_' . $code) }}</label>
                                            <input type="text" class="form-control form-control-solid" name="title[{{ $code }}]">
                                            <div class="invalid-feedback" data-error-for="title.{{ $code }}"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">{{ __('brand::brand.form.status') }}</label>
                            <select class="form-select form-select-solid" name="status">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}">{{ __('brand::brand.statuses.' . $status) }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-error-for="status"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('brand::brand.form.position') }}</label>
                            <input type="number" class="form-control form-control-solid" name="position" min="0" max="9999">
                            <div class="invalid-feedback" data-error-for="position"></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('brand::brand.form.image') }}</label>
                            <input type="file" class="form-control form-control-solid" name="image" accept="image/*">
                            <div class="form-text">{{ __('brand::brand.form.image_help') }}</div>
                            <div class="invalid-feedback" data-error-for="image"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('brand::brand.actions.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" data-brand-action="submit-form" data-kt-indicator="off">
                        <span class="indicator-label">{{ __('brand::brand.form.save') }}</span>
                        <span class="indicator-progress">{{ __('brand::brand.form.save') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
