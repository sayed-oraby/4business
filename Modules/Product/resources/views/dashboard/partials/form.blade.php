@php
    $mode ??= 'create';

    $titleRaw = $product->getRawOriginal('title');
    if (is_string($titleRaw)) {
        $titleTranslations = json_decode($titleRaw, true) ?: [];
    } elseif (is_array($titleRaw)) {
        $titleTranslations = $titleRaw;
    } else {
        $titleTranslations = [];
    }

    $descriptionRaw = $product->getRawOriginal('description');
    if (is_string($descriptionRaw)) {
        $descriptionTranslations = json_decode($descriptionRaw, true) ?: [];
    } elseif (is_array($descriptionRaw)) {
        $descriptionTranslations = $descriptionRaw;
    } else {
        $descriptionTranslations = [];
    }
@endphp

@once
    @push('styles')
        <style>
            .product-dropzone {
                border: 1px dashed var(--bs-gray-400);
                border-radius: 0.95rem;
                background: #fcfcfc;
                padding: 1.5rem;
                min-height: 160px;
            }

            .nav-link.product-locale-invalid {
                color: var(--bs-danger);
            }

            .nav-link.product-locale-invalid::after {
                content: '•';
                color: var(--bs-danger);
                margin-inline-start: 0.25rem;
                font-size: 1.25rem;
                line-height: 1;
            }

            .product-dropzone-message {
                color: var(--bs-gray-600);
            }

            .product-dropzone-previews .dz-preview {
                margin: 0.5rem;
                display: inline-block;
            }

            .product-dropzone .dz-image {
                border-radius: 0.75rem;
                overflow: hidden;
            }

            .product-dropzone .dz-image img {
                width: 120px;
                height: 120px;
                object-fit: cover;
            }
        </style>
    @endpush
@endonce

