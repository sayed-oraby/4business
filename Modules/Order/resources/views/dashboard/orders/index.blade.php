@extends('layouts.dashboard.master')

@section('title', __('order::dashboard.orders.title'))
@section('page-title', __('order::dashboard.orders.title'))

@push('styles')
<style>
    .order-hero {
        background: linear-gradient(135deg, #eff4ff 0%, #ffffff 100%);
        border-radius: 1.5rem;
    }

    .order-table-card .table thead tr {
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .04em;
    }

    .order-table-card .table thead th,
    .order-table-card .table tbody td {
        text-align: center !important;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-10 order-hero">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-7">
                <div>
                    <span class="badge badge-light-primary mb-3">{{ __('dashboard.orders') }}</span>
                    <h2 class="fw-bold text-gray-900 mb-2">{{ __('order::dashboard.orders.title') }}</h2>
                    <p class="text-gray-600 mb-0">{{ __('order::dashboard.orders.description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-flush shadow-sm mb-10 order-table-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">{{ __('order::dashboard.orders.title') }}</h3>
            </div>
            <div class="card-toolbar gap-3">
                <button class="btn btn-sm btn-flex btn-light btn-active-light-primary" data-bs-toggle="offcanvas" data-bs-target="#orderFiltersCanvas">
                    <i class="ki-outline ki-filter fs-2"></i>
                    {{ __('dashboard.filters') }}
                </button>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-4" id="orders-table">
                    <thead>
                    <tr class="text-muted text-uppercase">
                        <th>#</th>
                        <th>{{ __('order::dashboard.orders.table.customer') }}</th>
                        <th>{{ __('order::dashboard.orders.table.amount') }}</th>
                        <th>{{ __('order::dashboard.orders.table.status') }}</th>
                        <th>{{ __('order::dashboard.orders.table.payment_status') }}</th>
                        <th>{{ __('order::dashboard.orders.table.created_at') }}</th>
                        <th class="text-center min-w-150px">{{ __('order::dashboard.orders.table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="orderFiltersCanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">{{ __('dashboard.filters') }}</h5>
            <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="offcanvas">
                <i class="ki-outline ki-cross fs-2"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form id="orderFilterForm">
                <div class="mb-5">
                    <label class="form-label">{{ __('dashboard.search') }}</label>
                    <input type="text" class="form-control form-control-solid" id="orders-search" placeholder="{{ __('order::dashboard.orders.filters.search') }}">
                </div>
                <div class="mb-5">
                    <label class="form-label">{{ __('order::dashboard.orders.filters.all_statuses') }}</label>
                    <select class="form-select form-select-solid" id="orders-status-filter">
                        <option value="">{{ __('order::dashboard.orders.filters.all_statuses') }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->code }}">{{ $status->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-5">
                    <label class="form-label">{{ __('order::dashboard.orders.filters.all_payments') }}</label>
                    <select class="form-select form-select-solid" id="orders-payment-filter">
                        <option value="">{{ __('order::dashboard.orders.filters.all_payments') }}</option>
                        <option value="paid">{{ __('order::dashboard.orders.filters.paid') }}</option>
                        <option value="pending">{{ __('order::dashboard.orders.filters.pending') }}</option>
                        <option value="failed">{{ __('order::dashboard.orders.filters.failed') }}</option>
                    </select>
                </div>
                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-light flex-1" data-filter-reset>{{ __('dashboard.reset') }}</button>
                    <button type="button" class="btn btn-primary flex-1" data-filter-apply>{{ __('dashboard.apply') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.OrderDashboard = {
            routes: {
                data: "{{ route('dashboard.orders.data') }}",
                show: "{{ route('dashboard.orders.show', ['order' => '__ID__']) }}",
                changeStatus: "{{ route('dashboard.orders.status', ['order' => '__ID__']) }}",
            },
            labels: {
                view: "{{ __('order::dashboard.orders.actions.view') }}",
                changeStatus: "{{ __('order::dashboard.orders.actions.change_status') }}",
                viewInvoice: "{{ __('order::dashboard.orders.actions.view_invoice') }}",
                statuses: @json($statusesForJs),
            },
            messages: {
                statusChanged: "{{ __('order::messages.status_changed') }}",
                statusChangeFailed: "{{ __('order::messages.status_canceled') }}",
            },
            can: {
                update: @json(auth('admin')->user()?->can('orders.update')),
            }
        }
    </script>
    @vite('resources/js/app.js')
@endpush
