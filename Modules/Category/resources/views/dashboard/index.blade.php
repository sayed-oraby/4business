@extends('layouts.dashboard.master')

@section('title', __('category::category.title'))
@section('page-title', __('category::category.title'))

@php($availableLocales = available_locales())

@push('styles')
<style>
    .category-hero {
        background: linear-gradient(135deg, #eff4ff 0%, #ffffff 100%);
        border-radius: 1.5rem;
    }

    .category-hero__stats {
        min-width: 180px;
    }

    .category-table-card .table thead tr {
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .04em;
    }

    .category-table-card .table thead th,
    .category-table-card .table tbody td {
        text-align: center !important;
        vertical-align: middle;
    }

    .category-thumb {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 0.75rem;
        background: #f5f8fa;
    }

    .category-featured-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .nav-link.category-locale-invalid {
        color: var(--bs-danger);
    }

    .nav-link.category-locale-invalid::after {
        content: '•';
        color: var(--bs-danger);
        margin-inline-start: 0.25rem;
        font-size: 1.25rem;
        line-height: 1;
    }
</style>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-10 category-hero">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-7">
                <div>
                    <span class="badge badge-light-primary mb-3">{{ __('dashboard.catalog') }}</span>
                    <h2 class="fw-bold text-gray-900 mb-2">{{ __('category::category.title') }}</h2>
                    <p class="text-gray-600 mb-0">{{ __('category::category.description') }}</p>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-6 mt-8">
                @foreach([
                    'active' => ['class' => 'text-success', 'icon' => 'ki-check-circle'],
                    'draft' => ['class' => 'text-warning', 'icon' => 'ki-file'],
                    'archived' => ['class' => 'text-danger', 'icon' => 'ki-trash'],
                ] as $key => $meta)
                    <div class="category-hero__stats card border-0 shadow-sm flex-grow-1">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <span class="text-muted fw-semibold">{{ __('category::category.stats.'.$key.'.title') }}</span>
                                <span class="symbol symbol-35px symbol-circle bg-light">
                                    <i class="ki-duotone {{ $meta['icon'] }} fs-2 {{ $meta['class'] }}">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div class="fs-1 fw-bolder text-gray-900">{{ $stats[$key] ?? 0 }}</div>
                            <span class="text-muted fw-semibold">{{ __('category::category.stats.'.$key.'.subtitle') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card card-flush shadow-sm mb-10 category-table-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">{{ __('category::category.title') }}</h3>
            </div>
            <div class="card-toolbar gap-3">
                <div class="position-relative my-1">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12" id="category-search" placeholder="{{ __('category::category.search_placeholder') }}">
                </div>
                <button class="btn btn-light btn-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#categoryFiltersCanvas">
                    <i class="ki-duotone ki-filter fs-2"></i>{{ __('dashboard.filter_button') }}
                </button>
                @can('categories.create')
                    <button class="btn btn-primary btn-flex align-items-center gap-2" data-category-action="open-form">
                        <i class="ki-duotone ki-plus fs-2"></i>{{ __('category::category.actions.create') }}
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4" id="categories-table">
                    <thead class="bg-transparent text-gray-500 fw-semibold">
                        <tr class="text-center align-middle">
                            <th class="w-60px text-center">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="select-all-categories">
                                </div>
                            </th>
                            <th class="min-w-250px text-center">{{ __('category::category.table.title') }}</th>
                            <th class="min-w-120px text-center">{{ __('category::category.table.status') }}</th>
                            <th class="min-w-150px text-center">{{ __('category::category.table.parent') }}</th>
                            <th class="min-w-125px text-center">{{ __('category::category.table.featured') }}</th>
                            <th class="min-w-150px text-center">{{ __('category::category.table.updated_at') }}</th>
                            <th class="text-center pe-4 min-w-150px">{{ __('category::category.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        @can('categories.delete')
            <div class="card-footer d-flex justify-content-between flex-wrap gap-3">
                <div class="text-muted">{{ __('category::category.actions.bulk_delete') }}</div>
                <button class="btn btn-light-danger" id="bulk-delete-btn" disabled>
                    <i class="ki-duotone ki-trash fs-2 me-2"></i>{{ __('category::category.actions.bulk_delete') }}
                </button>
            </div>
        @endcan
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="categoryFiltersCanvas">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-bold">{{ __('dashboard.filters_panel.title') }}</h5>
            <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="offcanvas">
                <i class="ki-duotone ki-cross fs-2"></i>
            </button>
        </div>
        <div class="offcanvas-body pt-5">
            <form id="category-filter-form" class="d-flex flex-column gap-5">
                <div>
                    <label class="form-label">{{ __('category::category.filters.status') }}</label>
                    <select class="form-select form-select-solid" name="status" data-category-filter="status">
                        <option value="">{{ __('category::category.filters.status') }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ __('category::category.statuses.' . $status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('category::category.filters.state.active') }}</label>
                    <select class="form-select form-select-solid" name="state" data-category-filter="state">
                        <option value="active">{{ __('category::category.filters.state.active') }}</option>
                        <option value="archived">{{ __('category::category.filters.state.archived') }}</option>
                        <option value="all">{{ __('category::category.filters.state.all') }}</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('category::category.table.featured') }}</label>
                    <select class="form-select form-select-solid" name="featured" data-category-filter="featured">
                        <option value="">{{ __('category::category.table.featured') }}</option>
                        <option value="1">{{ __('category::category.labels.yes') }}</option>
                        <option value="0">{{ __('category::category.labels.no') }}</option>
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

    @include('category::dashboard.partials.form', ['availableLocales' => $availableLocales, 'statuses' => $statuses])
@endsection

@push('scripts')
    <script>
        window.CategoryModule = {
            routes: {
                data: "{{ route('dashboard.categories.data') }}",
                store: "{{ route('dashboard.categories.store') }}",
                update: "{{ route('dashboard.categories.update', ['category' => '__ID__']) }}",
                destroy: "{{ route('dashboard.categories.destroy', ['category' => '__ID__']) }}",
                bulkDestroy: "{{ route('dashboard.categories.bulk-destroy') }}",
                parents: "{{ route('dashboard.categories.parents') }}",
            },
            can: {
                update: @json(auth('admin')->user()?->can('categories.update')),
                delete: @json(auth('admin')->user()?->can('categories.delete')),
            },
            statuses: @json($statuses),
            statusLabels: @json(collect($statuses)->mapWithKeys(fn($status) => [$status => __('category::category.statuses.' . $status)])),
            states: {
                active: "{{ __('category::category.states.active') }}",
                archived: "{{ __('category::category.states.archived') }}",
            },
            labels: {
                yes: "{{ __('category::category.labels.yes') }}",
                no: "{{ __('category::category.labels.no') }}",
            },
            messages: {
                created: "{{ __('category::category.messages.created') }}",
                updated: "{{ __('category::category.messages.updated') }}",
                deleted: "{{ __('category::category.messages.deleted') }}",
                bulkDeleted: "{{ __('category::category.messages.bulk_deleted') }}",
            },
            i18n: {
                view: "{{ __('category::category.actions.view') }}",
                edit: "{{ __('category::category.actions.edit') }}",
                delete: "{{ __('category::category.actions.delete') }}",
                parent: "{{ __('category::category.table.parent') }}",
            },
            confirm: {
                deleteTitle: "{{ __('category::category.actions.delete') }}",
                deleteMessage: "{{ __('category::category.actions.confirm_delete') }}",
                bulkTitle: "{{ __('category::category.actions.bulk_delete') }}",
                bulkMessage: "{{ __('category::category.actions.bulk_delete_confirm') }}",
                confirm: "{{ __('category::category.actions.confirm') }}",
                cancel: "{{ __('category::category.actions.cancel') }}",
            },
            locale: "{{ app()->getLocale() }}",
            locales: @json($availableLocales),
        };
    </script>
@endpush
