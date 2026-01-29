@extends('layouts.frontend.master')

@section('title', $pageTitle ?? __('frontend.listings.title'))
@section('body-class', 'page-listings')

@push('styles')
@php($v = '1.5.0')
<link rel="stylesheet" href="{{ asset('frontend/css/pages/listings.css') }}?v={{ $v }}">
<style>
/* Simple One-Line Search Bar */
.p-filters {
    background: var(--c-bg-white);
    border-radius: var(--radius-xl);
    padding: var(--space-16) var(--space-20);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    margin-bottom: var(--space-24);
}

.p-filters__row {
    display: flex;
    align-items: center;
    gap: var(--space-10);
    flex-wrap: nowrap;
}

/* Search Input */
.p-filters__search {
    flex: 1;
    min-width: 200px;
    position: relative;
}

.p-filters__search-input {
    width: 100%;
    height: 44px;
    padding: 0 var(--space-16);
    padding-right: 40px;
    border: 1px solid var(--c-border);
    border-radius: var(--radius-lg);
    font-size: 14px;
    background: var(--c-bg);
}

.p-filters__search-input:focus {
    outline: none;
    border-color: var(--c-primary);
}

.p-filters__search-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    color: var(--c-text-muted);
}

/* Dropdown Buttons */
.p-filters__dropdown {
    position: relative;
}

.p-filters__dropdown-btn {
    display: flex;
    align-items: center;
    gap: var(--space-6);
    height: 44px;
    padding: 0 var(--space-14);
    background: var(--c-bg);
    border: 1px solid var(--c-border);
    border-radius: var(--radius-lg);
    font-size: 14px;
    font-weight: 500;
    color: var(--c-text);
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s;
}

.p-filters__dropdown-btn:hover {
    border-color: var(--c-primary);
}

.p-filters__dropdown-btn.is-active {
    background: var(--c-primary);
    border-color: var(--c-primary);
    color: white;
    border-radius: var(--radius-lg);
}

.p-filters__dropdown-btn svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    transition: transform 0.2s;
}

.p-filters__dropdown.is-open .p-filters__dropdown-btn svg {
    transform: rotate(180deg);
}

/* Dropdown Menu */
.p-filters__dropdown-menu {
    position: absolute;
    top: calc(100% + 4px);
    right: 0;
    min-width: 200px;
    max-height: 300px;
    overflow-y: auto;
    background: var(--c-bg-white);
    border: 1px solid var(--c-border);
    border-radius: var(--radius-lg);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    display: none;
}

.p-filters__dropdown.is-open .p-filters__dropdown-menu {
    display: block;
}

/* Mobile Backdrop */
.p-filters__backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9998;
}

.p-filters__backdrop.is-active {
    display: block;
}

.p-filters__dropdown-search {
    padding: var(--space-10);
    border-bottom: 1px solid var(--c-border);
    position: sticky;
    top: 0;
    background: var(--c-bg-white);
}

.p-filters__dropdown-search input {
    width: 100%;
    padding: var(--space-8) var(--space-12);
    border: 1px solid var(--c-border);
    border-radius: var(--radius-md);
    font-size: 13px;
}

.p-filters__dropdown-item {
    display: flex;
    align-items: center;
    gap: var(--space-8);
    width: 100%;
    padding: var(--space-10) var(--space-14);
    background: none;
    border: none;
    text-align: right;
    font-size: 14px;
    color: var(--c-text);
    cursor: pointer;
    transition: background 0.15s;
}

.p-filters__dropdown-item:hover {
    background: var(--c-bg);
}

.p-filters__dropdown-item.is-active {
    background: var(--c-primary-light);
    color: var(--c-primary);
    font-weight: 600;
}

.p-filters__dropdown-item--state {
    font-weight: 600;
    background: var(--c-bg);
}

.p-filters__dropdown-item--city {
    padding-right: var(--space-24);
    font-size: 13px;
}

