@extends('layouts.dashboard.master')

@section('title', __('reports::reports.dashboard.menu.users'))
@section('page-title', __('reports::reports.dashboard.menu.users'))

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title fw-bold">{{ __('reports::reports.dashboard.menu.users') }}</h3>
            <div class="card-toolbar text-muted">{{ __('reports::reports.dashboard.categories.other.users_desc') }}</div>
        </div>
        <div class="card-body">
            <div class="row g-4 mb-6">
                <div class="col-md-4">
                    <div class="fs-3 fw-bold">{{ $signups['total_signups'] ?? 0 }}</div>
                    <div class="text-muted">{{ __('reports::reports.dashboard.menu.users') }}</div>
                </div>
            </div>

            <h6 class="fw-semibold mb-3">Top buyers</h6>
            @if(!empty($topBuyers))
                <ul class="list-unstyled">
                    @foreach($topBuyers as $buyer)
                        <li>#{{ $buyer['user_id'] }} — {{ $buyer['name'] }} ({{ $buyer['email'] }}) — {{ number_format($buyer['total_spent'], 3) }} {{ $buyer['average_order_value'] ? ' / AOV '.number_format($buyer['average_order_value'], 3) : '' }}</li>
                    @endforeach
                </ul>
            @else
                <div class="text-muted">—</div>
            @endif

            <h6 class="fw-semibold mt-6 mb-3">Behavior</h6>
            <pre class="bg-light p-3 rounded">{{ json_encode($behavior ?? [], JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
@endsection
