@php
    $availableLocales = $availableLocales ?? config('setting.defaults.supported_locales', []);
    $supportedLocales = data_get($settings, 'supported_locales', $availableLocales);
    $initialSelection = $selectedLocales ?? array_keys($supportedLocales);
    $selectedLocales = old('supported_locales', $initialSelection);
    if (! is_array($selectedLocales)) {
        $selectedLocales = (array) $selectedLocales;
    }
    $selectedLocales = array_values(array_unique(array_filter($selectedLocales, function ($code) use ($availableLocales) {
        return is_string($code) && array_key_exists($code, $availableLocales ?? []);
    })));
    if (array_key_exists('en', $availableLocales ?? []) && ! in_array('en', $selectedLocales, true)) {
        array_unshift($selectedLocales, 'en');
    }
@endphp

@extends('layouts.dashboard.master')

@section('title', __('setting::settings.title'))
@section('page-title', __('setting::settings.title'))

@push('styles')
    <style>
        .settings-nav .nav-link {
            border: 1px solid transparent;
            margin-bottom: .75rem;
        }
        .settings-nav .nav-link.active {
            border-color: var(--bs-primary);
        }
        .settings-media-preview {
            max-height: 110px;
            object-fit: contain;
        }
        .locale-pane.d-none {
            display: none !important;
        }
    </style>
@endpush

