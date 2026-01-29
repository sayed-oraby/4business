<div class="modal fade" id="blogFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h2 class="fw-bold mb-1" data-modal-title>{{ __('blog::blog.actions.create') }}</h2>
                    <span class="text-muted fs-7">{{ __('blog::blog.description') }}</span>
                </div>
                <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </button>
            </div>
            <form id="blogForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="blog_id">
                <input type="hidden" name="gallery_token">
                <div class="modal-body pt-0">
                    <div class="alert alert-danger d-none" data-blog-errors></div>

                    <div class="row g-5">
                        <div class="col-12">
                            <label class="form-label fw-bold mb-2">{{ __('blog::blog.form.localization') }}</label>
                            <ul class="nav nav-tabs nav-line-tabs mb-3 fs-6">
                                @foreach($availableLocales as $code => $locale)
                                    <li class="nav-item">
                                        <a class="nav-link @if($loop->first) active @endif"
                                           data-blog-locale-tab
                                           data-locale="{{ $code }}"
                                           data-bs-toggle="tab"
                                           href="#blog_locale_{{ $code }}">
                                            {{ $locale['native'] ?? strtoupper($code) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content">
                                @foreach($availableLocales as $code => $locale)
                                    <div class="tab-pane fade @if($loop->first) show active @endif" id="blog_locale_{{ $code }}">
                                        <div class="mb-4">
                                            <label class="form-label @if($loop->first) required @endif">{{ __('blog::blog.form.title_' . $code) }}</label>
                                            <input type="text" class="form-control form-control-solid" name="title[{{ $code }}]" @if($loop->first) data-blog-title-en @endif>
                                            <div class="invalid-feedback" data-error-for="title.{{ $code }}"></div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">{{ __('blog::blog.form.short_description_' . $code) }}</label>
                                            <textarea class="form-control form-control-solid" rows="2" name="short_description[{{ $code }}]"></textarea>
                                            <div class="invalid-feedback" data-error-for="short_description.{{ $code }}"></div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">{{ __('blog::blog.form.description_' . $code) }}</label>
                                            <textarea class="form-control form-control-solid" rows="5" name="description[{{ $code }}]"></textarea>
                                            <div class="invalid-feedback" data-error-for="description.{{ $code }}"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">{{ __('blog::blog.form.status') }}</label>
                            <select class="form-select form-select-solid" name="status">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}">{{ __('blog::blog.statuses.' . $status) }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-error-for="status"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('blog::blog.form.author') }}</label>
                            <select class="form-select form-select-solid" name="created_by" data-blog-author data-control="select2" data-placeholder="{{ __('blog::blog.form.author_placeholder') }}"></select>
                            <div class="form-text">{{ __('blog::blog.form.author_help') }}</div>
                            <div class="invalid-feedback" data-error-for="created_by"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('blog::blog.form.image') }}</label>
                            <input type="file" class="form-control form-control-solid" name="image" accept="image/*">
                            <div class="form-text">{{ __('blog::blog.form.image_help') }}</div>
                            <div class="invalid-feedback" data-error-for="image"></div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0">{{ __('blog::blog.form.tags') }}</label>
                                <button type="button" class="btn btn-sm btn-light-primary" data-blog-action="open-tag-modal">
                                    <i class="ki-duotone ki-plus fs-3 me-1"></i>{{ __('blog::blog.form.add_tag') }}
                                </button>
                            </div>
                            <select class="form-select form-select-solid" name="tags[]" multiple data-blog-tags data-control="select2" data-placeholder="{{ __('blog::blog.form.tags_placeholder') }}"></select>
                            <div class="invalid-feedback" data-error-for="tags"></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">{{ __('blog::blog.form.gallery') }}</label>
                            <div class="blog-dropzone" id="blogGalleryDropzone" data-blog-dropzone>
                                <div class="blog-dropzone-message text-center text-muted" data-blog-dropzone-message>
                                    <i class="ki-duotone ki-picture fs-2qx mb-3"></i>
                                    <div>{{ __('blog::blog.form.gallery_help') }}</div>
                                </div>
                                <div class="blog-dropzone-previews" data-blog-dropzone-previews></div>
                            </div>
                            <div class="form-text">{{ __('blog::blog.form.gallery_hint') }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('blog::blog.actions.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" data-blog-action="submit-form" data-kt-indicator="off">
                        <span class="indicator-label">{{ __('blog::blog.form.save') }}</span>
                        <span class="indicator-progress">{{ __('blog::blog.form.save') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="blogDropzonePreviewTemplate">
    <div class="dz-preview dz-file-preview">
        <div class="dz-image">
            <img data-dz-thumbnail />
        </div>
        <div class="dz-details text-center mt-2">
            <div class="dz-filename fw-semibold text-gray-700 fs-8">
                <span data-dz-name></span>
            </div>
        </div>
        <a class="dz-remove text-danger fs-8" href="javascript:void(0);" data-dz-remove>{{ __('blog::blog.actions.delete') }}</a>
    </div>
</template>

<div class="modal fade" id="blogTagModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('blog::blog.form.add_tag') }}</h3>
                <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="blogTagForm">
                    @csrf
                    <div class="row g-4">
                        @foreach($availableLocales as $code => $locale)
                            <div class="col-12">
                                <label class="form-label @if($loop->first) required @endif">{{ __('blog::blog.form.tag_title_' . $code) }}</label>
                                <input type="text" class="form-control" name="title[{{ $code }}]">
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('blog::blog.actions.cancel') }}</button>
                <button type="submit" class="btn btn-primary" form="blogTagForm" data-blog-action="submit-tag">
                    {{ __('blog::blog.form.save_tag') }}
                </button>
            </div>
        </div>
    </div>
</div>
