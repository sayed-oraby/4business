@extends('layouts.dashboard.master')

@section('title', __('shipping::dashboard.locations.title'))
@section('page-title', __('shipping::dashboard.locations.title'))

@section('content')
    <div class="card card-flush shadow-sm mb-10">
        <div class="card-header align-items-center gap-3 flex-wrap">
            <div class="d-flex flex-column">
                <h3 class="card-title fw-bold">{{ __('shipping::dashboard.locations.states_title') }}</h3>
                <span class="text-muted fs-7">{{ __('shipping::dashboard.locations.states_description') }}</span>
            </div>
            <div class="card-toolbar ms-auto d-flex flex-wrap gap-3 align-items-center">
                <select class="form-select form-select-solid w-225px" id="location-country-filter">
                    <option value="">{{ __('shipping::dashboard.locations.filters.all_countries') }}</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name_en }} ({{ $country->iso2 }})</option>
                    @endforeach
                </select>
                <div class="position-relative">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-outline ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12 w-225px" id="location-state-search" placeholder="{{ __('shipping::dashboard.locations.filters.search_states') }}">
                </div>
                @can('shipping_countries.update')
                    <button class="btn btn-primary btn-flex align-items-center gap-2" data-location-action="open-state-form">
                        <i class="ki-outline ki-plus fs-2"></i>{{ __('shipping::dashboard.locations.actions.add_state') }}
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-4" id="shipping-states-table">
                    <thead>
                    <tr class="text-muted text-uppercase">
                        <th>{{ __('shipping::dashboard.locations.table.state_code') }}</th>
                        <th>{{ __('shipping::dashboard.locations.table.state_name_en') }}</th>
                        <th>{{ __('shipping::dashboard.locations.table.state_name_ar') }}</th>
                        <th>{{ __('shipping::dashboard.locations.table.country') }}</th>
                        <th>{{ __('shipping::dashboard.locations.table.cities_count') }}</th>
                        <th class="text-end min-w-150px">{{ __('shipping::dashboard.countries.table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card card-flush shadow-sm">
        <div class="card-header align-items-center gap-3 flex-wrap">
            <div class="d-flex flex-column">
                <h3 class="card-title fw-bold">{{ __('shipping::dashboard.locations.cities_title') }}</h3>
                <span class="text-muted fs-7">{{ __('shipping::dashboard.locations.cities_description') }}</span>
            </div>
            <div class="card-toolbar ms-auto d-flex flex-wrap gap-3 align-items-center">
                <select class="form-select form-select-solid w-225px" id="location-state-filter" disabled>
                    <option value="">{{ __('shipping::dashboard.locations.filters.select_state') }}</option>
                </select>
                <div class="position-relative">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-outline ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12 w-225px" id="location-city-search" placeholder="{{ __('shipping::dashboard.locations.filters.search_cities') }}">
                </div>
                @can('shipping_countries.update')
                    <button class="btn btn-primary btn-flex align-items-center gap-2" data-location-action="open-city-form" disabled>
                        <i class="ki-outline ki-plus fs-2"></i>{{ __('shipping::dashboard.locations.actions.add_city') }}
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-4" id="shipping-cities-table">
                    <thead>
                    <tr class="text-muted text-uppercase">
                        <th>{{ __('shipping::dashboard.locations.table.city_code') }}</th>
                        <th>{{ __('shipping::dashboard.locations.table.city_name_en') }}</th>
                        <th>{{ __('shipping::dashboard.locations.table.city_name_ar') }}</th>
                        <th>{{ __('shipping::dashboard.locations.table.state') }}</th>
                        <th class="text-end min-w-150px">{{ __('shipping::dashboard.countries.table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    @include('shipping::dashboard.locations.partials.state-form')
    @include('shipping::dashboard.locations.partials.city-form')
@endsection

@push('scripts')
    <script>
        window.ShippingLocations = {
            routes: {
                states: "{{ route('dashboard.shipping.locations.states') }}",
                statesStore: "{{ route('dashboard.shipping.locations.states.store') }}",
                statesUpdate: "{{ route('dashboard.shipping.locations.states.update', ['state' => '__ID__']) }}",
                statesDestroy: "{{ route('dashboard.shipping.locations.states.destroy', ['state' => '__ID__']) }}",
                cities: "{{ route('dashboard.shipping.locations.cities') }}",
                citiesStore: "{{ route('dashboard.shipping.locations.cities.store') }}",
                citiesUpdate: "{{ route('dashboard.shipping.locations.cities.update', ['city' => '__ID__']) }}",
                citiesDestroy: "{{ route('dashboard.shipping.locations.cities.destroy', ['city' => '__ID__']) }}",
            },
            labels: {
                stateFormCreate: "{{ __('shipping::dashboard.locations.actions.add_state') }}",
                stateFormEdit: "{{ __('shipping::dashboard.locations.actions.edit_state') }}",
                cityFormCreate: "{{ __('shipping::dashboard.locations.actions.add_city') }}",
                cityFormEdit: "{{ __('shipping::dashboard.locations.actions.edit_city') }}",
                delete: "{{ __('shipping::dashboard.countries.actions.delete') }}",
                cancel: "{{ __('shipping::dashboard.countries.actions.cancel') }}",
            },
            can: {
                update: @json(auth('admin')->user()?->can('shipping_countries.update')),
                delete: @json(auth('admin')->user()?->can('shipping_countries.delete')),
            },
            countries: @json($countries->map(fn ($c) => ['id' => $c->id, 'name' => $c->name_en, 'iso2' => $c->iso2])),
        };
    </script>
    @vite('resources/js/app.js')
@endpush
