@extends('layouts.frontend.master')

@section('title', __('frontend.agents.title'))
@section('body-class', 'page-agents')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/agents.css') }}">
@endpush

@section('content')
    <main class="p-agents">
        <div class="l-container">
            <nav class="c-breadcrumb">
                <a href="{{ route('frontend.home') }}" class="c-breadcrumb__link">{{ __('frontend.nav.home') }}</a>
                <span class="c-breadcrumb__separator">›</span>
                <span class="c-breadcrumb__current">{{ __('frontend.agents.title') }}</span>
            </nav>

            <div class="l-page-header">
                <h1 class="l-page-header__title">{{ __('frontend.agents.title') }}</h1>
                <p class="l-page-header__count">({{ __('frontend.listings.result_count', ['count' => $agents->total()]) }})</p>
            </div>

            <div class="p-agents__grid">
                @forelse($agents as $agent)
                    <a href="{{ route('frontend.agents.show', $agent->slug ?? $agent->id) }}" class="p-agents__card">
                        <div class="p-agents__avatar">
                            @if($agent->avatar)
                                <img src="{{ asset('storage/' . $agent->avatar) }}" alt="{{ $agent->name }}" class="p-agents__avatar-img">
                            @else
                                <svg class="p-agents__avatar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            @endif
                            @if($agent->is_verified)
                                <span class="p-agents__verified" title="{{ __('frontend.agents.verified') }}">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2L3 7l1.63 12L12 22l7.37-3L21 7l-9-5zm-1 15l-4-4 1.41-1.41L11 14.17l5.59-5.59L18 10l-7 7z"/>
                                    </svg>
                                </span>
                            @endif
                        </div>
                        <div class="p-agents__info">
                            <h3 class="p-agents__name">{{ $agent->name }}</h3>
                            @if($agent->company_name)
                                <p class="p-agents__office">{{ $agent->company_name }}</p>
                            @endif
                            <p class="p-agents__count">{{ __('frontend.agents.listings_count', ['count' => $agent->posts_count]) }}</p>
                        </div>
                    </a>
                @empty
                    <div class="u-text-center u-p-24" style="grid-column: 1 / -1;">
                        <p class="u-text-muted">{{ __('frontend.listings.no_results') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="u-mt-auto">
                {{ $agents->links() }}
            </div>
        </div>
    </main>
@endsection

