@extends('layouts.dashboard.master')

@section('title', __('reports::reports.dashboard.menu.products'))
@section('page-title', __('reports::reports.dashboard.menu.products'))

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title fw-bold">{{ __('reports::reports.dashboard.menu.products') }}</h3>
            <div class="card-toolbar text-muted">{{ __('reports::reports.dashboard.categories.other.products_desc') }}</div>
        </div>
        <div class="card-body">
            <div class="row g-4 mb-8">
                @php
                    $available = $inventory['available'] ?? 0;
                    $lowStock = $inventory['low_stock'] ?? 0;
                    $outOfStock = $inventory['out_of_stock'] ?? 0;
                    $total = max($inventory['total'] ?? 0, 1);
                @endphp
                <div class="col-md-3">
                    <div class="p-4 border rounded-3 h-100 bg-light-primary">
                        <div class="text-gray-700 fw-semibold small">{{ __('reports::reports.dashboard.stats.available_products') }}</div>
                        <div class="fs-2 fw-bold">{{ $available }}</div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ ($available/$total)*100 }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 border rounded-3 h-100 bg-light-warning">
                        <div class="text-gray-700 fw-semibold small">{{ __('reports::reports.dashboard.stats.low_stock') }}</div>
                        <div class="fs-2 fw-bold">{{ $lowStock }}</div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: {{ ($lowStock/$total)*100 }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 border rounded-3 h-100 bg-light-danger">
                        <div class="text-gray-700 fw-semibold small">{{ __('reports::reports.dashboard.stats.statuses') }}</div>
                        <div class="fs-2 fw-bold">{{ $outOfStock }}</div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-danger" style="width: {{ ($outOfStock/$total)*100 }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 border rounded-3 h-100 bg-light">
                        <div class="text-gray-700 fw-semibold small">{{ __('reports::reports.dashboard.stats.total_orders') }}</div>
                        <div class="fs-2 fw-bold">{{ $inventory['total'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="card-title fw-bold mb-0">{{ __('reports::reports.dashboard.stats.low_stock') }}</h5>
                </div>
                <div class="card-body p-0">
                    @if(!empty($inventory['low_stock_products']))
                        <div class="table-responsive">
                            <table class="table table-row-dashed align-middle mb-0">
                                <thead class="text-muted">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('reports::reports.dashboard.sales.table.product') }}</th>
                                        <th>SKU</th>
                                        <th class="text-end">{{ __('dashboard.available_products') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($inventory['low_stock_products'] as $product)
                                    <tr>
                                        <td class="fw-semibold">{{ $product['id'] }}</td>
                                        <td>{{ $product['title'] }}</td>
                                        <td class="text-muted">{{ $product['sku'] }}</td>
                                        <td class="text-end fw-bold text-danger">{{ $product['qty'] }}</td>
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
