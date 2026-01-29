@extends('layouts.dashboard.master')

@section('title', __('order::dashboard.orders.show.title', ['id' => $order->id]))
@section('page-title', __('order::dashboard.orders.show.title', ['id' => $order->id]))

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        .invoice-container {
            box-shadow: none !important;
            border: none !important;
        }
    }
    .invoice-container {
        max-width: 900px;
        margin: 0 auto;
    }
</style>
@endpush

@section('content')
    <div class="d-flex flex-column flex-lg-row gap-5 mb-5 no-print">
        <a href="{{ route('dashboard.orders.index') }}" class="btn btn-light btn-flex align-items-center gap-2">
            <i class="ki-outline ki-arrow-right fs-2"></i>
            {{ __('dashboard.back') }}
        </a>
        <div class="ms-auto d-flex gap-3">
            @can('orders.update')
                <a href="{{ route('dashboard.orders.edit', $order) }}" class="btn btn-primary btn-flex align-items-center gap-2">
                    <i class="ki-outline ki-pencil fs-2"></i>
                    {{ __('order::dashboard.orders.actions.edit') }}
                </a>
            @endcan
            <button onclick="window.print()" class="btn btn-light btn-flex align-items-center gap-2">
                <i class="ki-outline ki-printer fs-2"></i>
                {{ __('order::dashboard.orders.actions.print') }}
            </button>
        </div>
    </div>

    <div class="invoice-container">
        <!-- Invoice Header -->
        <div class="card card-flush shadow-sm mb-5">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-7">
                    <div>
                        <h2 class="fw-bold text-gray-900 mb-2">{{ __('order::dashboard.orders.show.invoice') }} #{{ $order->id }}</h2>
                        <span class="text-muted fs-6">{{ __('order::dashboard.orders.show.order_date') }}: {{ $order->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="text-end">
                        <span class="badge rounded-1 fs-6 px-4 py-2" style="background:{{ $order->status?->color ?? '#f6f8fb' }};color:#111;">
                            {{ $order->status?->title ?? '—' }}
                        </span>
                        <div class="mt-2">
                            <span class="badge badge-light-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'danger') }} fs-6">
                                {{ __("order::messages.order.payment_status.{$order->payment_status}") }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="row g-5 mb-7">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">{{ __('order::dashboard.orders.show.customer_info') }}</h5>
                        <div class="text-gray-700">
                            @if($order->user_id)
                                <div>{{ __('order::dashboard.orders.show.user_id') }}: #{{ $order->user_id }}</div>
                            @else
                                <div>{{ __('order::dashboard.orders.show.guest') }}: {{ $order->guest_uuid }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">{{ __('order::dashboard.orders.show.shipping_address') }}</h5>
                        @if($order->shippingAddress)
                            <div class="text-gray-700">
                                <div>{{ $order->shippingAddress->full_name }}</div>
                                <div>{{ $order->shippingAddress->address }}</div>
                                <div>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}</div>
                                <div>{{ $order->shippingAddress->country }} {{ $order->shippingAddress->postal_code }}</div>
                                <div class="mt-2">{{ $order->shippingAddress->phone }}</div>
                            </div>
                        @else
                            <div class="text-muted">—</div>
                        @endif
                    </div>
                </div>

                <!-- Status Change Forms -->
                @can('orders.update')
                <div class="row g-5 mb-7">
                    <div class="col-md-6">
                        <form id="changeStatusForm" class="d-flex gap-3 align-items-end">
                            @csrf
                            <div class="flex-grow-1">
                                <label class="form-label fw-bold">{{ __('order::dashboard.orders.show.change_status') }}</label>
                                <select name="status_id" class="form-select form-select-solid" required>
                                    <option value="">{{ __('order::dashboard.orders.filters.all_statuses') }}</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}" {{ $order->order_status_id === $status->id ? 'selected' : '' }}>
                                            {{ $status->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" data-kt-indicator="off">
                                <span class="indicator-label">{{ __('order::dashboard.orders.actions.change_status') }}</span>
                                <span class="indicator-progress">
                                    {{ __('dashboard.please_wait') }}...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form id="changePaymentStatusForm" class="d-flex gap-3 align-items-end">
                            @csrf
                            <div class="flex-grow-1">
                                <label class="form-label fw-bold">{{ __('order::dashboard.orders.show.change_payment_status') }}</label>
                                <select name="payment_status" class="form-select form-select-solid" required>
                                    @foreach($paymentStatuses as $status)
                                        <option value="{{ $status }}" {{ $order->payment_status === $status ? 'selected' : '' }}>
                                            {{ __("order::messages.order.payment_status.{$status}") }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" data-kt-indicator="off">
                                <span class="indicator-label">{{ __('order::dashboard.orders.show.update_payment') }}</span>
                                <span class="indicator-progress">
                                    {{ __('dashboard.please_wait') }}...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
                @endcan
            </div>
        </div>

        <!-- Order Items -->
        <div class="card card-flush shadow-sm mb-5">
            <div class="card-header">
                <h3 class="card-title fw-bold">{{ __('order::dashboard.orders.show.order_items') }}</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-4">
                        <thead>
                        <tr class="text-muted text-uppercase">
                            <th>{{ __('order::dashboard.orders.show.item') }}</th>
                            <th class="text-center">{{ __('order::dashboard.orders.show.quantity') }}</th>
                            <th class="text-end">{{ __('order::dashboard.orders.show.unit_price') }}</th>
                            <th class="text-end">{{ __('order::dashboard.orders.show.total') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ $item->title }}</span>
                                        @if($item->sku)
                                            <span class="text-muted fs-7">SKU: {{ $item->sku }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-end">{{ number_format($item->unit_price, 3) }} {{ $order->currency }}</td>
                                <td class="text-end fw-bold">{{ number_format($item->line_total, 3) }} {{ $order->currency }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">{{ __('order::dashboard.orders.show.subtotal') }}:</td>
                            <td class="text-end fw-bold">{{ number_format($order->subtotal, 3) }} {{ $order->currency }}</td>
                        </tr>
                        @if($order->discount_total > 0)
                        <tr>
                            <td colspan="3" class="text-end">{{ __('order::dashboard.orders.show.discount') }}:</td>
                            <td class="text-end">-{{ number_format($order->discount_total, 3) }} {{ $order->currency }}</td>
                        </tr>
                        @endif
                        @if($order->shipping_total > 0)
                        <tr>
                            <td colspan="3" class="text-end">{{ __('order::dashboard.orders.show.shipping') }}:</td>
                            <td class="text-end">{{ number_format($order->shipping_total, 3) }} {{ $order->currency }}</td>
                        </tr>
                        @endif
                        @if($order->tax_total > 0)
                        <tr>
                            <td colspan="3" class="text-end">{{ __('order::dashboard.orders.show.tax') }}:</td>
                            <td class="text-end">{{ number_format($order->tax_total, 3) }} {{ $order->currency }}</td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td colspan="3" class="text-end fw-bold fs-4">{{ __('order::dashboard.orders.show.grand_total') }}:</td>
                            <td class="text-end fw-bold fs-4">{{ number_format($order->grand_total, 3) }} {{ $order->currency }}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        @if($order->payments->count() > 0)
        <div class="card card-flush shadow-sm mb-5">
            <div class="card-header">
                <h3 class="card-title fw-bold">{{ __('order::dashboard.orders.show.payment_details') }}</h3>
            </div>
            <div class="card-body">
                @foreach($order->payments as $payment)
                    <div class="mb-5 {{ !$loop->last ? 'border-bottom pb-5' : '' }}">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold">{{ __('order::dashboard.orders.show.payment') }} #{{ $payment->id }}</h5>
                                <div class="text-muted fs-7">
                                    {{ __('order::dashboard.orders.show.provider') }}: {{ __("order::messages.payment_provider.{$payment->provider}") }}
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge badge-light-{{ $payment->status === 'paid' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ __("order::messages.order.payment_status.{$payment->status}") }}
                                </span>
                                @if($payment->invoice_url)
                                    <a href="{{ $payment->invoice_url }}" target="_blank" class="btn btn-sm btn-light-info ms-2">
                                        <i class="ki-outline ki-link fs-4"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <span class="text-muted">{{ __('order::dashboard.orders.show.amount') }}:</span>
                                <div class="fw-bold">{{ number_format($payment->amount, 3) }} {{ $payment->currency }}</div>
                            </div>
                            @if($payment->ref_number)
                            <div class="col-md-3">
                                <span class="text-muted">{{ __('order::dashboard.orders.show.ref_number') }}:</span>
                                <div class="fw-bold">{{ $payment->ref_number }}</div>
                            </div>
                            @endif
                            <div class="col-md-3">
                                <span class="text-muted">{{ __('order::dashboard.orders.show.created_at') }}:</span>
                                <div>{{ $payment->created_at->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>
                        @if($payment->logs->count() > 0)
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">{{ __('order::dashboard.orders.show.payment_logs') }}</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                    <tr>
                                        <th>{{ __('order::dashboard.orders.show.direction') }}</th>
                                        <th>{{ __('order::dashboard.orders.show.status_code') }}</th>
                                        <th>{{ __('order::dashboard.orders.show.created_at') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($payment->logs as $log)
                                        <tr>
                                            <td>{{ $log->direction }}</td>
                                            <td>{{ $log->status_code }}</td>
                                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Status History -->
        @if($order->statusHistory->count() > 0)
        <div class="card card-flush shadow-sm mb-5">
            <div class="card-header">
                <h3 class="card-title fw-bold">{{ __('order::dashboard.orders.show.status_history') }}</h3>
            </div>
            <div class="card-body">
                <div class="timeline timeline-border-dashed">
                    @foreach($order->statusHistory as $history)
                        <div class="timeline-item">
                            <div class="timeline-line w-40px"></div>
                            <div class="timeline-icon symbol symbol-circle symbol-40px">
                                <i class="ki-outline ki-check fs-2"></i>
                            </div>
                            <div class="timeline-content mb-10">
                                <div class="pe-3 mb-5">
                                    <div class="fs-5 fw-semibold mb-2">{{ $history->status?->title ?? '—' }}</div>
                                    @if($history->comment)
                                        <div class="text-muted mb-2">{{ $history->comment }}</div>
                                    @endif
                                    <div class="text-muted fs-7">{{ $history->created_at->format('Y-m-d H:i:s') }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        window.OrderShow = {
            routes: {
                changeStatus: "{{ route('dashboard.orders.status', $order) }}",
                changePaymentStatus: "{{ route('dashboard.orders.payment-status', $order) }}",
            },
            messages: {
                statusChanged: "{{ __('order::messages.status_changed') }}",
                paymentStatusChanged: "{{ __('order::messages.payment_status_changed') }}",
            }
        };
    </script>
    @vite(['resources/js/app.js', 'Modules/Order/resources/assets/js/order-show.js'])
@endpush

