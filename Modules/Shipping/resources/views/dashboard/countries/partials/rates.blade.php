<div class="modal fade" id="shippingCountryRatesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex flex-column">
                    <h3 class="modal-title">{{ __('shipping::dashboard.rates.title') }}</h3>
                    <span class="text-muted fs-7" data-shipping-country-rates-country></span>
                </div>
                <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info d-none" data-shipping-country-rates-empty>{{ __('shipping::dashboard.rates.empty') }}</div>
                <div class="table-responsive mb-5">
                    <table class="table align-middle table-row-dashed" id="shipping-country-rates-table">
                        <thead>
                        <tr class="text-muted text-uppercase">
                            <th>{{ __('shipping::dashboard.rates.form.calculation_type') }}</th>
                            <th>{{ __('shipping::dashboard.rates.form.base_price') }}</th>
                            <th>{{ __('shipping::dashboard.rates.form.price_per_kg') }}</th>
                            <th>{{ __('shipping::dashboard.rates.form.free_shipping_over') }}</th>
                            <th>{{ __('shipping::dashboard.rates.form.currency') }}</th>
                            <th>{{ __('shipping::dashboard.rates.form.scope') }}</th>
                            <th>{{ __('shipping::dashboard.countries.table.status') }}</th>
                            <th class="text-end">{{ __('shipping::dashboard.countries.table.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody data-shipping-country-rates-list></tbody>
                    </table>
                </div>

                <form id="shippingCountryRateForm">
                    @csrf
                    <input type="hidden" name="_method" value="POST">
                    <input type="hidden" name="rate_id">
                    <div class="alert alert-danger d-none" data-shipping-country-rate-errors></div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('shipping::dashboard.rates.form.calculation_type') }}</label>
                            <select class="form-select" name="calculation_type">
                                <option value="flat">{{ __('shipping::dashboard.rates.calculation_types.flat') }}</option>
                                <option value="weight">{{ __('shipping::dashboard.rates.calculation_types.weight') }}</option>
                                <option value="order_total">{{ __('shipping::dashboard.rates.calculation_types.order_total') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('shipping::dashboard.rates.form.base_price') }}</label>
                            <input type="number" step="0.001" min="0" class="form-control" name="base_price">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('shipping::dashboard.rates.form.price_per_kg') }}</label>
                            <input type="number" step="0.001" min="0" class="form-control" name="price_per_kg">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('shipping::dashboard.rates.form.free_shipping_over') }}</label>
                            <input type="number" step="0.001" min="0" class="form-control" name="free_shipping_over">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('shipping::dashboard.rates.form.currency') }}</label>
                            <input type="text" class="form-control" name="currency" maxlength="3" value="KWD">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('shipping::dashboard.rates.form.state') }}</label>
                            <select class="form-select" name="shipping_state_id" data-shipping-rate-state>
                                <option value="">{{ __('shipping::dashboard.rates.scopes.country') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('shipping::dashboard.rates.form.city') }}</label>
                            <select class="form-select" name="shipping_city_id" data-shipping-rate-city disabled>
                                <option value="">{{ __('shipping::dashboard.rates.scopes.state') }}</option>
                            </select>
                            <small class="text-muted">{{ __('shipping::dashboard.rates.form.scope_notice') }}</small>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch form-check-custom form-check-solid mt-10">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                <label class="form-check-label">{{ __('shipping::dashboard.rates.form.is_active') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('shipping::dashboard.rates.form.delivery_estimate_en') }}</label>
                            <input type="text" class="form-control" name="delivery_estimate_en">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('shipping::dashboard.rates.form.delivery_estimate_ar') }}</label>
                            <input type="text" class="form-control" name="delivery_estimate_ar">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('shipping::dashboard.countries.actions.cancel') }}</button>
                <button type="button" class="btn btn-primary" data-shipping-country-rate-action="submit">{{ __('shipping::dashboard.rates.form.submit') }}</button>
            </div>
        </div>
    </div>
</div>
