@php
    $previewId = 'preview_'.$field;
    $previewUrl = setting_media_url($value, asset('metronic/media/misc/image-placeholders/blank-image.svg'));
    $isRemote = $value && \Illuminate\Support\Str::startsWith($value, ['http://', 'https://']);
@endphp

<div class="card border-dashed border-gray-300 h-100">
    <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
        <h4 class="fw-bold mb-4">{{ $label }}</h4>
        <img src="{{ $previewUrl }}" class="settings-media-preview mb-4" id="{{ $previewId }}" data-placeholder="{{ asset('metronic/media/misc/image-placeholders/blank-image.svg') }}" alt="{{ $label }}">

        <input type="file" name="{{ $field }}" class="form-control form-control-solid mb-3"
               data-media-input
               data-preview-target="#{{ $previewId }}">

        <input type="hidden" name="remove_{{ $field }}" value="0" data-remove-input="{{ $field }}">

        <input type="url" name="{{ $field }}_url" class="form-control form-control-solid mb-3"
               placeholder="https://"
               value="{{ old($field.'_url', $isRemote ? $value : '') }}">

        <button type="button" class="btn btn-sm btn-light" data-media-remove="{{ $field }}" data-preview-target="#{{ $previewId }}">
            {{ __('setting::settings.branding.remove') }}
        </button>
    </div>
</div>
