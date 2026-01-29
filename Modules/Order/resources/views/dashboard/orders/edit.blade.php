@extends('layouts.dashboard.master')

@section('title', __('order::dashboard.orders.edit.title', ['id' => $order->id]))
@section('page-title', __('order::dashboard.orders.edit.title', ['id' => $order->id]))

@section('content')
    <div class="d-flex flex-column flex-lg-row gap-5 mb-5">
        <a href="{{ route('dashboard.orders.show', $order) }}" class="btn btn-light btn-flex align-items-center gap-2">
            <i class="ki-outline ki-arrow-right fs-2"></i>
            {{ __('dashboard.back') }}
        </a>
    </div>

    <div class="card card-flush shadow-sm">
        <div class="card-header">
            <h3 class="card-title fw-bold">{{ __('order::dashboard.orders.edit.title', ['id' => $order->id]) }}</h3>
        </div>
        <div class="card-body">
            <form id="orderEditForm">
                @csrf
                @method('PUT')

                <div class="row g-5 mb-7">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('order::dashboard.orders.show.order_status') }}</label>
                        <select name="status_id" class="form-select form-select-solid" disabled>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ $order->order_status_id === $status->id ? 'selected' : '' }}>
                                    {{ $status->title }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">{{ __('order::dashboard.orders.edit.status_note') }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('order::dashboard.orders.show.payment_status') }}</label>
                        <select name="payment_status" class="form-select form-select-solid" disabled>
                            @foreach($paymentStatuses as $status)
                                <option value="{{ $status }}" {{ $order->payment_status === $status ? 'selected' : '' }}>
                                    {{ __("order::messages.order.payment_status.{$status}") }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">{{ __('order::dashboard.orders.edit.payment_status_note') }}</div>
                    </div>
                </div>

                <div class="mb-7">
                    <label class="form-label fw-bold">{{ __('order::dashboard.orders.edit.notes') }}</label>
                    <textarea name="notes" class="form-control form-control-solid" rows="5" placeholder="{{ __('order::dashboard.orders.edit.notes_placeholder') }}">{{ is_array($order->notes) ? json_encode($order->notes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $order->notes }}</textarea>
                    <div class="form-text">{{ __('order::dashboard.orders.edit.notes_help') }}</div>
                </div>

                <div class="mb-7">
                    <label class="form-label fw-bold">{{ __('order::dashboard.orders.edit.meta') }}</label>
                    <textarea name="meta" class="form-control form-control-solid" rows="5" placeholder="{{ __('order::dashboard.orders.edit.meta_placeholder') }}">{{ is_array($order->meta) ? json_encode($order->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $order->meta }}</textarea>
                    <div class="form-text">{{ __('order::dashboard.orders.edit.meta_help') }}</div>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('dashboard.orders.show', $order) }}" class="btn btn-light">{{ __('dashboard.cancel') }}</a>
                    <button type="submit" class="btn btn-primary" data-kt-indicator="off">
                        <span class="indicator-label">{{ __('dashboard.save') }}</span>
                        <span class="indicator-progress">
                            {{ __('dashboard.please_wait') }}...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.OrderEdit = {
            routes: {
                update: "{{ route('dashboard.orders.update', $order) }}",
                show: "{{ route('dashboard.orders.show', $order) }}",
            },
            messages: {
                updated: "{{ __('order::messages.order_updated') }}",
            }
        };
    </script>
    @vite(['resources/js/app.js', 'Modules/Order/resources/assets/js/order-edit.js'])
@endpush

