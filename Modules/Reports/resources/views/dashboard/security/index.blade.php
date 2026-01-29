@extends('layouts.dashboard.master')

@section('title', __('reports::reports.dashboard.menu.security'))
@section('page-title', __('reports::reports.dashboard.menu.security'))

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title fw-bold">{{ __('reports::reports.dashboard.menu.security') }}</h3>
            <div class="card-toolbar text-muted">{{ __('reports::reports.dashboard.categories.other.security_desc') }}</div>
        </div>
        <div class="card-body">
            <h6 class="fw-semibold mb-3">Login attempts</h6>
            <pre class="bg-light p-3 rounded">{{ json_encode($loginAttempts ?? [], JSON_PRETTY_PRINT) }}</pre>

            <h6 class="fw-semibold mb-3 mt-6">Bans</h6>
            <pre class="bg-light p-3 rounded">{{ json_encode($bans ?? [], JSON_PRETTY_PRINT) }}</pre>

            <h6 class="fw-semibold mb-3 mt-6">Admin actions</h6>
            <pre class="bg-light p-3 rounded">{{ json_encode($adminActions ?? [], JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
@endsection
