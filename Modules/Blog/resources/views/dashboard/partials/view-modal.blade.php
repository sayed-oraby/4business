<div class="modal fade" id="blogViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h3 class="modal-title">{{ __('blog::blog.view.title') }}</h3>
                <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </button>
            </div>
            <div class="modal-body py-5 px-6">
                <div class="row g-5 mb-5">
                    <div class="col-md-4">
                        <div class="border rounded py-3 px-3 d-flex align-items-center justify-content-center bg-light">
                            <img src="" alt="" class="img-fluid rounded blog-thumb" data-blog-view="image">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h4 class="fw-bold text-gray-900 mb-3" data-blog-view="title">—</h4>
                        <p class="text-muted mb-4" data-blog-view="short_description">—</p>
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <span class="text-muted fw-semibold">{{ __('blog::blog.view.fields.status') }}:</span>
                                <span class="badge badge-light-primary ms-2" data-blog-view="status">—</span>
                            </div>
                            <div>
                                <span class="text-muted fw-semibold">{{ __('blog::blog.view.fields.state') }}:</span>
                                <span class="badge badge-light ms-2" data-blog-view="state">—</span>
                            </div>
                            <div>
                                <span class="text-muted fw-semibold">{{ __('blog::blog.view.fields.author') }}:</span>
                                <span class="fw-bold ms-2" data-blog-view="author">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-semibold">{{ __('blog::blog.view.fields.description') }}</label>
                    <div class="border rounded px-3 py-3 text-gray-900 min-h-150px" data-blog-view="description">—</div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-semibold">{{ __('blog::blog.view.fields.tags') }}</label>
                    <div data-blog-view="tags" class="d-flex flex-wrap gap-2"></div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-semibold">{{ __('blog::blog.view.fields.gallery') }}</label>
                    <div class="row g-4" data-blog-view="gallery"></div>
                </div>

                <div class="row g-5">
                    <div class="col-md-6">
                        <div class="text-muted fs-7">{{ __('blog::blog.view.fields.created_at') }}</div>
                        <div class="fw-semibold text-gray-900" data-blog-view="created_at">—</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted fs-7">{{ __('blog::blog.view.fields.updated_at') }}</div>
                        <div class="fw-semibold text-gray-900" data-blog-view="updated_at">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('blog::blog.actions.cancel') }}</button>
            </div>
        </div>
    </div>
</div>
