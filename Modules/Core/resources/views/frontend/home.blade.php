@extends('layouts.frontend.master')

@section('title', __('frontend.home.title'))
@section('body-class', 'page-home')

@push('styles')
@php($v = '1.5.0')
<link rel="stylesheet" href="{{ asset('frontend/css/pages/home.css') }}?v={{ $v }}">
@if($banners->count() > 0)
<link rel="stylesheet" href="{{ asset('frontend/css/components/banner-carousel.css') }}?v={{ $v }}">
@endif
@endpush

@section('content')
<!-- ========== BANNER SECTION ========== -->
@include('core::frontend.partials.banners')

<!-- ========== HERO SECTION ========== -->
<section class="p-hero">
    <img src="{{ setting_media_url($appSettings['hero_background'] ?? null, asset('frontend/img/background.png')) }}" alt="" class="p-hero__bg-img">
    <div class="l-container">
        <div class="p-hero__content">
            <h1 class="p-hero__title">{{ __('frontend.home.title') }}</h1>
            <p class="p-hero__subtitle">{{ __('frontend.home.subtitle') }}</p>

            <!-- Search Box -->
            <form action="{{ route('frontend.posts.index') }}" method="GET" class="p-hero__search" id="homeSearchForm">
                <!-- Text Search -->
                <div class="p-hero__search-input">
                    <svg class="p-hero__search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input type="text" name="q" class="p-hero__input" placeholder="{{ __('frontend.home.search_placeholder') }}">
                </div>

                <!-- Post Type Tabs -->
                <div class="p-hero__tabs">
                    @foreach($postTypes as $postType)
                    <button type="button" class="p-hero__tab" data-tab="{{ $postType->slug }}">
                        {{ $postType->name }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="type" id="selectedPostType" value="">

                <!-- Dropdowns Row -->
                <div class="p-hero__filters">
                    <!-- Category Dropdown -->
                    <div class="p-hero__dropdown" id="categoryDropdown">
                        <button type="button" class="p-hero__dropdown-btn" id="categoryBtn">
                            <span class="p-hero__dropdown-text">{{ __('frontend.home.property_type') }}</span>
                            <svg class="p-hero__dropdown-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </button>
                        <div class="p-hero__dropdown-menu">
                            <div class="p-hero__dropdown-header">
                                <span>{{ __('frontend.home.property_type') }}</span>
                                <button type="button" class="p-hero__dropdown-close" onclick="closeAllDropdowns()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                            <div class="p-hero__dropdown-search">
                                <input type="text" placeholder="بحث..." onkeyup="filterList(this, 'categoryList')">
                            </div>
                            <div class="p-hero__dropdown-list" id="categoryList">
                                @foreach($categories as $category)
                                <div class="p-hero__dropdown-item" data-value="{{ $category->slug }}" data-name="{{ $category->name }}">
                                    <span>{{ $category->name }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="category" id="categoryInput">

                    <!-- Location Dropdown -->
                    <div class="p-hero__dropdown" id="locationDropdown">
                        <button type="button" class="p-hero__dropdown-btn" id="locationBtn">
                            <span class="p-hero__dropdown-text" id="locationLabel">{{ __('frontend.home.select_location') }}</span>
                            <svg class="p-hero__dropdown-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </button>
                        <div class="p-hero__dropdown-menu p-hero__dropdown-menu--locations">
                            <div class="p-hero__dropdown-header">
                                <span>{{ __('frontend.home.select_location') }}</span>
                                <button type="button" class="p-hero__dropdown-close" onclick="closeAllDropdowns()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                            <div class="p-hero__dropdown-search">
                                <input type="text" placeholder="بحث عن محافظة أو منطقة..." id="locationSearchInput" onkeyup="filterLocations(this)">
                            </div>
                            <div class="p-hero__dropdown-list p-hero__locations-list" id="locationList">
                                @foreach($locations as $state)
                                <div class="p-hero__location-item p-hero__location-item--state" data-type="state" data-id="{{ $state->id }}" data-name="{{ $state->name_ar }}">
                                    <input type="checkbox" class="p-hero__location-checkbox state-checkbox" data-state="{{ $state->id }}">
                                    <span class="p-hero__location-name">{{ $state->name_ar }}</span>
                                    <span class="p-hero__location-count">({{ $state->cities_count }})</span>
                                </div>
                                @foreach($state->cities as $city)
                                <div class="p-hero__location-item p-hero__location-item--city" data-type="city" data-id="{{ $city->id }}" data-name="{{ $city->name_ar }}" data-state="{{ $state->id }}">
                                    <input type="checkbox" class="p-hero__location-checkbox city-checkbox" data-state="{{ $state->id }}" data-city="{{ $city->id }}">
                                    <span class="p-hero__location-name">{{ $city->name_ar }}</span>
                                </div>
                                @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="c-btn c-btn--primary c-btn--lg p-hero__search-btn">
                    <svg class="c-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    {{ __('frontend.search') }}
                </button>

                <!-- Selected Location Tags Row -->
                <div class="p-hero__tags-row" id="tagsRow"></div>
                <div id="locationInputs"></div>
            </form>

            <!-- Mobile Backdrop -->
            <div class="p-hero__backdrop" id="heroBackdrop"></div>
        </div>
    </div>
</section>

<!-- ========== LATEST LISTINGS SECTION ========== -->
<section class="l-section">
    <div class="l-container">
        <div class="l-section__header">
            <h2 class="l-section__title">{{ __('frontend.home.latest_listings') }}</h2>
            <span class="l-section__count">({{ number_format($totalPosts) }} {{ __('frontend.listings.count_suffix') }})</span>
        </div>

        <div class="l-listings-stack" id="latestListings">
            @forelse($latestPosts as $post)
            @include('post::frontend.partials.post-card-horizontal', ['post' => $post])
            @empty
            <p class="u-text-center u-text-muted">{{ __('frontend.listings.no_results') }}</p>
            @endforelse
        </div>

        @if($latestPosts->count() < $totalPosts)
        <div class="l-load-more" id="loadMoreContainer">
            <button type="button" class="l-load-more__btn" id="loadMoreBtn" data-page="1" data-url="{{ route('frontend.home.loadMore') }}">
                <span class="l-load-more__text">{{ __('frontend.load_more') }}</span>
                <span class="l-load-more__spinner" style="display: none;">
                    <svg class="l-load-more__spinner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" stroke-dasharray="60" stroke-dashoffset="20"/>
                    </svg>
                </span>
            </button>
        </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
@php($v = '1.5.0')
<script src="{{ asset('frontend/js/pages/home.js') }}?v={{ $v }}"></script>
@if($banners->count() > 0)
<script src="{{ asset('frontend/js/components/banner-carousel.js') }}?v={{ $v }}"></script>
@endif
<script>
// Load More Posts
(function() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const listingsContainer = document.getElementById('latestListings');
    const loadMoreContainer = document.getElementById('loadMoreContainer');
    
    if (!loadMoreBtn) return;
    
    loadMoreBtn.addEventListener('click', function() {
        const page = parseInt(this.dataset.page) + 1;
        const url = this.dataset.url;
        const textEl = this.querySelector('.l-load-more__text');
        const spinnerEl = this.querySelector('.l-load-more__spinner');
        
        // Count existing cards before adding new ones
        const existingCardsCount = listingsContainer.querySelectorAll('.c-card').length;
        
        // Show loading state
        textEl.style.display = 'none';
        spinnerEl.style.display = 'inline-flex';
        loadMoreBtn.disabled = true;
        
        fetch(`${url}?page=${page}`, {
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
            
            // Scroll to first NEW card (not the last one)
            const allCards = listingsContainer.querySelectorAll('.c-card');
            if (allCards.length > existingCardsCount) {
                const firstNewCard = allCards[existingCardsCount];
                if (firstNewCard) {
                    // Scroll with offset to show new card with some context
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
