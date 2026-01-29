<div class="modal fade" id="shippingCityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" data-shipping-city-form-title>{{ __('shipping::dashboard.locations.actions.add_city') }}</h3>
                <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="shippingCityForm">
                    @csrf
                    <input type="hidden" name="city_id">
                    <input type="hidden" name="_method" value="POST">
                    <div class="alert alert-danger d-none" data-shipping-city-errors></div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('shipping::dashboard.locations.form.state') }}</label>
                            <select class="form-select" name="shipping_state_id" data-city-state-select required>
                                <option value="">{{ __('shipping::dashboard.locations.filters.select_state') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('shipping::dashboard.locations.form.city_code') }}</label>
                            <input type="text" class="form-control" name="code" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('shipping::dashboard.locations.form.city_name_en') }}</label>
                            <input type="text" class="form-control" name="name_en">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('shipping::dashboard.locations.form.city_name_ar') }}</label>
                            <input type="text" class="form-control" name="name_ar">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Lat</label>
                            <input type="number" step="0.0000001" class="form-control" name="lat">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Lng</label>
                            <input type="number" step="0.0000001" class="form-control" name="lng">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('shipping::dashboard.countries.actions.cancel') }}</button>
                <button type="button" class="btn btn-primary" data-shipping-city-action="submit">{{ __('shipping::dashboard.countries.actions.edit') }}</button>
            </div>
        </div>
    </div>
</div>
