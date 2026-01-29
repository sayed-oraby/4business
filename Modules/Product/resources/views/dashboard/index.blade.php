@extends('layouts.dashboard.master')

@section('title', __('product::product.title'))
@section('page-title', __('product::product.title'))

@push('styles')
    <style>
        .product-hero {
            background: linear-gradient(135deg, #eff4ff 0%, #ffffff 100%);
            border-radius: 1.5rem;
        }

        .product-table-card .table thead tr {
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: .04em;
        }

        .product-table-card .table thead th,
        .product-table-card .table tbody td {
            text-align: center !important;
            vertical-align: middle;
        }

        .product-thumb {
            width: 60px;
            height: 60px;
            border-radius: 0.75rem;
            object-fit: cover;
            background: #f5f8fa;
        }

        .product-flag-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
        }
    </style>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-10 product-hero">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-7">
                <div>
                    <span class="badge badge-light-primary mb-3">{{ __('dashboard.catalog') }}</span>
                    <h2 class="fw-bold text-gray-900 mb-2">{{ __('product::product.title') }}</h2>
                    <p class="text-gray-600 mb-0">{{ __('product::product.description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-flush shadow-sm mb-10 product-table-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">{{ __('product::product.title') }}</h3>
            </div>
            <div class="card-toolbar gap-3">
                <div class="position-relative my-1">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12" id="product-search" placeholder="{{ __('product::product.search_placeholder') }}">
                </div>
                <button class="btn btn-light btn-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#productFiltersCanvas">
                    <i class="ki-duotone ki-filter fs-2"></i>{{ __('dashboard.filter_button') }}
                </button>
                @can('products.create')
                    <a class="btn btn-primary btn-flex align-items-center gap-2" href="{{ route('dashboard.products.create') }}">
                        <i class="ki-duotone ki-plus fs-2"></i>{{ __('product::product.actions.create') }}
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4" id="products-table">
                    <thead class="bg-transparent text-gray-500 fw-semibold">
                        <tr class="text-center align-middle">
                            <th class="w-60px text-center">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="select-all-products">
                                </div>
                            </th>
                            <th class="min-w-220px text-center">{{ __('product::product.table.title') }}</th>
                            <th class="min-w-125px text-center">{{ __('product::product.table.sku') }}</th>
                            <th class="min-w-125px text-center">{{ __('product::product.table.price') }}</th>
                            <th class="min-w-125px text-center">{{ __('product::product.table.status') }}</th>
                            <th class="min-w-150px text-center">{{ __('product::product.table.flags') }}</th>
                            <th class="min-w-100px text-center">{{ __('product::product.table.stock') }}</th>
                            <th class="min-w-150px text-center">{{ __('product::product.table.updated_at') }}</th>
                            <th class="text-center pe-4 min-w-150px">{{ __('product::product.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        @can('products.delete')
            <div class="card-footer d-flex justify-content-between flex-wrap gap-3">
                <div class="text-muted">{{ __('product::product.actions.bulk_delete') }}</div>
                <button class="btn btn-light-danger" id="bulk-delete-btn" disabled>
                    <i class="ki-duotone ki-trash fs-2 me-2"></i>{{ __('product::product.actions.bulk_delete') }}
                </button>
            </div>
        @endcan
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="productFiltersCanvas">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-bold">{{ __('dashboard.filters_panel.title') }}</h5>
            <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="offcanvas">
                <i class="ki-duotone ki-cross fs-2"></i>
            </button>
        </div>
        <div class="offcanvas-body pt-5">
            <form id="product-filter-form" class="d-flex flex-column gap-5">
                <div>
                    <label class="form-label">{{ __('product::product.filters.status') }}</label>
                    <select class="form-select form-select-solid" name="status" data-product-filter="status">
                        <option value="">{{ __('product::product.filters.status') }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ __('product::product.statuses.' . $status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('product::product.filters.state.active') }}</label>
                    <select class="form-select form-select-solid" name="state" data-product-filter="state">
                        <option value="active">{{ __('product::product.states.active') }}</option>
                        <option value="archived">{{ __('product::product.states.archived') }}</option>
                        <option value="all">{{ __('product::product.states.all') }}</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('product::product.filters.featured') }}</label>
                    <select class="form-select form-select-solid" name="featured" data-product-filter="featured">
                        <option value="">{{ __('product::product.filters.featured') }}</option>
                        <option value="1">{{ __('product::product.labels.yes') }}</option>
                        <option value="0">{{ __('product::product.labels.no') }}</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('product::product.filters.new_arrival') }}</label>
                    <select class="form-select form-select-solid" name="new_arrival" data-product-filter="new_arrival">
                        <option value="">{{ __('product::product.filters.new_arrival') }}</option>
                        <option value="1">{{ __('product::product.labels.yes') }}</option>
                        <option value="0">{{ __('product::product.labels.no') }}</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('product::product.filters.trending') }}</label>
                    <select class="form-select form-select-solid" name="trending" data-product-filter="trending">
                        <option value="">{{ __('product::product.filters.trending') }}</option>
                        <option value="1">{{ __('product::product.labels.yes') }}</option>
                        <option value="0">{{ __('product::product.labels.no') }}</option>
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
@endsection

@push('scripts')
    <script>
        window.ProductModule = {
            routes: {
                data: "{{ route('dashboard.products.data') }}",
                destroy: "{{ route('dashboard.products.destroy', ['product' => '__ID__']) }}",
                bulkDestroy: "{{ route('dashboard.products.bulk-destroy') }}",
                edit: "{{ route('dashboard.products.edit', ['product' => '__ID__']) }}",
                create: "{{ route('dashboard.products.create') }}",
            },
            actions: {
                edit: "{{ __('product::product.actions.edit') }}",
                delete: "{{ __('product::product.actions.delete') }}",
            },
            statuses: @json($statuses),
            statusLabels: @json(collect($statuses)->mapWithKeys(fn($status) => [$status => __('product::product.statuses.' . $status)])),
            states: {
                active: "{{ __('product::product.states.active') }}",
                archived: "{{ __('product::product.states.archived') }}",
                all: "{{ __('product::product.states.all') }}",
            },
            labels: {
                yes: "{{ __('product::product.labels.yes') }}",
                no: "{{ __('product::product.labels.no') }}",
            },
            flags: {
                featured: "{{ __('product::product.flags.featured') }}",
                new_arrival: "{{ __('product::product.flags.new_arrival') }}",
                trending: "{{ __('product::product.flags.trending') }}",
            },
            confirm: {
                deleteTitle: "{{ __('product::product.actions.delete') }}",
                deleteMessage: "{{ __('product::product.actions.confirm_delete') }}",
                bulkTitle: "{{ __('product::product.actions.bulk_delete') }}",
                bulkMessage: "{{ __('product::product.actions.bulk_delete_confirm') }}",
                confirm: "{{ __('product::product.actions.confirm') }}",
                cancel: "{{ __('product::product.actions.cancel') }}",
            },
            messages: {
                deleted: "{{ __('product::product.messages.deleted') }}",
                bulkDeleted: "{{ __('product::product.messages.bulk_deleted') }}",
            },
            can: {
                update: @json(auth('admin')->user()?->can('products.update')),
                delete: @json(auth('admin')->user()?->can('products.delete')),
            },
            locale: "{{ app()->getLocale() }}",
        };
    </script>
    @vite('Modules/Product/resources/assets/js/product.js')
@endpush