@section('content')
    <form method="POST" action="{{ route('dashboard.settings.update') }}" enctype="multipart/form-data">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger d-flex align-items-center mb-5">
                <i class="ki-duotone ki-information-5 fs-2hx text-danger me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <div>
                    <h4 class="mb-1">{{ __('setting::settings.messages.validation_failed') }}</h4>
                    <ul class="mb-0 ps-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="row g-5 g-xl-10">
            <div class="col-xl-3">
                <div class="card card-flush">
                    <div class="card-header">
                        <h3 class="card-title fw-bold">{{ __('setting::settings.navigation.title') }}</h3>
                    </div>
                    <div class="card-body settings-nav">
                        <div class="nav flex-column nav-pills" id="settingsNav" role="tablist">
                            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#settings_general" type="button">
                                {{ __('setting::settings.tabs.general') }}
                            </button>
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#settings_languages" type="button">
                                {{ __('setting::settings.tabs.languages') }}
                            </button>
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#settings_contact" type="button">
                                {{ __('setting::settings.tabs.contact') }}
                            </button>
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#settings_mail" type="button">
                                {{ __('setting::settings.tabs.mail') }}
                            </button>
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#settings_branding" type="button">
                                {{ __('setting::settings.tabs.branding') }}
                            </button>
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#settings_social" type="button">
                                {{ __('setting::settings.tabs.social') }}
                            </button>
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#settings_custom_code" type="button">
                                {{ __('setting::settings.tabs.custom_code') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-9">
                <div class="tab-content" id="settingsTabContent">
                    <div class="tab-pane fade show active" id="settings_general" role="tabpanel">
                        <div class="card card-flush mb-5">
                            <div class="card-header border-0 pt-6">
                                <h3 class="card-title">{{ __('setting::settings.general.heading') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-6">
                                    <label class="required form-label">{{ __('setting::settings.general.app_name') }}</label>
                                    <ul class="nav nav-tabs nav-line-tabs mb-4">
                                        @foreach($availableLocales as $code => $meta)
                                            <li class="nav-item">
                                                <button class="nav-link {{ $loop->first ? 'active' : '' }}" type="button" data-bs-toggle="tab" data-bs-target="#app_name_{{ $code }}">
                                                    {{ $meta['native'] ?? strtoupper($code) }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content">
                                        @foreach($availableLocales as $code => $meta)
                                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="app_name_{{ $code }}">
                                                <input type="text"
                                                       name="app_name[{{ $code }}]"
                                                       class="form-control form-control-solid mb-3"
                                                       value="{{ old("app_name.$code", data_get($settings, "app_name.$code")) }}"
                                                       maxlength="120">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label class="form-label">{{ __('setting::settings.general.app_description') }}</label>
                                    <ul class="nav nav-tabs nav-line-tabs mb-4">
                                        @foreach($availableLocales as $code => $meta)
                                            <li class="nav-item">
                                                <button class="nav-link {{ $loop->first ? 'active' : '' }}" type="button" data-bs-toggle="tab" data-bs-target="#app_desc_{{ $code }}">
                                                    {{ $meta['native'] ?? strtoupper($code) }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content">
                                        @foreach($availableLocales as $code => $meta)
                                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="app_desc_{{ $code }}">
                                                <textarea name="app_description[{{ $code }}]" class="form-control form-control-solid" rows="3">{{ old("app_description.$code", data_get($settings, "app_description.$code")) }}</textarea>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="settings_languages" role="tabpanel">
                        <div class="card card-flush mb-5">
                            <div class="card-header border-0 pt-6">
                                <h3 class="card-title">{{ __('setting::settings.languages.heading') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">{{ __('setting::settings.languages.supported') }}</label>
                                        <div class="d-flex flex-column gap-3" data-supported-locales>
                                            @foreach($availableLocales as $code => $meta)
                                                <label class="form-check form-switch form-switch-sm form-check-custom">
                                                    <input class="form-check-input" type="checkbox" name="supported_locales[]" value="{{ $code }}"
                                                           data-locale-checkbox
                                                           {{ in_array($code, $selectedLocales, true) ? 'checked' : '' }}
                                                           {{ $code === 'en' ? 'disabled' : '' }}>
                                                    <span class="form-check-label">{{ $meta['native'] ?? $meta['name'] ?? strtoupper($code) }}</span>
                                                </label>
                                            @endforeach
                                            <input type="hidden" name="supported_locales[]" value="en">
                                            <span class="text-muted fs-7">{{ __('setting::settings.languages.hint') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">{{ __('setting::settings.languages.default_locale') }}</label>
                                        <select name="default_locale" class="form-select form-select-solid mb-5">
                                            @foreach($availableLocales as $code => $meta)
                                                <option value="{{ $code }}" {{ old('default_locale', $settings['default_locale'] ?? app()->getLocale()) === $code ? 'selected' : '' }}>
                                                    {{ $meta['native'] ?? $meta['name'] ?? strtoupper($code) }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <label class="form-label fw-semibold">{{ __('setting::settings.languages.fallback_locale') }}</label>
                                        <select name="fallback_locale" class="form-select form-select-solid">
                                            @foreach($availableLocales as $code => $meta)
                                                <option value="{{ $code }}" {{ old('fallback_locale', $settings['fallback_locale'] ?? config('app.fallback_locale')) === $code ? 'selected' : '' }}>
                                                    {{ $meta['native'] ?? $meta['name'] ?? strtoupper($code) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="settings_contact" role="tabpanel">
                        <div class="card card-flush mb-5">
                            <div class="card-header border-0 pt-6">
                                <h3 class="card-title">{{ __('setting::settings.contact.heading') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-6">
                                    <label class="form-label fw-semibold">{{ __('setting::settings.contact.address_heading') }}</label>
                                    <ul class="nav nav-tabs nav-line-tabs mb-5" data-locale-tabs>
                                        @foreach($availableLocales as $code => $meta)
                                            <li class="nav-item">
                                                <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                        type="button"
                                                        data-bs-toggle="tab"
                                                        data-bs-target="#address_{{ $code }}"
                                                        data-locale-tab="{{ $code }}">
                                                    {{ $meta['native'] ?? strtoupper($code) }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content">
                                        @foreach($availableLocales as $code => $meta)
                                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="address_{{ $code }}">
                                                <textarea class="form-control form-control-solid"
                                                          name="addresses[{{ $code }}]"
                                                          rows="2"
                                                          data-locale-pane="{{ $code }}">{{ old("addresses.$code", data_get($contact, "address.$code")) }}</textarea>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-6">
                                        <label class="required form-label">{{ __('setting::settings.contact.inbox_email') }}</label>
                                        <input type="email" name="contact[inbox_email]" class="form-control form-control-solid @error('contact.inbox_email') is-invalid @enderror"
                                               value="{{ old('contact.inbox_email', $contact['inbox_email'] ?? '') }}">
                                        @error('contact.inbox_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-6">
                                        <label class="form-label">{{ __('setting::settings.contact.whatsapp') }}</label>
                                        <input type="text" name="contact[whatsapp]" class="form-control form-control-solid"
                                               value="{{ old('contact.whatsapp', $contact['whatsapp'] ?? '') }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-6">
                                        <label class="form-label">{{ __('setting::settings.contact.phone') }}</label>
                                        <input type="text" name="contact[phone]" class="form-control form-control-solid"
                                               value="{{ old('contact.phone', $contact['phone'] ?? '') }}">
                                    </div>
                                    <div class="col-md-6 mb-6">
                                        <label class="form-label">{{ __('setting::settings.contact.support_line') }}</label>
                                        <input type="text" name="contact[support_line]" class="form-control form-control-solid"
                                               value="{{ old('contact.support_line', $contact['support_line'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="settings_mail" role="tabpanel">
                        <div class="card card-flush mb-5">
                            <div class="card-header border-0 pt-6">
                                <h3 class="card-title">{{ __('setting::settings.mail.heading') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-6">
                                        <label class="form-label">{{ __('setting::settings.mail.driver') }}</label>
                                        <select name="mail[driver]" class="form-select form-select-solid">
                                            @foreach($mailDrivers as $value => $label)
                                                <option value="{{ $value }}" {{ old('mail.driver', $mail['driver'] ?? 'smtp') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-6">
                                        <label class="form-label">{{ __('setting::settings.mail.encryption') }}</label>
                                        <select name="mail[encryption]" class="form-select form-select-solid">
                                            @foreach($mailEncryptions as $value => $label)
                                                <option value="{{ $value }}" {{ old('mail.encryption', $mail['encryption'] ?? 'ssl') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-6">
                                        <label class="form-label">{{ __('setting::settings.mail.host') }}</label>
                                        <input type="text" name="mail[host]" class="form-control form-control-solid"
                                               value="{{ old('mail.host', $mail['host'] ?? '') }}">
                                    </div>
                                    <div class="col-md-3 mb-6">
                                        <label class="form-label">{{ __('setting::settings.mail.port') }}</label>
                                        <input type="number" name="mail[port]" class="form-control form-control-solid"
                                               value="{{ old('mail.port', $mail['port'] ?? 465) }}">
                                    </div>
                                    <div class="col-md-3 mb-6">
                                        <label class="form-label">{{ __('setting::settings.mail.username') }}</label>
                                        <input type="text" name="mail[username]" class="form-control form-control-solid"
                                               value="{{ old('mail.username', $mail['username'] ?? '') }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-6">
                                        <label class="form-label">{{ __('setting::settings.mail.password') }}</label>
                                        <input type="password" name="mail[password]" class="form-control form-control-solid"
                                               placeholder="{{ __('setting::settings.mail.password_placeholder') }}">
                                        <span class="text-muted fs-7">{{ __('setting::settings.mail.password_help') }}</span>
                                    </div>
                                    <div class="col-md-6 mb-6">
                                        <label class="form-label">{{ __('setting::settings.mail.from_name') }}</label>
                                        <input type="text" name="mail[from][name]" class="form-control form-control-solid"
                                               value="{{ old('mail.from.name', data_get($mail, 'from.name')) }}">
                                    </div>
                                    <div class="col-md-6 mb-6">
                                        <label class="form-label">{{ __('setting::settings.mail.from_address') }}</label>
                                        <input type="email" name="mail[from][address]" class="form-control form-control-solid"
                                               value="{{ old('mail.from.address', data_get($mail, 'from.address')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="settings_branding" role="tabpanel">
                        <div class="card card-flush mb-5">
                            <div class="card-header border-0 pt-6">
                                <h3 class="card-title">{{ __('setting::settings.branding.heading') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-5">
                                    @php($logoValue = data_get($branding, 'logo'))
                                    @php($logoWhiteValue = data_get($branding, 'logo_white'))
                                    @php($faviconValue = data_get($branding, 'favicon'))
                                    <div class="col-md-4">
                                        <x-setting::media field="logo" :label="__('setting::settings.branding.logo')" :value="$logoValue" />
                                    </div>
                                    <div class="col-md-4">
                                        <x-setting::media field="logo_white" :label="__('setting::settings.branding.logo_white')" :value="$logoWhiteValue" />
                                    </div>
                                    <div class="col-md-4">
                                        <x-setting::media field="favicon" :label="__('setting::settings.branding.favicon')" :value="$faviconValue" />
                                    </div>
                                </div>

                                <div class="mt-8">
                                    <label class="form-label">{{ __('setting::settings.branding.footer') }}</label>
                                    <input type="text" name="branding_footer" class="form-control form-control-solid"
                                           value="{{ old('branding_footer', data_get($branding, 'footer')) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="settings_social" role="tabpanel">
                        <div class="card card-flush mb-5">
                            <div class="card-header border-0 pt-6">
                                <h3 class="card-title">{{ __('setting::settings.social.heading') }}</h3>
                            </div>
                            <div class="card-body">
                                @foreach(['facebook','twitter','instagram','youtube','snapchat','tiktok'] as $network)
                                    <div class="mb-5">
                                        <label class="form-label text-capitalize">{{ __('setting::settings.social.'.$network) }}</label>
                                        <input type="url" class="form-control form-control-solid"
                                               name="social[{{ $network }}]"
                                               value="{{ old("social.$network", $social[$network] ?? '') }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="settings_custom_code" role="tabpanel">
                        <div class="card card-flush">
                            <div class="card-header border-0 pt-6">
                                <h3 class="card-title">{{ __('setting::settings.custom_code.heading') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-6">
                                    <label class="form-label">{{ __('setting::settings.custom_code.head_css') }}</label>
                                    <textarea name="custom_code[head_css]" class="form-control form-control-solid font-monospace" rows="4">{{ old('custom_code.head_css', data_get($customCode, 'head_css')) }}</textarea>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label">{{ __('setting::settings.custom_code.head_js') }}</label>
                                    <textarea name="custom_code[head_js]" class="form-control form-control-solid font-monospace" rows="4">{{ old('custom_code.head_js', data_get($customCode, 'head_js')) }}</textarea>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label">{{ __('setting::settings.custom_code.body_js') }}</label>
                                    <textarea name="custom_code[body_js]" class="form-control form-control-solid font-monospace" rows="4">{{ old('custom_code.body_js', data_get($customCode, 'body_js')) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-10">
                    <button type="submit" class="btn btn-primary">
                        <span class="indicator-label">{{ __('setting::settings.actions.save') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    @vite('Modules/Setting/resources/assets/js/settings.js')
@endpush
