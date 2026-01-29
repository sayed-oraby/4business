@extends('layouts.dashboard.master')

@section('title', __('page::page.title'))
@section('page-title', __('page::page.title'))

@push('styles')
<style>
    .page-hero {
        background: linear-gradient(135deg, #eff4ff 0%, #ffffff 100%);
        border-radius: 1.5rem;
    }

    .page-table-card .table thead tr {
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .04em;
    }

    .page-table-card .table thead th,
    .page-table-card .table tbody td {
        text-align: center !important;
        vertical-align: middle;
    }

    .page-thumb {
        width: 72px;
        height: 72px;
        object-fit: cover;
        border-radius: 0.75rem;
        background: #f5f8fa;
    }

    .nav-link.page-locale-invalid {
        color: var(--bs-danger);
    }

    .nav-link.page-locale-invalid::after {
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
    <div class="card border-0 shadow-sm mb-10 page-hero">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-7">
                <div>
                    <span class="badge badge-light-primary mb-3">{{ __('dashboard.content') }}</span>
                    <h2 class="fw-bold text-gray-900 mb-2">{{ __('page::page.title') }}</h2>
                    <p class="text-gray-600 mb-0">{{ __('page::page.description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-flush shadow-sm mb-10 page-table-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">{{ __('page::page.title') }}</h3>
            </div>
            <div class="card-toolbar gap-3">
                <div class="position-relative my-1">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12" id="page-search" placeholder="{{ __('page::page.search_placeholder') }}">
                </div>
                <button class="btn btn-light btn-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#pageFiltersCanvas">
                    <i class="ki-duotone ki-filter fs-2"></i>{{ __('dashboard.filter_button') }}
                </button>
                @can('pages.create')
                    <button class="btn btn-primary btn-flex align-items-center gap-2" data-page-action="open-form">
                        <i class="ki-duotone ki-plus fs-2"></i>{{ __('page::page.actions.create') }}
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4" id="pages-table">
                    <thead class="bg-transparent text-gray-500 fw-semibold">
                        <tr class="text-center align-middle">
                            <th class="w-60px text-center">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="select-all-pages">
                                </div>
                            </th>
                            <th class="min-w-250px text-center">{{ __('page::page.table.title') }}</th>
                            <th class="min-w-150px text-center">{{ __('page::page.table.slug') }}</th>
                            <th class="min-w-125px text-center">{{ __('page::page.table.status') }}</th>
                            <th class="min-w-150px text-center">{{ __('page::page.table.updated_at') }}</th>
                            <th class="text-center pe-4 min-w-150px">{{ __('page::page.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        @can('pages.delete')
            <div class="card-footer d-flex justify-content-between flex-wrap gap-3">
                <div class="text-muted">{{ __('page::page.actions.bulk_delete') }}</div>
                <button class="btn btn-light-danger" id="bulk-delete-btn" disabled>
                    <i class="ki-duotone ki-trash fs-2 me-2"></i>{{ __('page::page.actions.bulk_delete') }}
                </button>
            </div>
        @endcan
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="pageFiltersCanvas">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-bold">{{ __('dashboard.filters_panel.title') }}</h5>
            <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="offcanvas">
                <i class="ki-duotone ki-cross fs-2"></i>
            </button>
        </div>
        <div class="offcanvas-body pt-5">
            <form id="page-filter-form" class="d-flex flex-column gap-5">
                <div>
                    <label class="form-label">{{ __('page::page.filters.status') }}</label>
                    <select class="form-select form-select-solid" name="status" data-page-filter="status">
                        <option value="">{{ __('page::page.filters.status') }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ __('page::page.statuses.' . $status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('page::page.filters.state.active') }}</label>
                    <select class="form-select form-select-solid" name="state" data-page-filter="state">
                        <option value="active">{{ __('page::page.filters.state.active') }}</option>
                        <option value="archived">{{ __('page::page.filters.state.archived') }}</option>
                        <option value="all">{{ __('page::page.filters.state.all') }}</option>
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

    @include('page::dashboard.partials.form', ['availableLocales' => $availableLocales, 'statuses' => $statuses])
    @include('page::dashboard.partials.view-modal', ['availableLocales' => $availableLocales])
@endsection

@push('scripts')
    <script>
        window.PageModule = {
            routes: {
                data: "{{ route('dashboard.pages.data') }}",
                store: "{{ route('dashboard.pages.store') }}",
                update: "{{ route('dashboard.pages.update', ['page' => '__ID__']) }}",
                destroy: "{{ route('dashboard.pages.destroy', ['page' => '__ID__']) }}",
                bulkDestroy: "{{ route('dashboard.pages.bulk-destroy') }}",
            },
            can: {
                update: @json(auth('admin')->user()?->can('pages.update')),
                delete: @json(auth('admin')->user()?->can('pages.delete')),
            },
            statuses: @json($statuses),
            statusLabels: @json(collect($statuses)->mapWithKeys(fn($status) => [$status => __('page::page.statuses.' . $status)])),
            states: {
                active: "{{ __('page::page.states.active') }}",
                archived: "{{ __('page::page.states.archived') }}",
            },
            messages: {
                created: "{{ __('page::page.messages.created') }}",
                updated: "{{ __('page::page.messages.updated') }}",
                deleted: "{{ __('page::page.messages.deleted') }}",
                bulkDeleted: "{{ __('page::page.messages.bulk_deleted') }}",
            },
            i18n: {
                view: "{{ __('page::page.actions.view') }}",
                edit: "{{ __('page::page.actions.edit') }}",
                delete: "{{ __('page::page.actions.delete') }}",
            },
            confirm: {
                deleteTitle: "{{ __('page::page.actions.delete') }}",
                deleteMessage: "{{ __('page::page.actions.confirm_delete') }}",
                bulkTitle: "{{ __('page::page.actions.bulk_delete') }}",
                bulkMessage: "{{ __('page::page.actions.bulk_delete_confirm') }}",
                confirm: "{{ __('page::page.actions.confirm') }}",
                cancel: "{{ __('page::page.actions.cancel') }}",
            },
            locale: "{{ app()->getLocale() }}",
            locales: @json($availableLocales),
            view: {
                fields: @json(__('page::page.view.fields')),
                translationsEmpty: "{{ __('page::page.view.translations.empty') }}",
            },
        };
    </script>
    @vite('Modules/Page/resources/assets/js/page.js')
@endpush
