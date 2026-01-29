@extends('layouts.dashboard.master')

@section('title', __('brand::brand.title'))
@section('page-title', __('brand::brand.title'))

@push('styles')
<style>
    .brand-hero {
        background: linear-gradient(135deg, #eff4ff 0%, #ffffff 100%);
        border-radius: 1.5rem;
    }

    .brand-table-card .table thead tr {
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .04em;
    }

    .brand-table-card .table thead th,
    .brand-table-card .table tbody td {
        text-align: center !important;
        vertical-align: middle;
    }

    .brand-thumb {
        width: 60px;
        height: 60px;
        object-fit: contain;
        border-radius: 0.75rem;
        background: #f5f8fa;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .nav-link.brand-locale-invalid {
        color: var(--bs-danger);
    }

    .nav-link.brand-locale-invalid::after {
        content: '•';
        color: var(--bs-danger);
        margin-inline-start: 0.25rem;
        font-size: 1.25rem;
        line-height: 1;
    }
</style>
@endpush

@php($availableLocales = available_locales())

@section('content')
    <div class="card border-0 shadow-sm mb-10 brand-hero">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-7">
                <div>
                    <span class="badge badge-light-primary mb-3">{{ __('dashboard.catalog') }}</span>
                    <h2 class="fw-bold text-gray-900 mb-2">{{ __('brand::brand.title') }}</h2>
                    <p class="text-gray-600 mb-0">{{ __('brand::brand.description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-flush shadow-sm mb-10 brand-table-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">{{ __('brand::brand.title') }}</h3>
            </div>
            <div class="card-toolbar gap-3">
                <div class="position-relative my-1">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12" id="brand-search" placeholder="{{ __('brand::brand.search_placeholder') }}">
                </div>
                <button class="btn btn-light btn-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#brandFiltersCanvas">
                    <i class="ki-duotone ki-filter fs-2"></i>{{ __('dashboard.filter_button') }}
                </button>
                @can('brands.create')
                    <button class="btn btn-primary btn-flex align-items-center gap-2" data-brand-action="open-form">
                        <i class="ki-duotone ki-plus fs-2"></i>{{ __('brand::brand.actions.create') }}
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4" id="brands-table">
                    <thead class="bg-transparent text-gray-500 fw-semibold">
                        <tr class="text-center align-middle">
                            <th class="w-60px text-center">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="select-all-brands">
                                </div>
                            </th>
                            <th class="min-w-220px text-center">{{ __('brand::brand.table.title') }}</th>
                            <th class="min-w-125px text-center">{{ __('brand::brand.table.status') }}</th>
                            <th class="min-w-125px text-center">{{ __('brand::brand.table.position') }}</th>
                            <th class="min-w-150px text-center">{{ __('brand::brand.table.updated_at') }}</th>
                            <th class="text-center pe-4 min-w-150px">{{ __('brand::brand.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        @can('brands.delete')
            <div class="card-footer d-flex justify-content-between flex-wrap gap-3">
                <div class="text-muted">{{ __('brand::brand.actions.bulk_delete') }}</div>
                <button class="btn btn-light-danger" id="bulk-delete-btn" disabled>
                    <i class="ki-duotone ki-trash fs-2 me-2"></i>{{ __('brand::brand.actions.bulk_delete') }}
                </button>
            </div>
        @endcan
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="brandFiltersCanvas">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-bold">{{ __('dashboard.filters_panel.title') }}</h5>
            <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="offcanvas">
                <i class="ki-duotone ki-cross fs-2"></i>
            </button>
        </div>
        <div class="offcanvas-body pt-5">
            <form id="brand-filter-form" class="d-flex flex-column gap-5">
                <div>
                    <label class="form-label">{{ __('brand::brand.filters.status') }}</label>
                    <select class="form-select form-select-solid" name="status" data-brand-filter="status">
                        <option value="">{{ __('brand::brand.filters.status') }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ __('brand::brand.statuses.' . $status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('brand::brand.filters.state.active') }}</label>
                    <select class="form-select form-select-solid" name="state" data-brand-filter="state">
                        <option value="active">{{ __('brand::brand.filters.state.active') }}</option>
                        <option value="archived">{{ __('brand::brand.filters.state.archived') }}</option>
                        <option value="all">{{ __('brand::brand.filters.state.all') }}</option>
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

    @include('brand::dashboard.partials.form', ['availableLocales' => $availableLocales, 'statuses' => $statuses])
@endsection

@push('scripts')
    <script>
        window.BrandModule = {
            routes: {
                data: "{{ route('dashboard.brands.data') }}",
                store: "{{ route('dashboard.brands.store') }}",
                update: "{{ route('dashboard.brands.update', ['brand' => '__ID__']) }}",
                destroy: "{{ route('dashboard.brands.destroy', ['brand' => '__ID__']) }}",
                bulkDestroy: "{{ route('dashboard.brands.bulk-destroy') }}",
            },
            can: {
                update: @json(auth('admin')->user()?->can('brands.update')),
                delete: @json(auth('admin')->user()?->can('brands.delete')),
            },
            statuses: @json($statuses),
            statusLabels: @json(collect($statuses)->mapWithKeys(fn($status) => [$status => __('brand::brand.statuses.' . $status)])),
            states: {
                active: "{{ __('brand::brand.states.active') }}",
                archived: "{{ __('brand::brand.states.archived') }}",
            },
            messages: {
                created: "{{ __('brand::brand.messages.created') }}",
                updated: "{{ __('brand::brand.messages.updated') }}",
                deleted: "{{ __('brand::brand.messages.deleted') }}",
                bulkDeleted: "{{ __('brand::brand.messages.bulk_deleted') }}",
            },
            i18n: {
                view: "{{ __('brand::brand.actions.view') }}",
                edit: "{{ __('brand::brand.actions.edit') }}",
                delete: "{{ __('brand::brand.actions.delete') }}",
            },
            confirm: {
                deleteTitle: "{{ __('brand::brand.actions.delete') }}",
                deleteMessage: "{{ __('brand::brand.actions.confirm_delete') }}",
                bulkTitle: "{{ __('brand::brand.actions.bulk_delete') }}",
                bulkMessage: "{{ __('brand::brand.actions.bulk_delete_confirm') }}",
                confirm: "{{ __('brand::brand.actions.confirm') }}",
                cancel: "{{ __('brand::brand.actions.cancel') }}",
            },
            locale: "{{ app()->getLocale() }}",
            locales: @json($availableLocales),
        };
    </script>
    @vite('Modules/Brand/resources/assets/js/brand.js')
@endpush
