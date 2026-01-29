@extends('layouts.dashboard.master')

@section('title', __('reports::reports.dashboard.menu.financial'))
@section('page-title', __('reports::reports.dashboard.menu.financial'))

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title fw-bold">{{ __('reports::reports.dashboard.menu.financial') }}</h3>
            <div class="card-toolbar text-muted">{{ __('reports::reports.dashboard.categories.other.financial_desc') }}</div>
        </div>
        <div class="card-body">
            <div class="row g-4 mb-7">
                @php $cur = config('app.currency', 'KWD'); @endphp
                <div class="col-md-3">
                    <div class="fs-3 fw-bold">{{ number_format($revenue['gross_revenue'] ?? 0, 3) }} {{ $cur }}</div>
                    <div class="text-muted">{{ __('reports::reports.dashboard.financial.gross_revenue') }}</div>
                </div>
                <div class="col-md-3">
                    <div class="fs-3 fw-bold">{{ number_format($revenue['net_revenue'] ?? 0, 3) }} {{ $cur }}</div>
                    <div class="text-muted">{{ __('reports::reports.dashboard.financial.net_revenue') }}</div>
                </div>
                <div class="col-md-3">
                    <div class="fs-3 fw-bold">{{ number_format($revenue['total_tax'] ?? 0, 3) }} {{ $cur }}</div>
                    <div class="text-muted">{{ __('reports::reports.dashboard.financial.tax_collected') }}</div>
                </div>
                <div class="col-md-3">
                    <div class="fs-3 fw-bold">{{ number_format($revenue['profit'] ?? 0, 3) }} {{ $cur }}</div>
                    <div class="text-muted">{{ __('reports::reports.dashboard.financial.profit') }}</div>
                    <div class="text-muted small">{{ number_format($revenue['profit_margin'] ?? 0, 1) }}%</div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <h5 class="fw-semibold mb-3">{{ __('reports::reports.dashboard.financial.revenue_breakdown') }}</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between"><span>{{ __('reports::reports.dashboard.financial.shipping') }}</span><span>{{ number_format($revenue['total_shipping'] ?? 0, 3) }} {{ $cur }}</span></li>
                        <li class="d-flex justify-content-between"><span>{{ __('reports::reports.dashboard.financial.discounts') }}</span><span>{{ number_format($revenue['total_discounts'] ?? 0, 3) }} {{ $cur }}</span></li>
                        <li class="d-flex justify-content-between"><span>{{ __('reports::reports.dashboard.financial.payment_fees') }}</span><span>{{ number_format($revenue['payment_fees'] ?? 0, 3) }} {{ $cur }}</span></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5 class="fw-semibold mb-3">{{ __('reports::reports.dashboard.financial.tax_by_country') }}</h5>
                    @if(!empty($tax))
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead class="text-muted">
                                    <tr>
                                        <th>{{ __('reports::reports.dashboard.financial.country') }}</th>
                                        <th>{{ __('reports::reports.dashboard.financial.orders') }}</th>
                                        <th class="text-end">{{ __('reports::reports.dashboard.financial.tax') }}</th>
                                        <th class="text-end">{{ __('reports::reports.dashboard.financial.rate') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($tax as $row)
                                    <tr>
                                        <td>{{ $row['country'] ?? '—' }}</td>
                                        <td>{{ $row['orders_count'] ?? 0 }}</td>
                                        <td class="text-end">{{ number_format($row['total_tax'] ?? 0, 3) }} {{ $cur }}</td>
                                        <td class="text-end">{{ number_format($row['tax_rate'] ?? 0, 2) }}%</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">{{ __('reports::reports.dashboard.financial.no_tax') }}</div>
                    @endif
                </div>
            </div>

            <h5 class="fw-semibold mb-3 mt-6">{{ __('reports::reports.dashboard.financial.wallet') }}</h5>
            <div class="alert alert-warning">
                {{ $wallet['message'] ?? __('reports::reports.dashboard.financial.wallet_placeholder') }}
            </div>
        </div>
    </div>
@endsection
