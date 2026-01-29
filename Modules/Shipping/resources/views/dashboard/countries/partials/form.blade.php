<div class="modal fade" id="shippingCountryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" data-shipping-country-form-title>{{ __('shipping::dashboard.countries.actions.create') }}</h3>
                <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="shippingCountryForm">
                    @csrf
                    <input type="hidden" name="_method" value="POST">
                    <input type="hidden" name="country_id">
                    <div class="alert alert-danger d-none" data-shipping-country-errors></div>
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label">{{ __('shipping::dashboard.countries.form.package_select') }}</label>
                            <select class="form-select" data-shipping-country-package data-control="select2" data-placeholder="{{ __('shipping::dashboard.countries.form.package_select') }}"></select>
                            <div class="form-text">{{ __('shipping::dashboard.countries.form.package_help') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">{{ __('shipping::dashboard.countries.form.iso2') }}</label>
                            <input type="text" class="form-control" name="iso2" maxlength="2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('shipping::dashboard.countries.form.iso3') }}</label>
                            <input type="text" class="form-control" name="iso3" maxlength="3">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('shipping::dashboard.countries.form.phone_code') }}</label>
                            <input type="text" class="form-control" name="phone_code">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">{{ __('shipping::dashboard.countries.form.name_en') }}</label>
                            <input type="text" class="form-control" name="name_en">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">{{ __('shipping::dashboard.countries.form.name_ar') }}</label>
                            <input type="text" class="form-control" name="name_ar">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('shipping::dashboard.countries.form.flag_svg') }}</label>
                            <input type="text" class="form-control" name="flag_svg">
                            <div class="form-text">{{ __('shipping::dashboard.countries.form.flag_help') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('shipping::dashboard.countries.form.sort_order') }}</label>
                            <input type="number" class="form-control" name="sort_order" min="0" value="0">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch form-check-custom form-check-solid mt-10">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                <label class="form-check-label">{{ __('shipping::dashboard.countries.form.is_active') }}</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch form-check-custom form-check-solid mt-10">
                                <input class="form-check-input" type="checkbox" name="is_shipping_enabled" value="1" checked>
                                <label class="form-check-label">{{ __('shipping::dashboard.countries.form.is_shipping_enabled') }}</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('shipping::dashboard.countries.actions.cancel') }}</button>
                <button type="button" class="btn btn-primary" data-shipping-country-action="submit">
                    {{ __('shipping::dashboard.countries.form.submit') }}
                </button>
            </div>
        </div>
    </div>
</div>
