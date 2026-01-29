<div class="modal fade" id="bannerViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('banner::banner.view.title') }}</h3>
                <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </button>
            </div>
            <div class="modal-body py-5 px-6">
                <div class="row g-5">
                    <div class="col-md-5">
                        <div class="border rounded py-3 px-3 d-flex align-items-center justify-content-center bg-light">
                            <img src="" alt="" class="img-fluid rounded" data-banner-view="image">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h4 class="fw-bold text-gray-900 mb-3" data-banner-view="title">—</h4>
                        <p class="text-muted mb-4" data-banner-view="description">—</p>
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <span class="text-muted fw-semibold">{{ __('banner::banner.view.fields.placement') }}:</span>
                                <span class="fw-bold ms-2" data-banner-view="placement">—</span>
                            </div>
                            <div>
                                <span class="text-muted fw-semibold">{{ __('banner::banner.view.fields.status') }}:</span>
                                <span class="badge badge-light-primary ms-2" data-banner-view="status">—</span>
                            </div>
                            <div>
                                <span class="text-muted fw-semibold">{{ __('banner::banner.view.fields.state') }}:</span>
                                <span class="badge badge-light ms-2" data-banner-view="state">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="separator my-5"></div>

                <div class="row g-5">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('banner::banner.view.fields.schedule') }}</label>
                        <div class="border rounded px-3 py-3 text-muted fs-7">
                            <div>{{ __('banner::banner.view.starts_at') }}:
                                <span class="fw-semibold text-gray-900" data-banner-view="starts_at">—</span>
                            </div>
                            <div>{{ __('banner::banner.view.ends_at') }}:
                                <span class="fw-semibold text-gray-900" data-banner-view="ends_at">—</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('banner::banner.view.fields.button') }}</label>
                        <div class="border rounded px-3 py-3 text-muted fs-7">
                            <div>{{ __('banner::banner.view.fields.button_label') }}:
                                <span class="fw-semibold text-gray-900" data-banner-view="button_label">—</span>
                            </div>
                            <div>{{ __('banner::banner.view.fields.button_url') }}:
                                <a href="#" target="_blank" class="fw-semibold" data-banner-view="button_url">—</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="separator my-5"></div>

                <div class="row g-5">
                    <div class="col-md-6">
                        <div class="text-muted fs-7">{{ __('banner::banner.view.fields.created_at') }}</div>
                        <div class="fw-semibold text-gray-900" data-banner-view="created_at">—</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted fs-7">{{ __('banner::banner.view.fields.updated_at') }}</div>
                        <div class="fw-semibold text-gray-900" data-banner-view="updated_at">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('banner::banner.actions.cancel') }}</button>
            </div>
        </div>
    </div>
</div>
