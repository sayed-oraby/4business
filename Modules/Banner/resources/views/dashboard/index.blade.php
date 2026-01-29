@extends('layouts.dashboard.master')

@section('title', __('banner::banner.title'))
@section('page-title', __('banner::banner.title'))

@push('styles')
<style>
    .banner-hero {
        background: linear-gradient(135deg, #eff4ff 0%, #ffffff 100%);
        border-radius: 1.5rem;
    }

    .banner-table-card .table thead tr {
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .04em;
    }

    .banner-table-card .table thead th,
    .banner-table-card .table tbody td {
        text-align: center !important;
        vertical-align: middle;
    }

    .banner-thumb {
        width: 120px;
        height: 60px;
        object-fit: cover;
        border-radius: 0.65rem;
    }
</style>
@endpush

@php($availableLocales = available_locales())

@section('content')
    <div class="card border-0 shadow-sm mb-10 banner-hero">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-7">
                <div>
                    <span class="badge badge-light-primary mb-3">{{ __('dashboard.content') }}</span>
                    <h2 class="fw-bold text-gray-900 mb-2">{{ __('banner::banner.title') }}</h2>
                    <p class="text-gray-600 mb-0">{{ __('banner::banner.description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-flush shadow-sm mb-10 banner-table-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">{{ __('banner::banner.title') }}</h3>
            </div>
            <div class="card-toolbar gap-3">
                <div class="position-relative my-1">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12" id="banner-search" placeholder="{{ __('banner::banner.search_placeholder') }}">
                </div>
                {{-- <button class="btn btn-light btn-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#bannerFiltersCanvas">
                    <i class="ki-duotone ki-filter fs-2"></i>{{ __('dashboard.filter_button') }}
                </button> --}}
                @can('banners.create')
                    <button class="btn btn-primary btn-flex align-items-center gap-2" data-banner-action="open-form">
                        <i class="ki-duotone ki-plus fs-2"></i>{{ __('banner::banner.actions.create') }}
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4" id="banner-table">
                    <thead class="bg-transparent text-gray-500 fw-semibold">
                        <tr class="text-center align-middle">
                            <th class="w-60px text-center">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="select-all-banners">
                                </div>
                            </th>
                            <th class="w-150px text-center">{{ __('banner::banner.table.image') }}</th>
                            <th class="text-center">{{ __('banner::banner.table.title') }}</th>
                            {{-- <th class="text-center">{{ __('banner::banner.table.placement') }}</th> --}}
                            <th class="text-center">{{ __('banner::banner.table.status') }}</th>
                            {{-- <th class="text-center">{{ __('banner::banner.table.schedule') }}</th> --}}
                            <th class="text-center pe-4 min-w-150px">{{ __('banner::banner.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        @can('banners.delete')
            <div class="card-footer d-flex justify-content-between flex-wrap gap-3">
                <div class="text-muted">{{ __('banner::banner.actions.bulk_delete') }}</div>
                <button class="btn btn-light-danger" id="bulk-delete-btn" disabled>
                    <i class="ki-duotone ki-trash fs-2 me-2"></i>{{ __('banner::banner.actions.bulk_delete') }}
                </button>
            </div>
        @endcan
    </div>

    {{-- <div class="offcanvas offcanvas-end" tabindex="-1" id="bannerFiltersCanvas">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-bold">{{ __('dashboard.filters_panel.title') }}</h5>
            <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="offcanvas">
                <i class="ki-duotone ki-cross fs-2"></i>
            </button>
        </div>
        <div class="offcanvas-body pt-5">
            <form id="banner-filter-form" class="d-flex flex-column gap-5">
                <div>
                    <label class="form-label">{{ __('banner::banner.filters.placement') }}</label>
                    <select class="form-select form-select-solid" name="placement" data-banner-filter="placement">
                        <option value="">{{ __('banner::banner.filters.placement') }}</option>
                        @foreach($placements as $key => $placement)
                            <option value="{{ $key }}">{{ __($placement['label']) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('banner::banner.filters.status') }}</label>
                    <select class="form-select form-select-solid" name="status" data-banner-filter="status">
                        <option value="">{{ __('banner::banner.filters.status') }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ __('banner::banner.statuses.' . $status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('banner::banner.filters.state.active') }}</label>
                    <select class="form-select form-select-solid" name="state" data-banner-filter="state">
                        <option value="active">{{ __('banner::banner.filters.state.active') }}</option>
                        <option value="archived">{{ __('banner::banner.filters.state.archived') }}</option>
                        <option value="all">{{ __('banner::banner.filters.state.all') }}</option>
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
    </div> --}}

    @include('banner::dashboard.partials.form', ['availableLocales' => $availableLocales])
    @include('banner::dashboard.partials.view-modal')
@endsection

@push('scripts')
    <script>
        window.BannerModule = {
            routes: {
                data: "{{ route('dashboard.banners.data') }}",
                store: "{{ route('dashboard.banners.store') }}",
                update: "{{ route('dashboard.banners.update', ['banner' => '__ID__']) }}",
                destroy: "{{ route('dashboard.banners.destroy', ['banner' => '__ID__']) }}",
                bulkDestroy: "{{ route('dashboard.banners.bulk-destroy') }}",
            },
            can: {
                update: @json(auth('admin')->user()?->can('banners.update')),
                delete: @json(auth('admin')->user()?->can('banners.delete')),
            },
            placements: @json(collect($placements)->mapWithKeys(fn($placement, $key) => [$key => __($placement['label'])])),
            statuses: @json($statuses),
            statusLabels: @json(collect($statuses)->mapWithKeys(fn($status) => [$status => __('banner::banner.statuses.' . $status)])),
            messages: {
                created: "{{ __('banner::banner.messages.created') }}",
                updated: "{{ __('banner::banner.messages.updated') }}",
                deleted: "{{ __('banner::banner.messages.deleted') }}",
                bulkDeleted: "{{ __('banner::banner.messages.bulk_deleted') }}",
            },
            states: {
                active: "{{ __('banner::banner.states.active') }}",
                archived: "{{ __('banner::banner.states.archived') }}",
            },
            i18n: {
                view: "{{ __('banner::banner.actions.view') }}",
                edit: "{{ __('banner::banner.actions.edit') }}",
                delete: "{{ __('banner::banner.actions.delete') }}",
            },
            confirm: {
                deleteTitle: "{{ __('banner::banner.actions.delete') }}",
                deleteMessage: "{{ __('banner::banner.actions.confirm_delete') }}",
                bulkTitle: "{{ __('banner::banner.actions.bulk_delete') }}",
                bulkMessage: "{{ __('banner::banner.actions.bulk_delete_confirm') }}",
                confirm: "{{ __('banner::banner.actions.confirm') }}",
                cancel: "{{ __('banner::banner.actions.cancel') }}",
            },
            view: {
                title: "{{ __('banner::banner.view.title') }}",
                fields: @json(__('banner::banner.view.fields')),
                startsAt: "{{ __('banner::banner.view.starts_at') }}",
                endsAt: "{{ __('banner::banner.view.ends_at') }}",
            },
            locale: "{{ app()->getLocale() }}",
        };
    </script>
    @vite('Modules/Banner/resources/assets/js/banner.js')
@endpush
