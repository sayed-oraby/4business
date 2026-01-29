<div class="modal fade" id="bannerFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h2 class="fw-bold mb-1" data-modal-title>{{ __('banner::banner.actions.create') }}</h2>
                    <span class="text-muted fs-7">{{ __('banner::banner.description') }}</span>
                </div>
                <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </button>
            </div>
            <form id="bannerForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="banner_id">
                <div class="modal-body pt-0">
                    <div class="alert alert-danger d-none" data-banner-errors></div>
                    <div class="row g-5">
                        <div class="col-12">
                            <label class="form-label fw-bold mb-2">{{ __('banner::banner.form.title_group') }}</label>
                            <ul class="nav nav-tabs nav-line-tabs mb-3 fs-6">
                                @foreach($availableLocales as $code => $locale)
                                    <li class="nav-item">
                                        <a class="nav-link @if ($loop->first) active @endif" data-banner-locale-tab data-bs-toggle="tab" href="#banner_locale_{{ $code }}">
                                            {{ $locale['native'] ?? strtoupper($code) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($availableLocales as $code => $locale)
                                    <div class="tab-pane fade @if ($loop->first) show active @endif" id="banner_locale_{{ $code }}">
                                        <div class="mb-4">
                                            <label class="form-label @if($loop->first) required @endif">
                                                {{ __('banner::banner.form.title_' . $code) }}
                                            </label>
                                            <input type="text" class="form-control form-control-solid" name="title[{{ $code }}]">
                                            <div class="invalid-feedback" data-error-for="title.{{ $code }}"></div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">{{ __('banner::banner.form.description_' . $code) }}</label>
                                            <textarea class="form-control form-control-solid" rows="3" name="description[{{ $code }}]"></textarea>
                                            <div class="invalid-feedback" data-error-for="description.{{ $code }}"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('banner::banner.form.button_label') }}</label>
                            <input type="text" class="form-control form-control-solid" name="button_label">
                            <div class="invalid-feedback" data-error-for="button_label"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('banner::banner.form.button_url') }}</label>
                            <input type="url" class="form-control form-control-solid" name="button_url">
                            <div class="invalid-feedback" data-error-for="button_url"></div>
                        </div>

                        {{-- <div class="col-md-6">
                            <label class="form-label required">{{ __('banner::banner.form.placement') }}</label>
                            <select class="form-select form-select-solid" name="placement" data-control="select2" data-placeholder="{{ __('banner::banner.form.placement') }}">
                                @foreach($placements as $key => $placement)
                                    <option value="{{ $key }}">{{ __($placement['label']) }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-error-for="placement"></div>
                        </div> --}}

                        <div class="col-md-6">
                            <label class="form-label required">{{ __('banner::banner.form.status') }}</label>
                            <select class="form-select form-select-solid" name="status">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}">{{ __('banner::banner.statuses.' . $status) }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-error-for="status"></div>
                        </div>

                        {{-- <div class="col-md-6">
                            <label class="form-label">{{ __('banner::banner.form.starts_at') }}</label>
                            <input type="datetime-local" class="form-control form-control-solid" name="starts_at">
                            <div class="invalid-feedback" data-error-for="starts_at"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('banner::banner.form.ends_at') }}</label>
                            <input type="datetime-local" class="form-control form-control-solid" name="ends_at">
                            <div class="invalid-feedback" data-error-for="ends_at"></div>
                        </div> --}}

                        {{-- <div class="col-md-6">
                            <label class="form-label">{{ __('banner::banner.form.sort_order') }}</label>
                            <input type="number" class="form-control form-control-solid" name="sort_order" min="0" max="1000">
                            <div class="invalid-feedback" data-error-for="sort_order"></div>
                        </div> --}}
                        
                        <div class="col-md-6">
                            <label class="form-label">{{ __('banner::banner.form.image') }}</label>
                            <input type="file" class="form-control form-control-solid" name="image" accept="image/*">
                            <div class="invalid-feedback" data-error-for="image"></div>
                            <div class="mt-3 d-none" id="banner-image-preview-container">
                                <label class="form-label d-block text-muted fs-7 mb-2">{{ __('banner::banner.form.current_image') }}</label>
                                <img src="" alt="Banner Image" class="rounded border shadow-sm" style="max-height: 150px; max-width: 100%; object-fit: contain;" id="banner-image-preview">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('banner::banner.actions.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" data-banner-action="submit-form" data-kt-indicator="off">
                        <span class="indicator-label">{{ __('banner::banner.form.save') }}</span>
                        <span class="indicator-progress">{{ __('banner::banner.form.save') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
