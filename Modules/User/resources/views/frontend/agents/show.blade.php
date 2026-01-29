@extends('layouts.frontend.master')

@section('title', $agent->name)
@section('body-class', 'page-agent')

@php($v = '1.5.0')

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/css/pages/agent.css') }}?v={{ $v }}">
@endpush

@section('content')
<main class="p-agent">
    <div class="l-container">
        <nav class="c-breadcrumb">
            <a href="{{ route('frontend.home') }}" class="c-breadcrumb__link">{{ __('frontend.nav.home') }}</a>
            <span class="c-breadcrumb__separator">›</span>
            <a href="{{ route('frontend.agents.index') }}" class="c-breadcrumb__link">{{ __('frontend.agents.title') }}</a>
            <span class="c-breadcrumb__separator">›</span>
            <span class="c-breadcrumb__current">{{ $agent->name }}</span>
        </nav>

        <div class="p-agent__header">
            <div class="p-agent__avatar">
                @if($agent->avatar)
                <img src="{{ asset('storage/' . $agent->avatar) }}" alt="{{ $agent->name }}" class="p-agent__avatar-img">
                @else
                <svg class="p-agent__avatar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                @endif
            </div>
            <div class="p-agent__info">
                <h1 class="p-agent__name">
                    {{ $agent->name }}
                    @if($agent->is_verified)
                    <span class="p-agent__verified" title="{{ __('frontend.agents.verified') }}">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L3 7l1.63 12L12 22l7.37-3L21 7l-9-5zm-1 15l-4-4 1.41-1.41L11 14.17l5.59-5.59L18 10l-7 7z" />
                        </svg>
                    </span>
                    @endif
                </h1>
                @if($agent->office_name)
                <p class="p-agent__office">{{ $agent->office_name }}</p>
                @endif
                <p class="p-agent__stats">{{ $totalPosts }} {{ __('frontend.agents.listings') }}</p>

                <div class="p-agent__actions">
                    @if($agent->phone)
                    <a href="tel:{{ $agent->phone }}" class="c-btn c-btn--outline">
                        <svg class="c-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72" />
                        </svg>
                        {{ __('frontend.ad.call') }}
                    </a>
                    @endif
                    @if($agent->whatsapp ?? $agent->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $agent->whatsapp ?? $agent->phone) }}" target="_blank" class="c-btn c-btn--primary">
                        <svg class="c-btn__icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347" />
                        </svg>
                        {{ __('frontend.ad.whatsapp') }}
                    </a>
                    @endif
                </div>
            </div>
        </div>

        @if($agent->bio)
        <div class="p-agent__bio">
            <p>{{ $agent->bio }}</p>
        </div>
        @endif

        <div class="p-agent__listings">
            <div class="l-section__header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-20);">
                <h2 class="l-section__title" style="margin-bottom: 0;">{{ __('frontend.agents.agent_listings') }}</h2>
                <span class="l-section__count">{{ $totalPosts }} {{ __('frontend.listings.results') }}</span>
            </div>

            <div class="l-listings-stack" id="agentListings">
                @forelse($posts as $post)
                @include('post::frontend.partials.post-card-horizontal', ['post' => $post])
                @empty
                <div class="u-text-center u-p-24">
                    <p class="u-text-muted">{{ __('frontend.listings.no_results') }}</p>
                </div>
                @endforelse
            </div>

            @if($hasMore)
            <div class="l-load-more" id="loadMoreContainer">
                <button type="button" class="l-load-more__btn" id="loadMoreBtn" data-page="1" data-agent="{{ $agent->id }}">
                    <span class="l-load-more__text">{{ __('frontend.load_more') }}</span>
                    <span class="l-load-more__spinner" style="display: none;">
                        <svg class="l-load-more__spinner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" stroke-dasharray="32" stroke-dashoffset="32">
                                <animate attributeName="stroke-dashoffset" values="32;0" dur="1s" repeatCount="indefinite"/>
                            </circle>
                        </svg>
                    </span>
                </button>
            </div>
            @endif
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
(function() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const listingsContainer = document.getElementById('agentListings');
    const loadMoreContainer = document.getElementById('loadMoreContainer');

    if (!loadMoreBtn) return;

    loadMoreBtn.addEventListener('click', function() {
        const page = parseInt(this.dataset.page) + 1;
        const agentId = this.dataset.agent;
        const textEl = this.querySelector('.l-load-more__text');
        const spinnerEl = this.querySelector('.l-load-more__spinner');

        // Count existing cards
        const existingCardsCount = listingsContainer.querySelectorAll('.c-card').length;

        // Show loading state
        textEl.style.display = 'none';
        spinnerEl.style.display = 'inline-flex';
        loadMoreBtn.disabled = true;

        fetch(`/agents/load-more/${agentId}?page=${page}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Accept-Language': '{{ app()->getLocale() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Append new posts
            listingsContainer.insertAdjacentHTML('beforeend', data.html);

            // Update page number
            loadMoreBtn.dataset.page = page;

            // Scroll to first new card
            const allCards = listingsContainer.querySelectorAll('.c-card');
            if (allCards.length > existingCardsCount) {
                const firstNewCard = allCards[existingCardsCount];
                if (firstNewCard) {
                    setTimeout(() => {
                        const yOffset = -100;
                        const y = firstNewCard.getBoundingClientRect().top + window.pageYOffset + yOffset;
                        window.scrollTo({ top: y, behavior: 'smooth' });
                    }, 100);
                }
            }

            // Hide button if no more posts
            if (!data.hasMore) {
                loadMoreContainer.style.display = 'none';
            }

            // Reset button state
            textEl.style.display = 'inline';
            spinnerEl.style.display = 'none';
            loadMoreBtn.disabled = false;
        })
        .catch(error => {
            console.error('Error loading more posts:', error);
            textEl.style.display = 'inline';
            spinnerEl.style.display = 'none';
            loadMoreBtn.disabled = false;
        });
    });
})();
</script>
@endpush