/* Location Tags Row */
.p-filters__tags-row {
    display: flex;
    align-items: center;
    gap: var(--space-8);
    margin-top: var(--space-12);
    padding-top: var(--space-12);
    border-top: 1px solid var(--c-border-light);
    overflow-x: auto;
    scrollbar-width: none;
}

.p-filters__tags-row::-webkit-scrollbar {
    display: none;
}

.p-filters__tags-row:empty {
    display: none;
}

.p-filters__tag {
    display: inline-flex;
    align-items: center;
    gap: var(--space-6);
    padding: var(--space-6) var(--space-12);
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    border-radius: var(--radius-lg);
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
    flex-shrink: 0;
}

.p-filters__tag-remove {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    background: none;
    border: none;
    color: #64748b;
    cursor: pointer;
    padding: 0;
    transition: color 0.2s;
}

.p-filters__tag-remove:hover {
    color: #dc3545;
}

.p-filters__tag-remove svg {
    width: 12px;
    height: 12px;
}

.p-filters__clear-all {
    display: flex;
    align-items: center;
    gap: var(--space-4);
    padding: var(--space-6) var(--space-12);
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
    border-radius: var(--radius-lg);
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
    flex-shrink: 0;
}

.p-filters__clear-all:hover {
    background: #fecaca;
}

/* Submit Button */
.p-filters__submit {
    height: 44px;
    padding: 0 var(--space-24);
    background: var(--c-primary);
    color: white;
    border: none;
    border-radius: var(--radius-lg);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.2s;
}

.p-filters__submit:hover {
    background: var(--c-primary-dark);
}

/* Responsive */
@media (max-width: 900px) {
    .p-filters__row {
        flex-wrap: wrap;
        gap: var(--space-8);
    }

    .p-filters__search {
        flex: 1 1 100%;
        min-width: 100%;
        margin-bottom: var(--space-4);
    }

    .p-filters__dropdown {
        flex: 1 1 calc(33.33% - 6px);
        min-width: 0;
        position: static;
    }

    .p-filters__dropdown-btn {
        width: 100%;
        justify-content: center;
        padding: 0 var(--space-10);
        font-size: 13px;
    }

    .p-filters__dropdown-btn span {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Fix dropdown menu - use fixed position on mobile */
    .p-filters__dropdown-menu {
        position: fixed;
        top: auto;
        bottom: 0;
        left: 0;
        right: 0;
        min-width: 100%;
        max-width: 100%;
        max-height: 60vh;
        border-radius: var(--radius-xl) var(--radius-xl) 0 0;
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.2);
        z-index: 9999;
    }

    .p-filters__submit {
        flex: 1 1 100%;
        margin-top: var(--space-4);
    }

    .p-filters__tags-row {
        gap: var(--space-6);
    }

    .p-filters__tag {
        font-size: 12px;
        padding: var(--space-4) var(--space-8);
    }
}
</style>
@endpush

