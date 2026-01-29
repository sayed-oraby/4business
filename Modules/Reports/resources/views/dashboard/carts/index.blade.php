@extends('layouts.dashboard.master')

@section('title', __('reports::reports.dashboard.carts.title'))
@section('page-title', __('reports::reports.dashboard.carts.title'))

@section('content')
    <div class="card shadow-sm mb-6">
        <div class="card-header">
            <h3 class="card-title fw-bold">{{ __('reports::reports.dashboard.carts.title') }}</h3>
            <div class="card-toolbar text-muted">{{ __('reports::reports.dashboard.carts.subtitle') }}</div>
        </div>
        <div class="card-body">
            <div class="row g-4 mb-7">
                @php $cur = config('app.currency', 'KWD'); @endphp
                <div class="col-md-3">
                    <div class="p-4 border rounded-3 h-100 bg-light">
                        <div class="text-gray-700 fw-semibold small">{{ __('reports::reports.dashboard.carts.total') }}</div>
                        <div class="fs-2 fw-bold">{{ $stats['total'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 border rounded-3 h-100 bg-light-primary">
                        <div class="text-gray-700 fw-semibold small">{{ __('reports::reports.dashboard.carts.active') }}</div>
                        <div class="fs-2 fw-bold">{{ $stats['active'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 border rounded-3 h-100 bg-light-warning">
                        <div class="text-gray-700 fw-semibold small">{{ __('reports::reports.dashboard.carts.abandoned') }}</div>
                        <div class="fs-2 fw-bold">{{ $stats['abandoned'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 border rounded-3 h-100 bg-light-danger">
                        <div class="text-gray-700 fw-semibold small">{{ __('reports::reports.dashboard.carts.expired') }}</div>
                        <div class="fs-2 fw-bold">{{ $stats['expired'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-7">
                <div class="col-md-4">
                    <div class="p-4 border rounded-3 h-100 bg-light">
                        <div class="text-gray-700 fw-semibold small">{{ __('reports::reports.dashboard.carts.checked_out') }}</div>
                        <div class="fs-2 fw-bold">{{ $stats['checked_out'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border rounded-3 h-100 bg-light">
                        <div class="text-gray-700 fw-semibold small">{{ __('reports::reports.dashboard.carts.total_value') }}</div>
                        <div class="fs-2 fw-bold">{{ number_format($stats['total_value'] ?? 0, 3) }} {{ $cur }}</div>
                        <div class="text-muted small">{{ __('reports::reports.dashboard.carts.average_value') }}: {{ number_format($stats['average_value'] ?? 0, 3) }} {{ $cur }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border rounded-3 h-100 bg-light">
                        <div class="text-gray-700 fw-semibold small">{{ __('reports::reports.dashboard.carts.average_items') }}</div>
                        <div class="fs-2 fw-bold">{{ number_format($stats['average_items'] ?? 0, 1) }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-7">
                <div class="card-header d-flex flex-wrap gap-3 align-items-center">
                    <h5 class="card-title fw-bold mb-0">{{ __('reports::reports.dashboard.carts.incentives') }}</h5>
                    <form class="d-flex flex-wrap gap-3 align-items-center ms-auto" method="post" action="{{ route('dashboard.reports.carts.notify') }}">
                        @csrf
                        <input type="hidden" name="status" value="abandoned">
                        <select name="channel" class="form-select form-select-sm w-auto">
                            <option value="email">Email</option>
                            <option value="fcm">FCM</option>
                        </select>
                        <input type="text" name="coupon_code" class="form-control form-control-sm w-auto" placeholder="{{ __('reports::reports.dashboard.carts.coupon_placeholder') }}">
                        <input type="text" name="message" class="form-control form-control-sm w-250px" placeholder="{{ __('reports::reports.dashboard.carts.message_placeholder') }}" required>
                        <button class="btn btn-sm btn-primary" type="submit">{{ __('reports::reports.dashboard.carts.send') }}</button>
                    </form>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">{{ __('reports::reports.dashboard.carts.incentives_hint') }}</p>
                </div>
            </div>

            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header">
                            <h5 class="card-title fw-bold mb-0">{{ __('reports::reports.dashboard.carts.abandoned_list') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            @if(!empty($abandoned))
                                <div class="table-responsive">
                                    <table class="table table-row-dashed align-middle mb-0">
                                        <thead class="text-muted">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('reports::reports.dashboard.carts.user') }}</th>
                                                <th class="text-end">{{ __('reports::reports.dashboard.sales.table.revenue') }}</th>
                                                <th class="text-end">{{ __('reports::reports.dashboard.sales.table.quantity') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($abandoned as $cart)
                                            <tr>
                                                <td class="fw-semibold">{{ $cart['id'] }}</td>
                                                <td>{{ $cart['user'] }}</td>
                                                <td class="text-end fw-semibold">{{ number_format($cart['grand_total'], 3) }} {{ $cart['currency'] }}</td>
                                                <td class="text-end">{{ $cart['items_count'] }}</td>
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
                            <h5 class="card-title fw-bold mb-0">{{ __('reports::reports.dashboard.carts.active_list') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            @if(!empty($active))
                                <div class="table-responsive">
                                    <table class="table table-row-dashed align-middle mb-0">
                                        <thead class="text-muted">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('reports::reports.dashboard.carts.user') }}</th>
                                                <th class="text-end">{{ __('reports::reports.dashboard.sales.table.revenue') }}</th>
                                                <th class="text-end">{{ __('reports::reports.dashboard.sales.table.quantity') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($active as $cart)
                                            <tr>
                                                <td class="fw-semibold">{{ $cart['id'] }}</td>
                                                <td>{{ $cart['user'] }}</td>
                                                <td class="text-end fw-semibold">{{ number_format($cart['grand_total'], 3) }} {{ $cart['currency'] }}</td>
                                                <td class="text-end">{{ $cart['items_count'] }}</td>
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
                    <h5 class="card-title fw-bold mb-0">{{ __('reports::reports.dashboard.carts.latest') }}</h5>
                </div>
                <div class="card-body p-0">
                    @if(!empty($latest))
                        <div class="table-responsive">
                            <table class="table table-row-dashed align-middle mb-0">
                                <thead class="text-muted">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('reports::reports.dashboard.carts.user') }}</th>
                                        <th>{{ __('reports::reports.dashboard.carts.status') }}</th>
                                        <th class="text-end">{{ __('reports::reports.dashboard.sales.table.revenue') }}</th>
                                        <th class="text-end">{{ __('reports::reports.dashboard.sales.table.quantity') }}</th>
                                        <th class="text-end">{{ __('reports::reports.dashboard.carts.updated_at') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($latest as $cart)
                                    <tr>
                                        <td>{{ $cart['id'] }}</td>
                                        <td>{{ $cart['user'] }}</td>
                                        <td class="text-capitalize">{{ $cart['status'] }}</td>
                                        <td class="text-end">{{ number_format($cart['grand_total'], 3) }} {{ $cart['currency'] }}</td>
                                        <td class="text-end">{{ $cart['items_count'] }}</td>
                                        <td class="text-end">{{ $cart['updated_at'] }}</td>
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
