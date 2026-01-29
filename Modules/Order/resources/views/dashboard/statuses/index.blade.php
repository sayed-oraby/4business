@extends('layouts.dashboard.master')

@section('title', __('order::dashboard.statuses.title'))
@section('page-title', __('order::dashboard.statuses.title'))

@php($availableLocales = available_locales())

@push('styles')
<style>
    .order-status-hero {
        background: linear-gradient(135deg, #eff4ff 0%, #ffffff 100%);
        border-radius: 1.5rem;
    }

    .order-status-table-card .table thead tr {
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .04em;
    }

    .order-status-table-card .table thead th,
    .order-status-table-card .table tbody td {
        text-align: center !important;
        vertical-align: middle;
    }

    .nav-link.order-status-locale-invalid {
        color: var(--bs-danger);
    }

    .nav-link.order-status-locale-invalid::after {
        content: '•';
        color: var(--bs-danger);
        margin-inline-start: 0.25rem;
        font-size: 1.25rem;
        line-height: 1;
    }
</style>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-10 order-status-hero">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-7">
                <div>
                    <span class="badge badge-light-primary mb-3">{{ __('dashboard.orders') }}</span>
                    <h2 class="fw-bold text-gray-900 mb-2">{{ __('order::dashboard.statuses.title') }}</h2>
                    <p class="text-gray-600 mb-0">{{ __('order::dashboard.statuses.description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-flush shadow-sm mb-10 order-status-table-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">{{ __('order::dashboard.statuses.title') }}</h3>
            </div>
            <div class="card-toolbar gap-3">
                <button class="btn btn-sm btn-flex btn-light btn-active-light-primary" data-bs-toggle="offcanvas" data-bs-target="#orderStatusFiltersCanvas">
                    <i class="ki-outline ki-filter fs-2"></i>
                    {{ __('dashboard.filters') }}
                </button>
                @can('order_statuses.create')
                    <button class="btn btn-primary btn-flex align-items-center gap-2" data-order-status-action="open-form">
                        <i class="ki-outline ki-plus fs-2"></i>
                        {{ __('order::dashboard.statuses.actions.create') }}
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-4" id="order-statuses-table">
                    <thead>
                    <tr class="text-muted text-uppercase">
                        <th>{{ __('order::dashboard.statuses.table.code') }}</th>
                        <th>{{ __('order::dashboard.statuses.table.title') }}</th>
                        <th>{{ __('order::dashboard.statuses.table.flags') }}</th>
                        <th>{{ __('order::dashboard.statuses.table.sort') }}</th>
                        <th class="text-center min-w-150px">{{ __('order::dashboard.statuses.table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="orderStatusFiltersCanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">{{ __('dashboard.filters') }}</h5>
            <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="offcanvas">
                <i class="ki-outline ki-cross fs-2"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form id="orderStatusFilterForm">
                <div class="mb-5">
                    <label class="form-label">{{ __('dashboard.search') }}</label>
                    <input type="text" class="form-control form-control-solid" id="order-status-search" placeholder="{{ __('dashboard.search') }}">
                </div>
                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-light flex-1" data-filter-reset>{{ __('dashboard.reset') }}</button>
                    <button type="button" class="btn btn-primary flex-1" data-filter-apply>{{ __('dashboard.apply') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="orderStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h3 class="modal-title" data-order-status-form-title>{{ __('order::dashboard.statuses.actions.create') }}</h3>
                    <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-2"></i>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <form id="orderStatusForm">
                        @csrf
                        <input type="hidden" name="status_id">
                        <input type="hidden" name="_method" value="POST">
                        <div class="alert alert-danger d-none" data-order-status-errors></div>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('order::dashboard.statuses.form.code') }}</label>
                                <input type="text" class="form-control form-control-solid" name="code">
                                <div class="invalid-feedback" data-error-for="code"></div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold mb-2">{{ __('order::dashboard.statuses.form.title') }}</label>
                                <ul class="nav nav-tabs nav-line-tabs mb-3 fs-6">
                                    @foreach($availableLocales as $code => $locale)
                                        <li class="nav-item">
                                            <a class="nav-link @if($loop->first) active @endif"
                                               data-bs-toggle="tab"
                                               href="#status_locale_{{ $code }}"
                                               data-order-status-locale-tab
                                               data-locale="{{ $code }}">
                                                {{ $locale['native'] ?? strtoupper($code) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content">
                                    @foreach($availableLocales as $code => $locale)
                                        <div class="tab-pane fade @if($loop->first) show active @endif" id="status_locale_{{ $code }}">
                                            <input type="text"
                                                   class="form-control form-control-solid"
                                                   name="title[{{ $code }}]"
                                                   placeholder="{{ __('order::dashboard.statuses.form.title') }} ({{ $locale['native'] }})">
                                            <div class="invalid-feedback" data-error-for="title.{{ $code }}"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('order::dashboard.statuses.form.color') }}</label>
                                <input type="text" class="form-control form-control-solid" name="color">
                                <div class="invalid-feedback" data-error-for="color"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('order::dashboard.statuses.form.sort_order') }}</label>
                                <input type="number" min="0" class="form-control form-control-solid" name="sort_order" value="0">
                                <div class="invalid-feedback" data-error-for="sort_order"></div>
                            </div>
                            <div class="col-md-9 d-flex gap-4 flex-wrap">
                                <div class="form-check form-check-custom form-check-solid mt-8">
                                    <input class="form-check-input" type="checkbox" name="is_default" value="1"> <label class="form-check-label">{{ __('order::dashboard.statuses.form.is_default') }}</label>
                                </div>
                                <div class="form-check form-check-custom form-check-solid mt-8">
                                    <input class="form-check-input" type="checkbox" name="is_final" value="1"> <label class="form-check-label">{{ __('order::dashboard.statuses.form.is_final') }}</label>
                                </div>
                                <div class="form-check form-check-custom form-check-solid mt-8">
                                    <input class="form-check-input" type="checkbox" name="is_cancel" value="1"> <label class="form-check-label">{{ __('order::dashboard.statuses.form.is_cancel') }}</label>
                                </div>
                                <div class="form-check form-check-custom form-check-solid mt-8">
                                    <input class="form-check-input" type="checkbox" name="is_refund" value="1"> <label class="form-check-label">{{ __('order::dashboard.statuses.form.is_refund') }}</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('order::dashboard.statuses.actions.cancel') }}</button>
                    <button type="button" class="btn btn-primary" data-order-status-action="submit" data-kt-indicator="off">
                        <span class="indicator-label">{{ __('order::dashboard.statuses.actions.save') }}</span>
                        <span class="indicator-progress">
                            {{ __('dashboard.please_wait') }}...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.OrderStatuses = {
            routes: {
                data: "{{ route('dashboard.order-statuses.data') }}",
                store: "{{ route('dashboard.order-statuses.store') }}",
                update: "{{ route('dashboard.order-statuses.update', ['status' => '__ID__']) }}",
                destroy: "{{ route('dashboard.order-statuses.destroy', ['status' => '__ID__']) }}",
            },
            labels: {
                create: "{{ __('order::dashboard.statuses.actions.create') }}",
                edit: "{{ __('order::dashboard.statuses.actions.edit') }}",
                delete: "{{ __('order::dashboard.statuses.actions.delete') }}",
                cancel: "{{ __('order::dashboard.statuses.actions.cancel') }}",
            },
            flags: {
                default: "{{ __('order::dashboard.statuses.flags.default') }}",
                final: "{{ __('order::dashboard.statuses.flags.final') }}",
                cancel: "{{ __('order::dashboard.statuses.flags.cancel') }}",
                refund: "{{ __('order::dashboard.statuses.flags.refund') }}",
            },
            can: {
                update: @json(auth('admin')->user()?->can('order_statuses.update')),
                delete: @json(auth('admin')->user()?->can('order_statuses.delete')),
            }
        };
    </script>
    @vite('resources/js/app.js')
@endpush