@section('content')
<main class="p-listings">
    <div class="l-container">
        <!-- Breadcrumb -->
        <nav class="c-breadcrumb">
            <a href="{{ route('frontend.home') }}" class="c-breadcrumb__link">{{ __('frontend.nav.home') }}</a>
            @if($selectedPostType)
            <span class="c-breadcrumb__separator">›</span>
            <span class="c-breadcrumb__current">{{ $selectedPostType->name }}</span>
            @elseif($selectedCategory)
            <span class="c-breadcrumb__separator">›</span>
            <span class="c-breadcrumb__current">{{ $selectedCategory->name }}</span>
            @endif
        </nav>

        <!-- Page Header -->
        <div class="l-page-header">
            <h1 class="l-page-header__title">{{ $pageTitle ?? __('frontend.listings.title') }}</h1>
            <p class="l-page-header__count">({{ __('frontend.listings.result_count', ['count' => $posts->total()]) }})</p>
        </div>

        <!-- Simple One-Line Filters -->
        <form action="{{ route('frontend.posts.index') }}" method="GET" class="p-filters" id="filterForm">
            <div class="p-filters__row">
                <!-- Search Input -->
                <div class="p-filters__search">
                    <svg class="p-filters__search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" name="q" class="p-filters__search-input" placeholder="{{ __('frontend.home.search_placeholder') }}" value="{{ request('q') }}">
                </div>

                <!-- Post Type Dropdown -->
                <div class="p-filters__dropdown" id="typeDropdown">
                    <button type="button" class="p-filters__dropdown-btn @if(request('type')) is-active @endif">
                        <span id="typeLabel">{{ $selectedPostType?->name ?? 'للإيجار' }}</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="p-filters__dropdown-menu">
                        @foreach($postTypes as $postType)
                        <button type="button" class="p-filters__dropdown-item @if(request('type') == $postType->slug) is-active @endif"
                            data-value="{{ $postType->slug }}" data-name="{{ $postType->name }}">
                            {{ $postType->name }}
                        </button>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="type" id="typeInput" value="{{ request('type') }}">

                <!-- Category Dropdown -->
                <div class="p-filters__dropdown" id="categoryDropdown">
                    <button type="button" class="p-filters__dropdown-btn @if(request('category')) is-active @endif">
                        <span id="categoryLabel">{{ $selectedCategory?->name ?? __('frontend.home.property_type') }}</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="p-filters__dropdown-menu">
                        <div class="p-filters__dropdown-search">
                            <input type="text" placeholder="بحث..." onkeyup="filterDropdownItems(this, 'categoryDropdown')">
                        </div>
                        <button type="button" class="p-filters__dropdown-item @if(!request('category')) is-active @endif" data-value="" data-name="{{ __('frontend.home.property_type') }}">
                            {{ __('frontend.home.property_type') }}
                        </button>
                        @foreach($categories as $category)
                        <button type="button" class="p-filters__dropdown-item @if(request('category') == $category->id) is-active @endif"
                            data-value="{{ $category->id }}" data-name="{{ $category->title }}">
                            {{ $category->title }}
                        </button>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="category" id="categoryInput" value="{{ request('category') }}">

                <!-- Location Dropdown -->
                <div class="p-filters__dropdown" id="locationDropdown">
                    <button type="button" class="p-filters__dropdown-btn">
                        <span id="locationLabel">المحافظات</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="p-filters__dropdown-menu" style="min-width: 250px;">
                        <div class="p-filters__dropdown-search">
                            <input type="text" placeholder="بحث عن محافظة أو منطقة..." onkeyup="filterDropdownItems(this, 'locationDropdown')">
                        </div>
                        @foreach($locations as $state)
                        <button type="button" class="p-filters__dropdown-item p-filters__dropdown-item--state"
                            data-type="state" data-id="{{ $state->id }}" data-name="{{ $state->name_ar }}">
                            <input type="checkbox" class="loc-checkbox" style="pointer-events: none;">
                            <span style="flex:1;">{{ $state->name_ar }}</span>
                            <span style="color: #94a3b8; font-size: 12px;">({{ $state->cities_count }})</span>
                        </button>
                        @foreach($state->cities as $city)
                        <button type="button" class="p-filters__dropdown-item p-filters__dropdown-item--city"
                            data-type="city" data-id="{{ $city->id }}" data-name="{{ $city->name_ar }}" data-state="{{ $state->id }}">
                            <input type="checkbox" class="loc-checkbox" style="pointer-events: none;">
                            {{ $city->name_ar }}
                        </button>
                        @endforeach
                        @endforeach
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="p-filters__submit">{{ __('frontend.search') }}</button>
            </div>

            <!-- Selected Location Tags -->
            <div class="p-filters__tags-row" id="tagsRow"></div>
            <div id="locationInputs"></div>
        </form>

        <!-- Mobile Backdrop -->
        <div class="p-filters__backdrop" id="filtersBackdrop"></div>

        <!-- Listings Grid -->
        <div class="l-listings-stack" id="listingsGrid">
            @forelse($posts as $post)
            @include('post::frontend.partials.post-card-horizontal', ['post' => $post])
            @empty
            <div class="u-text-center u-p-24">
                <p class="u-text-muted">{{ __('frontend.listings.no_results') }}</p>
            </div>
            @endforelse
        </div>

        <!-- Load More Button -->
        @if($posts->hasMorePages())
        <div class="l-load-more" id="loadMoreContainer">
            <button type="button" class="l-load-more__btn" id="loadMoreBtn" data-page="{{ $posts->currentPage() }}" data-last-page="{{ $posts->lastPage() }}">
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
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedLocations = [];

    // Parse URL locations
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.getAll('locations[]').forEach(function(loc) {
        const [type, id] = loc.includes('_') ? [loc.split('_')[0], loc.split('_')[1]] : ['city', loc];
        const item = document.querySelector('[data-type="' + type + '"][data-id="' + id + '"]');
        if (item) {
            selectedLocations.push({ type: type, id: id, name: item.dataset.name });
        }
    });
    updateLocationTags();

    const backdrop = document.getElementById('filtersBackdrop');
    const isMobile = window.innerWidth <= 900;

    function closeAllDropdowns() {
        document.querySelectorAll('.p-filters__dropdown').forEach(function(d) {
            d.classList.remove('is-open');
        });
        if (backdrop) backdrop.classList.remove('is-active');
        document.body.style.overflow = '';
    }

    // Dropdown toggle
    document.querySelectorAll('.p-filters__dropdown-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const dropdown = this.closest('.p-filters__dropdown');
            const isOpen = dropdown.classList.contains('is-open');

            // Close all first
            closeAllDropdowns();

            if (!isOpen) {
                dropdown.classList.add('is-open');
                // Show backdrop on mobile
                if (window.innerWidth <= 900 && backdrop) {
                    backdrop.classList.add('is-active');
                    document.body.style.overflow = 'hidden';
                }
            }
        });
    });

    // Close on backdrop click
    if (backdrop) {
        backdrop.addEventListener('click', closeAllDropdowns);
    }

    // Close on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.p-filters__dropdown') && !e.target.closest('.p-filters__backdrop')) {
            closeAllDropdowns();
        }
    });

    // Type selection
    document.querySelectorAll('#typeDropdown .p-filters__dropdown-item').forEach(function(item) {
        item.addEventListener('click', function() {
            document.getElementById('typeInput').value = this.dataset.value;
            document.getElementById('typeLabel').textContent = this.dataset.name;

            document.querySelectorAll('#typeDropdown .p-filters__dropdown-item').forEach(i => i.classList.remove('is-active'));
            this.classList.add('is-active');

            const btn = document.querySelector('#typeDropdown .p-filters__dropdown-btn');
            btn.classList.toggle('is-active', this.dataset.value !== '');

            closeAllDropdowns();
        });
    });

    // Category selection
    document.querySelectorAll('#categoryDropdown .p-filters__dropdown-item').forEach(function(item) {
        item.addEventListener('click', function() {
            document.getElementById('categoryInput').value = this.dataset.value;
            document.getElementById('categoryLabel').textContent = this.dataset.name;

            document.querySelectorAll('#categoryDropdown .p-filters__dropdown-item').forEach(i => i.classList.remove('is-active'));
            this.classList.add('is-active');

            const btn = document.querySelector('#categoryDropdown .p-filters__dropdown-btn');
            btn.classList.toggle('is-active', this.dataset.value !== '');

            closeAllDropdowns();
        });
    });

    // Location selection
    document.querySelectorAll('#locationDropdown .p-filters__dropdown-item').forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const type = this.dataset.type;
            const id = this.dataset.id;
            const name = this.dataset.name;
            const checkbox = this.querySelector('.loc-checkbox');

            const idx = selectedLocations.findIndex(l => l.type === type && l.id === id);

            if (idx > -1) {
                selectedLocations.splice(idx, 1);
                checkbox.checked = false;
                this.classList.remove('is-active');
            } else {
                selectedLocations.push({ type, id, name });
                checkbox.checked = true;
                this.classList.add('is-active');
            }

            updateLocationTags();
        });
    });

    function updateLocationTags() {
        const container = document.getElementById('tagsRow');
        const inputsContainer = document.getElementById('locationInputs');

        container.innerHTML = '';
        inputsContainer.innerHTML = '';

        // Reset checkboxes
        document.querySelectorAll('#locationDropdown .loc-checkbox').forEach(cb => {
            cb.checked = false;
            cb.closest('.p-filters__dropdown-item').classList.remove('is-active');
        });

        if (selectedLocations.length === 0) {
            document.getElementById('locationLabel').textContent = 'المحافظات';
            return;
        }

        document.getElementById('locationLabel').textContent = selectedLocations.length + ' مناطق';

        selectedLocations.forEach(function(loc, index) {
            // Update checkbox
            const item = document.querySelector('[data-type="' + loc.type + '"][data-id="' + loc.id + '"]');
            if (item) {
                item.querySelector('.loc-checkbox').checked = true;
                item.classList.add('is-active');
            }

            // Create tag
            const tag = document.createElement('span');
            tag.className = 'p-filters__tag';
            tag.innerHTML = loc.name + '<button type="button" class="p-filters__tag-remove" onclick="removeLocation(' + index + ')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>';
            container.appendChild(tag);

            // Create input
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'locations[]';
            input.value = loc.type + '_' + loc.id;
            inputsContainer.appendChild(input);
        });

        // Add clear all button
        if (selectedLocations.length > 1) {
            const clearBtn = document.createElement('button');
            clearBtn.type = 'button';
            clearBtn.className = 'p-filters__clear-all';
            clearBtn.innerHTML = 'مسح الكل';
            clearBtn.onclick = function() {
                selectedLocations = [];
                updateLocationTags();
            };
            container.appendChild(clearBtn);
        }
    }

    window.removeLocation = function(index) {
        selectedLocations.splice(index, 1);
        updateLocationTags();
    };

    window.selectedLocations = selectedLocations;
});

