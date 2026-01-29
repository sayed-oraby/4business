@extends('layouts.dashboard.master')

@section('title', __('reports::reports.dashboard.sales.title'))
@section('page-title', __('reports::reports.dashboard.sales.title'))

@push('styles')
<style>
    .reports-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1.5rem;
    }
    .chart-container {
        min-height: 350px;
    }
</style>
@endpush

@section('content')
    <!-- Hero Section -->
    <div class="card border-0 shadow-sm mb-10 reports-hero">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-7">
                <div>
                    <span class="badge badge-light-white mb-3">{{ __('reports::reports.dashboard.sales.badge') }}</span>
                    <h2 class="fw-bold text-white mb-2">{{ __('reports::reports.dashboard.sales.title') }}</h2>
                    <p class="text-white opacity-75 mb-0">{{ __('reports::reports.dashboard.sales.description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card card-flush shadow-sm mb-10">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('reports::reports.dashboard.filters.title') }}</h3>
        </div>
        <div class="card-body pt-0">
            <form id="salesReportFilters" class="row g-4">
                <div class="col-md-3">
                    <label class="form-label">{{ __('reports::reports.dashboard.filters.period') }}</label>
                    <select name="period" class="form-select form-select-solid">
                        <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>{{ __('reports::reports.dashboard.filters.daily') }}</option>
                        <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>{{ __('reports::reports.dashboard.filters.weekly') }}</option>
                        <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>{{ __('reports::reports.dashboard.filters.monthly') }}</option>
                        <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>{{ __('reports::reports.dashboard.filters.yearly') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('reports::reports.dashboard.filters.start_date') }}</label>
                    <input type="date" name="start_date" class="form-control form-control-solid" value="{{ $startDate?->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('reports::reports.dashboard.filters.end_date') }}</label>
                    <input type="date" name="end_date" class="form-control form-control-solid" value="{{ $endDate?->format('Y-m-d') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">{{ __('reports::reports.dashboard.filters.apply') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-5 g-xl-8 mb-10">
        <div class="col-xl-3">
            <div class="card bg-body hoverable card-xl-stretch">
                <div class="card-body">
                    <i class="ki-outline ki-dollar text-primary fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">{{ number_format($stats['total_sales'] ?? 0, 3) }} KWD</div>
                    <div class="fw-semibold text-gray-500">{{ __('reports::reports.dashboard.sales.total_sales') }}</div>
                    @if(isset($stats['growth_percentage']))
                    <div class="d-flex align-items-center mt-2">
                        @php
                            $growth = $stats['growth_percentage'];
                            $growthClass = $growth >= 0 ? 'text-success' : 'text-danger';
                            $growthIcon = $growth >= 0 ? 'ki-arrow-up' : 'ki-arrow-down';
                        @endphp
                        <i class="ki-outline {{ $growthIcon }} fs-5 {{ $growthClass }} me-1"></i>
                        <span class="fw-semibold {{ $growthClass }}">{{ number_format(abs($growth), 1) }}%</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card bg-body hoverable card-xl-stretch">
                <div class="card-body">
                    <i class="ki-outline ki-shopping-cart text-success fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">{{ $stats['orders_count'] ?? 0 }}</div>
                    <div class="fw-semibold text-gray-500">{{ __('reports::reports.dashboard.sales.orders_count') }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card bg-body hoverable card-xl-stretch">
                <div class="card-body">
                    <i class="ki-outline ki-chart text-info fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">{{ number_format($stats['average_order_value'] ?? 0, 3) }} KWD</div>
                    <div class="fw-semibold text-gray-500">{{ __('reports::reports.dashboard.sales.average_order_value') }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card bg-body hoverable card-xl-stretch">
                <div class="card-body">
                    <i class="ki-outline ki-chart-simple text-warning fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">{{ number_format(($stats['previous_period']['total_sales'] ?? 0), 3) }} KWD</div>
                    <div class="fw-semibold text-gray-500">{{ __('reports::reports.dashboard.sales.previous_period') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-5 g-xl-8 mb-10">
        <!-- Sales by Time Chart -->
        <div class="col-xl-8">
            <div class="card card-flush shadow-sm">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold">{{ __('reports::reports.dashboard.sales.charts.time_series') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div id="salesTimeChart" class="chart-container"></div>
                </div>
            </div>
        </div>

        <!-- Sales by Payment Method -->
        <div class="col-xl-4">
            <div class="card card-flush shadow-sm">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold">{{ __('reports::reports.dashboard.sales.charts.payment_methods') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div id="paymentMethodsChart" class="chart-container"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-5 g-xl-8">
        <!-- Top Products -->
        <div class="col-xl-6">
            <div class="card card-flush shadow-sm">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold">{{ __('reports::reports.dashboard.sales.top_products') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-4">
                            <thead>
                            <tr class="text-muted text-uppercase">
                                <th>{{ __('reports::reports.dashboard.sales.table.product') }}</th>
                                <th class="text-end">{{ __('reports::reports.dashboard.sales.table.quantity') }}</th>
                                <th class="text-end">{{ __('reports::reports.dashboard.sales.table.revenue') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($byProduct as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $product['title'] }}</span>
                                            <span class="text-muted fs-7">SKU: {{ $product['sku'] }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end">{{ $product['total_qty'] }}</td>
                                    <td class="text-end fw-bold">{{ number_format($product['total_revenue'], 3) }} KWD</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">{{ __('reports::reports.dashboard.sales.no_products') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales by Country -->
        <div class="col-xl-6">
            <div class="card card-flush shadow-sm">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold">{{ __('reports::reports.dashboard.sales.by_country') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-4">
                            <thead>
                            <tr class="text-muted text-uppercase">
                                <th>{{ __('reports::reports.dashboard.sales.table.country') }}</th>
                                <th class="text-end">{{ __('reports::reports.dashboard.sales.table.orders') }}</th>
                                <th class="text-end">{{ __('reports::reports.dashboard.sales.table.revenue') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($byCountry as $country)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $country['country'] }}</span>
                                    </td>
                                    <td class="text-end">{{ $country['orders_count'] }}</td>
                                    <td class="text-end fw-bold">{{ number_format($country['total_revenue'], 3) }} KWD</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">{{ __('reports::reports.dashboard.sales.no_countries') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.SalesReport = {
            routes: {
                stats: "{{ route('dashboard.reports.sales.stats') }}",
                byTime: "{{ route('dashboard.reports.sales.by-time') }}",
                byPaymentMethod: "{{ route('dashboard.reports.sales.by-payment-method') }}",
            },
            data: {
                timeSeries: @json($byTime),
                paymentMethods: @json($byPaymentMethod),
            }
        };
    </script>
    @vite('resources/js/app.js')
    <script src="{{ asset('Modules/Reports/resources/assets/js/sales-report.js') }}"></script>
@endpush