<form id="productForm"
      action="{{ $mode === 'edit' ? route('dashboard.products.update', $product) : route('dashboard.products.store') }}"
      method="POST"
      class="needs-validation"
      novalidate
      enctype="multipart/form-data"
      data-product-form
      data-product-mode="{{ $mode }}">
    @csrf
    <input type="hidden" name="_method" value="{{ $mode === 'edit' ? 'PUT' : 'POST' }}">
    <input type="hidden" name="product_id" value="{{ $mode === 'edit' ? $product->id : '' }}">
    <input type="hidden" name="gallery_token" value="{{ $galleryToken }}">

    <div class="alert alert-danger d-none" data-product-errors>
        {{ __('product::product.form.validation_errors') }}
    </div>

    <div class="row g-5">
        <div class="col-xl-8">
            <div class="card card-flush shadow-sm mb-5">
                <div class="card-header">
                    <div class="card-title">{{ __('product::product.form.localization') }}</div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                        @foreach($availableLocales as $code => $locale)
                            <li class="nav-item">
                                <a class="nav-link @if($loop->first) active @endif"
                                   data-product-locale-tab
                                   data-locale="{{ $code }}"
                                   data-bs-toggle="tab"
                                   href="#product_locale_{{ $code }}">
                                    {{ $locale['native'] ?? strtoupper($code) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content">
                        @foreach($availableLocales as $code => $locale)
                            <div class="tab-pane fade @if($loop->first) show active @endif" id="product_locale_{{ $code }}">
                                <div class="mb-5">
                                    <label class="form-label @if($loop->first) required @endif">
                                        {{ __("product::product.form.title_{$code}") }}
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           name="title[{{ $code }}]"
                                           value="{{ old("title.$code", $titleTranslations[$code] ?? '') }}">
                                </div>
                                <div>
                                    <label class="form-label">{{ __("product::product.form.description_{$code}") }}</label>
                                    <textarea class="form-control"
                                              rows="4"
                                              name="description[{{ $code }}]">{{ old("description.$code", $descriptionTranslations[$code] ?? '') }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card card-flush shadow-sm mb-5">
                <div class="card-header">
                    <div class="card-title">{{ __('product::product.form.image') }}</div>
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <label class="form-label">{{ __('product::product.form.image') }}</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <div class="form-text">{{ __('product::product.form.image_help') }}</div>
                        @if($mode === 'edit' && $product->image_path)
                            <div class="border rounded d-inline-flex align-items-center gap-3 p-3 mt-3 bg-light-subtle">
                                <img src="{{ setting_media_url($product->image_path) }}" alt="cover" class="rounded" style="width: 120px; height: 120px; object-fit: cover;">
                                <div class="text-muted small">
                                    {{ __('product::product.form.image_current') }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold">{{ __('product::product.form.gallery') }}</label>
                        <div class="product-dropzone" data-product-dropzone>
                            <div class="product-dropzone-message text-center" data-product-dropzone-message>
                                <i class="ki-duotone ki-picture fs-2qx mb-3"></i>
                                <div>{{ __('product::product.form.gallery_help') }}</div>
                            </div>
                            <div class="product-dropzone-previews" data-product-dropzone-previews></div>
                        </div>
                        <div class="form-text">{{ __('product::product.form.gallery_hint') }}</div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0">{{ __('product::product.form.tags') }}</label>
                            <button type="button" class="btn btn-sm btn-light-primary" data-product-action="open-tag-modal">
                                <i class="ki-duotone ki-plus fs-3 me-1"></i>{{ __('product::product.form.add_tag') }}
                            </button>
                        </div>
                        <select class="form-select"
                                name="tags[]"
                                multiple
                                data-product-tags
                                data-control="select2"
                                data-placeholder="{{ __('product::product.form.tags_placeholder') }}">
                            @if($product->relationLoaded('tags'))
                                @foreach($product->tags as $tag)
                                    <option value="{{ $tag->id }}" selected>{{ $tag->title }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card card-flush shadow-sm mb-5">
                <div class="card-header">
                    <div class="card-title">{{ __('product::product.form.status') }}</div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label required">{{ __('product::product.form.status') }}</label>
                        <select class="form-select" name="status">
                            @foreach($statuses as $status)
                                <option value="{{ $status }}"
                                    @selected(old('status', $product->status) === $status)>
                                    {{ __('product::product.statuses.' . $status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label required">{{ __('product::product.form.sku') }}</label>
                        <input type="text" class="form-control" name="sku" value="{{ old('sku', $product->sku) }}">
                    </div>
                    <div class="mb-4">
                        <label class="form-label required">{{ __('product::product.form.price') }}</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="price" value="{{ old('price', $product->price) }}">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">{{ __('product::product.form.position') }}</label>
                        <input type="number" min="0" class="form-control" name="position" value="{{ old('position', $product->position) }}">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">{{ __('product::product.form.category') }}</label>
                        <select class="form-select"
                                name="category_id"
                                data-product-category
                                data-control="select2"
                                data-placeholder="{{ __('product::product.form.category_placeholder') }}">
                            @if($product->relationLoaded('category') && $product->category)
                                <option value="{{ $product->category->id }}" selected>{{ $product->category->title }}</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="form-label">{{ __('product::product.form.brand') }}</label>
                        <select class="form-select"
                                name="brand_id"
                                data-product-brand
                                data-control="select2"
                                data-placeholder="{{ __('product::product.form.brand_placeholder') }}">
                            @if($product->relationLoaded('brand') && $product->brand)
                                <option value="{{ $product->brand->id }}" selected>{{ $product->brand->title }}</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="card card-flush shadow-sm mb-5">
                <div class="card-header">
                    <div class="card-title">{{ __('product::product.form.inventory') }}</div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label required">{{ __('product::product.form.qty') }}</label>
                        <input type="number" min="0" class="form-control" name="qty" value="{{ old('qty', $product->qty) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('product::product.form.flags') }}</label>
                        <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid mb-2">
                            <input class="form-check-input" type="checkbox" value="1" name="is_featured" @checked(old('is_featured', $product->is_featured))>
                            <label class="form-check-label">
                                {{ __('product::product.flags.featured') }}
                            </label>
                        </div>
                        <div class="form-text">{{ __('product::product.flags.featured_hint') }}</div>
                        <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid mt-4 mb-2">
                            <input class="form-check-input" type="checkbox" value="1" name="is_new_arrival" @checked(old('is_new_arrival', $product->is_new_arrival))>
                            <label class="form-check-label">
                                {{ __('product::product.flags.new_arrival') }}
                            </label>
                        </div>
                        <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="1" name="is_trending" @checked(old('is_trending', $product->is_trending))>
                            <label class="form-check-label">
                                {{ __('product::product.flags.trending') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-flush shadow-sm">
                <div class="card-header">
                    <div class="card-title">{{ __('product::product.form.offer') }}</div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">{{ __('product::product.form.offer_type') }}</label>
                        <select class="form-select" name="offer_type">
                            <option value="">{{ __('product::product.labels.no') }}</option>
                            <option value="percentage" @selected(old('offer_type', $product->offer_type) === 'percentage')>
                                {{ __('product::product.form.offer_type_percentage') }}
                            </option>
                            <option value="fixed" @selected(old('offer_type', $product->offer_type) === 'fixed')>
                                {{ __('product::product.form.offer_type_fixed') }}
                            </option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">{{ __('product::product.form.offer_price') }}</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="offer_price" value="{{ old('offer_price', $product->offer_price) }}">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">{{ __('product::product.form.offer_starts_at') }}</label>
                        <input type="datetime-local" class="form-control" name="offer_starts_at"
                               value="{{ old('offer_starts_at', optional($product->offer_starts_at)->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">{{ __('product::product.form.offer_ends_at') }}</label>
                        <input type="datetime-local" class="form-control" name="offer_ends_at"
                               value="{{ old('offer_ends_at', optional($product->offer_ends_at)->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="form-text">{{ __('product::product.form.offer_help') }}</div>
                </div>
            </div>
        </div>
    </div>
</form>

<template id="productDropzonePreviewTemplate">
    <div class="dz-preview dz-file-preview">
        <div class="dz-image">
            <img data-dz-thumbnail />
        </div>
        <div class="dz-details text-center mt-2">
            <div class="dz-filename fw-semibold text-gray-700 fs-8">
                <span data-dz-name></span>
            </div>
        </div>
        <a class="dz-remove text-danger fs-8" href="javascript:void(0);" data-dz-remove>{{ __('product::product.actions.delete') }}</a>
    </div>
</template>

<div class="modal fade" id="productTagModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('product::product.form.tag_modal_title') }}</h3>
                <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="productTagForm">
                    @csrf
                    <div class="row g-4">
                        @foreach($availableLocales as $code => $locale)
                            <div class="col-12">
                                <label class="form-label @if($loop->first) required @endif">
                                    {{ __("product::product.form.tag_title_{$code}") }}
                                </label>
                                <input type="text" class="form-control" name="title[{{ $code }}]">
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('product::product.actions.cancel') }}</button>
                <button type="submit" class="btn btn-primary" form="productTagForm" data-product-action="submit-tag">
                    {{ __('product::product.form.tag_save') }}
                </button>
            </div>
        </div>
    </div>
</div>