function filterDropdownItems(input, dropdownId) {
    const filter = input.value.toLowerCase();
    const items = document.querySelectorAll('#' + dropdownId + ' .p-filters__dropdown-item');
    items.forEach(function(item) {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(filter) ? '' : 'none';
    });
}

// Load More Posts with AJAX
(function() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const listingsGrid = document.getElementById('listingsGrid');
    const loadMoreContainer = document.getElementById('loadMoreContainer');
    
    if (!loadMoreBtn) return;
    
    loadMoreBtn.addEventListener('click', function() {
        const currentPage = parseInt(this.dataset.page);
        const nextPage = currentPage + 1;
        const lastPage = parseInt(this.dataset.lastPage);
        const textEl = this.querySelector('.l-load-more__text');
        const spinnerEl = this.querySelector('.l-load-more__spinner');
        
        // Show loading state
        textEl.style.display = 'none';
        spinnerEl.style.display = 'inline-flex';
        loadMoreBtn.disabled = true;
        
        // Build URL with current filters
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        formData.append('page', nextPage);
        
        const params = new URLSearchParams();
        for (const [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        
        const url = '{{ route("frontend.posts.index") }}?' + params.toString();
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Accept-Language': '{{ app()->getLocale() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Append new posts
            listingsGrid.insertAdjacentHTML('beforeend', data.html);
            
            // Update page number
            loadMoreBtn.dataset.page = nextPage;
            
            // Scroll to show new content
            const newCards = listingsGrid.querySelectorAll('.c-card');
            if (newCards.length > 0) {
                const firstNewCard = newCards[newCards.length - data.count];
                if (firstNewCard) {
                    firstNewCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
            
            // Hide button if no more posts
            if (!data.hasMore || nextPage >= lastPage) {
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
