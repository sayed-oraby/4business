@extends('layouts.dashboard.master')

@section('title', __('reports::reports.dashboard.menu.orders'))
@section('page-title', __('reports::reports.dashboard.menu.orders'))

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title fw-bold">{{ __('reports::reports.dashboard.menu.orders') }}</h3>
            <div class="card-toolbar text-muted">{{ __('reports::reports.dashboard.categories.other.orders_desc') }}</div>
        </div>
        <div class="card-body">
            @php $cur = config('app.currency', 'KWD'); @endphp
            <div class="row g-4 mb-7">
                <div class="col-md-3">
                    <div class="p-4 border rounded-3 h-100 bg-light-primary">
                        <div class="text-gray-700 fw-semibold small">{{ __('reports::reports.dashboard.stats.total_orders') }}</div>
                        <div class="fs-2 fw-bold">{{ array_sum(array_column($byStatus, 'orders_count')) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    @php $paid = collect($byPaymentStatus)->firstWhere('payment_status', 'paid')['orders_count'] ?? 0; @endphp
                    <div class="p-4 border rounded-3 h-100 bg-light-success">
                        <div class="text-gray-700 fw-semibold small">{{ __('order::dashboard.orders.filters.paid') }}</div>
                        <div class="fs-2 fw-bold">{{ $paid }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    @php $pending = collect($byPaymentStatus)->firstWhere('payment_status', 'pending')['orders_count'] ?? 0; @endphp
                    <div class="p-4 border rounded-3 h-100 bg-light-warning">
                        <div class="text-gray-700 fw-semibold small">{{ __('order::dashboard.orders.filters.pending') }}</div>
                        <div class="fs-2 fw-bold">{{ $pending }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    @php $failed = collect($byPaymentStatus)->firstWhere('payment_status', 'failed')['orders_count'] ?? 0; @endphp
                    <div class="p-4 border rounded-3 h-100 bg-light-danger">
                        <div class="text-gray-700 fw-semibold small">{{ __('order::dashboard.orders.filters.failed') }}</div>
                        <div class="fs-2 fw-bold">{{ $failed }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header">
                            <h5 class="card-title fw-bold mb-0">{{ __('reports::reports.dashboard.stats.statuses') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            @if(!empty($byStatus))
                                <div class="table-responsive">
                                    <table class="table table-row-dashed align-middle mb-0">
                                        <thead class="text-muted">
                                            <tr>
                                                <th>{{ __('reports::reports.dashboard.stats.statuses') }}</th>
                                                <th class="text-end">{{ __('reports::reports.dashboard.stats.total_orders') }}</th>
                                                <th class="text-end">{{ __('reports::reports.dashboard.sales.table.revenue') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($byStatus as $row)
                                            <tr>
                                                <td>
                                                    <span class="badge rounded-1" style="background: {{ $row['status_color'] ?? '#eef2f7' }};color:#111;">
                                                        {{ $row['status_title'] ?? $row['status_code'] }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-semibold">{{ $row['orders_count'] }}</td>
                                                <td class="text-end fw-semibold">{{ number_format($row['total_revenue'], 3) }} {{ $cur }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-4 text-muted">{{ __('reports::reports.dashboard.sales.no_products') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header">
                            <h5 class="card-title fw-bold mb-0">{{ __('order::dashboard.orders.table.payment_status') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            @if(!empty($byPaymentStatus))
                                <div class="table-responsive">
                                    <table class="table table-row-dashed align-middle mb-0">
                                        <thead class="text-muted">
                                            <tr>
                                                <th>{{ __('order::dashboard.orders.table.payment_status') }}</th>
                                                <th class="text-end">{{ __('reports::reports.dashboard.stats.total_orders') }}</th>
                                                <th class="text-end">{{ __('reports::reports.dashboard.sales.table.revenue') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($byPaymentStatus as $row)
                                            <tr>
                                                <td class="fw-semibold text-capitalize">{{ $row['payment_status'] }}</td>
                                                <td class="text-end fw-semibold">{{ $row['orders_count'] }}</td>
                                                <td class="text-end fw-semibold">{{ number_format($row['total_amount'], 3) }} {{ $cur }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-4 text-muted">{{ __('reports::reports.dashboard.sales.no_products') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-6">
                <div class="card-header">
                    <h5 class="card-title fw-bold mb-0">{{ __('order::dashboard.orders.table.payment_status') }}: Failed</h5>
                </div>
                <div class="card-body p-0">
                    @if(!empty($paymentFailures))
                        <div class="table-responsive">
                            <table class="table table-row-dashed align-middle mb-0">
                                <thead class="text-muted">
                                    <tr>
                                        <th>ID</th>
                                        <th>{{ __('order::dashboard.orders.table.amount') }}</th>
                                        <th>{{ __('order::dashboard.orders.table.payment_status') }}</th>
                                        <th>{{ __('reports::reports.dashboard.financial.revenue_breakdown') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($paymentFailures as $fail)
                                    <tr>
                                        <td>#{{ $fail['payment_id'] }}</td>
                                        <td>{{ number_format($fail['amount'], 3) }} {{ $fail['currency'] }}</td>
                                        <td>{{ $fail['provider'] }}</td>
                                        <td class="text-muted">{{ $fail['failure_reason'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-muted">{{ __('reports::reports.dashboard.sales.no_products') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
