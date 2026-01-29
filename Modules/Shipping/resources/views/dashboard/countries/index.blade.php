@extends('layouts.dashboard.master')

@section('title', __('shipping::dashboard.countries.title'))
@section('page-title', __('shipping::dashboard.countries.title'))

@push('styles')
    <style>
        .shipping-country-hero {
            background: linear-gradient(135deg, #eff4ff 0%, #ffffff 100%);
            border-radius: 1.5rem;
        }

        .shipping-country-table-card .table thead tr {
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: .04em;
        }

        .shipping-country-table-card .table thead th,
        .shipping-country-table-card .table tbody td {
            text-align: center !important;
            vertical-align: middle;
        }

        .shipping-country-flag {
            width: 36px;
            height: 24px;
            object-fit: cover;
            border-radius: 0.25rem;
            border: 1px solid rgba(0,0,0,0.05);
            background: #f8f9fa;
        }
    </style>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-10 shipping-country-hero">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-7">
                <div>
                    <span class="badge badge-light-primary mb-3">{{ __('dashboard.shipping') }}</span>
                    <h2 class="fw-bold text-gray-900 mb-2">{{ __('shipping::dashboard.countries.title') }}</h2>
                    <p class="text-gray-600 mb-0">{{ __('shipping::dashboard.countries.description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-flush shadow-sm mb-10 shipping-country-table-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">{{ __('shipping::dashboard.countries.title') }}</h3>
            </div>
            <div class="card-toolbar gap-3">
                <div class="position-relative my-1">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12" id="shipping-country-search" placeholder="{{ __('shipping::dashboard.countries.filters.search') }}">
                </div>
                <button class="btn btn-light btn-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#shippingCountryFiltersCanvas">
                    <i class="ki-outline ki-filter fs-2"></i>{{ __('dashboard.filter_button') }}
                </button>
                @can('shipping_countries.create')
                    <button class="btn btn-primary btn-flex align-items-center gap-2" data-shipping-country-action="open-form">
                        <i class="ki-outline ki-plus fs-2"></i>{{ __('shipping::dashboard.countries.actions.create') }}
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4" id="shipping-countries-table">
                    <thead class="bg-transparent text-gray-500 fw-semibold">
                        <tr class="text-center align-middle">
                            <th class="min-w-80px text-center">{{ __('shipping::dashboard.countries.table.iso2') }}</th>
                            <th class="min-w-160px text-center">{{ __('shipping::dashboard.countries.table.name_en') }}</th>
                            <th class="min-w-160px text-center">{{ __('shipping::dashboard.countries.table.name_ar') }}</th>
                            <th class="min-w-120px text-center">{{ __('shipping::dashboard.countries.table.phone_code') }}</th>
                            <th class="min-w-125px text-center">{{ __('shipping::dashboard.countries.table.status') }}</th>
                            <th class="min-w-125px text-center">{{ __('shipping::dashboard.countries.table.shipping') }}</th>
                            <th class="min-w-80px text-center">{{ __('shipping::dashboard.countries.table.sort_order') }}</th>
                            <th class="text-center pe-4 min-w-150px">{{ __('shipping::dashboard.countries.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="shippingCountryFiltersCanvas">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-bold">{{ __('dashboard.filters_panel.title') }}</h5>
            <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="offcanvas">
                <i class="ki-duotone ki-cross fs-2"></i>
            </button>
        </div>
        <div class="offcanvas-body pt-5">
            <form id="shipping-country-filter-form" class="d-flex flex-column gap-5">
                <div>
                    <label class="form-label">{{ __('shipping::dashboard.countries.filters.shipping_enabled_all') }}</label>
                    <select class="form-select form-select-solid" name="shipping_enabled" id="shipping-country-shipping-filter">
                        <option value="">{{ __('shipping::dashboard.countries.filters.shipping_enabled_all') }}</option>
                        <option value="1">{{ __('shipping::dashboard.countries.filters.shipping_enabled_yes') }}</option>
                        <option value="0">{{ __('shipping::dashboard.countries.filters.shipping_enabled_no') }}</option>
                    </select>
                </div>
                <div class="d-flex gap-3">
                    <button class="btn btn-light flex-grow-1" type="button" data-filter-reset>
                        <i class="ki-duotone ki-arrows-circle fs-2 me-2"></i>{{ __('dashboard.filters_panel.reset') }}
                    </button>
                    <button class="btn btn-primary flex-grow-1" type="button" data-filter-apply>
                        <i class="ki-duotone ki-check fs-2 me-2"></i>{{ __('dashboard.filters_panel.apply') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('shipping::dashboard.countries.partials.form')
    @include('shipping::dashboard.countries.partials.rates')
@endsection

@push('scripts')
    <script>
        window.ShippingCountries = {
            routes: {
                data: "{{ route('dashboard.shipping.countries.data') }}",
                store: "{{ route('dashboard.shipping.countries.store') }}",
                update: "{{ route('dashboard.shipping.countries.update', ['country' => '__ID__']) }}",
                destroy: "{{ route('dashboard.shipping.countries.destroy', ['country' => '__ID__']) }}",
                package: "{{ route('api.shipping.countries.package') }}",
                import: "{{ route('dashboard.shipping.countries.import', ['country' => '__ID__']) }}",
                states: "{{ route('dashboard.shipping.countries.states', ['country' => '__ID__']) }}",
                rates: {
                    index: "{{ route('dashboard.shipping.countries.rates.index', ['country' => '__ID__']) }}",
                    store: "{{ route('dashboard.shipping.countries.rates.store', ['country' => '__ID__']) }}",
                    update: "{{ route('dashboard.shipping.countries.rates.update', ['rate' => '__ID__']) }}",
                    destroy: "{{ route('dashboard.shipping.countries.rates.destroy', ['rate' => '__ID__']) }}",
                }
            },
            labels: {
                create: "{{ __('shipping::dashboard.countries.actions.create') }}",
                edit: "{{ __('shipping::dashboard.countries.actions.edit') }}",
                delete: "{{ __('shipping::dashboard.countries.actions.delete') }}",
                rates: "{{ __('shipping::dashboard.countries.actions.rates') }}",
                import: "{{ __('shipping::dashboard.countries.actions.import_locations') }}",
                confirmDelete: "{{ __('shipping::messages.country_deleted') }}",
                formTitleCreate: "{{ __('shipping::dashboard.countries.actions.create') }}",
                formTitleEdit: "{{ __('shipping::dashboard.countries.actions.edit') }}",
                active: "{{ __('shipping::messages.active') }}",
                inactive: "{{ __('shipping::messages.inactive') }}",
                shippingEnabled: "{{ __('shipping::dashboard.countries.filters.shipping_enabled_yes') }}",
                shippingDisabled: "{{ __('shipping::dashboard.countries.filters.shipping_enabled_no') }}",
                cancel: "{{ __('shipping::dashboard.countries.actions.cancel') }}",
            },
            rateLabels: {
                title: "{{ __('shipping::dashboard.rates.title') }}",
                empty: "{{ __('shipping::dashboard.rates.empty') }}",
                saved: "{{ __('shipping::messages.rate_updated') }}",
                deleted: "{{ __('shipping::messages.rate_deleted') }}",
                scopeCountry: "{{ __('shipping::dashboard.rates.scopes.country') }}",
                scopeState: "{{ __('shipping::dashboard.rates.scopes.state') }}",
                scopeCity: "{{ __('shipping::dashboard.rates.scopes.city') }}",
            },
            can: {
                create: @json(auth('admin')->user()?->can('shipping_countries.create')),
                update: @json(auth('admin')->user()?->can('shipping_countries.update')),
                delete: @json(auth('admin')->user()?->can('shipping_countries.delete')),
            },
        };
    </script>
    @vite('resources/js/app.js')
@endpush
