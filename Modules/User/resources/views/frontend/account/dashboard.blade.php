@extends('layouts.frontend.master')

@section('title', __('frontend.nav.my_account'))
@section('body-class', 'page-account')

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/css/pages/listings.css') }}">


@endpush

@section('content')
<main class="p-account">
    <div class="l-container">
        <!-- Header -->
        <div class="p-account__header">
            <div class="p-account__avatar">
                @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                @else
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                @endif
            </div>
            <div class="p-account__info">
                <h1 class="p-account__name">{{ $user->name }}</h1>
                <p class="p-account__phone">{{ $user->mobile }}</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="p-account__stats">
            <div class="p-account__stat">
                <div class="p-account__stat-value">{{ $stats['total_posts'] }}</div>
                <div class="p-account__stat-label">{{ __('frontend.account.total_posts') }}</div>
            </div>
            <div class="p-account__stat">
                <div class="p-account__stat-value">{{ $stats['active_posts'] }}</div>
                <div class="p-account__stat-label">{{ __('frontend.account.active_posts') }}</div>
            </div>
            <div class="p-account__stat">
                <div class="p-account__stat-value">{{ $stats['pending_posts'] }}</div>
                <div class="p-account__stat-label">{{ __('frontend.account.pending_posts') }}</div>
            </div>
            <div class="p-account__stat">
                <div class="p-account__stat-value">{{ number_format($stats['total_views']) }}</div>
                <div class="p-account__stat-label">{{ __('frontend.account.total_views') }}</div>
            </div>
        </div>

        <!-- Quick Menu -->
        <div class="p-account__menu">
            <a href="{{ route('frontend.account.edit') }}" class="p-account__menu-item">
                <div class="p-account__menu-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                </div>
                <div class="p-account__menu-content">
                    <div class="p-account__menu-title">{{ __('frontend.account.edit_profile') }}</div>
                    <div class="p-account__menu-desc">{{ __('frontend.account.edit_profile_desc') }}</div>
                </div>
            </a>

            <a href="{{ route('frontend.account.password') }}" class="p-account__menu-item">
                <div class="p-account__menu-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                </div>
                <div class="p-account__menu-content">
                    <div class="p-account__menu-title">{{ __('frontend.account.change_password') }}</div>
                    <div class="p-account__menu-desc">{{ __('frontend.account.change_password_desc') }}</div>
                </div>
            </a>

            @if($user->account_type === 'individual')
            <a href="{{ route('frontend.account.become-agent') }}" class="p-account__menu-item">
                <div class="p-account__menu-icon p-account__menu-icon--success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="8.5" cy="7" r="4" />
                        <line x1="20" y1="8" x2="20" y2="14" />
                        <line x1="23" y1="11" x2="17" y2="11" />
                    </svg>
                </div>
                <div class="p-account__menu-content">
                    <div class="p-account__menu-title">{{ __('frontend.account.become_agent') }}</div>
                    <div class="p-account__menu-desc">{{ __('frontend.account.become_agent_desc') }}</div>
                </div>
            </a>
            @else
            <a href="{{ route('frontend.agents.show', $user->slug ?? $user->id) }}" class="p-account__menu-item">
                <div class="p-account__menu-icon p-account__menu-icon--success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                        <path d="M16 11l2 2 4-4" />
                    </svg>
                </div>
                <div class="p-account__menu-content">
                    <div class="p-account__menu-title">{{ __('frontend.account.my_office') }}</div>
                    <div class="p-account__menu-desc">{{ __('frontend.account.my_office_desc') }}</div>
                </div>
            </a>
            @endif

            <form action="{{ route('frontend.logout') }}" method="POST" class="p-account__menu-item"
                style="cursor: pointer;" onclick="this.submit();">
                @csrf
                <div class="p-account__menu-icon p-account__menu-icon--danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                </div>
                <div class="p-account__menu-content">
                    <div class="p-account__menu-title">{{ __('frontend.account.logout') }}</div>
                    <div class="p-account__menu-desc">{{ __('frontend.account.logout_desc') }}</div>
                </div>
            </form>
        </div>

        <!-- My Listings -->
        <div class="p-account__section">
            <div class="p-account__section-header">
                <h2 class="p-account__section-title">{{ __('frontend.account.my_listings') }}</h2>
                <a href="{{ route('frontend.posts.create') }}" class="c-btn c-btn--primary c-btn--sm">
                    <svg class="c-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    {{ __('frontend.nav.add_listing') }}
                </a>
            </div>

            <div class="l-listings-stack">
                @forelse($posts as $post)
                @include('post::frontend.partials.user-post-card-horizontal', ['post' => $post])
                @empty
                <div class="u-text-center"
                    style="padding: var(--space-48); background: var(--c-bg-white); border-radius: var(--radius-lg);">
                    <svg style="width: 64px; height: 64px; color: var(--c-muted); margin-bottom: var(--space-16);"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    <p style="color: var(--c-muted); margin-bottom: var(--space-16);">{{
                        __('frontend.account.no_listings') }}</p>
                    <a href="{{ route('frontend.posts.create') }}" class="c-btn c-btn--primary">
                        <svg class="c-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        {{ __('frontend.nav.add_listing') }}
                    </a>
                </div>
                @endforelse
            </div>

            @if($posts->hasPages())
            <div class="p-listings__pagination">
                <div class="c-pagination">
                    {{-- Previous Page Link --}}
                    @if ($posts->onFirstPage())
                    <span class="c-pagination__item c-pagination__item--disabled">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </span>
                    @else
                    <a href="{{ $posts->previousPageUrl() }}" class="c-pagination__item">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($posts->getUrlRange(max(1, $posts->currentPage() - 2), min($posts->lastPage(),
                    $posts->currentPage() + 2)) as $page => $url)
                    @if ($page == $posts->currentPage())
                    <span class="c-pagination__item c-pagination__item--active">{{ $page }}</span>
                    @else
                    <a href="{{ $url }}" class="c-pagination__item">{{ $page }}</a>
                    @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($posts->hasMorePages())
                    <a href="{{ $posts->nextPageUrl() }}" class="c-pagination__item">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    @else
                    <span class="c-pagination__item c-pagination__item--disabled">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </span>
                    @endif
                </div>
            </div>
            @endif

            @if($posts->hasPages())
            {{-- <div style="margin-top: var(--space-24);">
                {{ $posts->links() }}
            </div> --}}
            @endif
        </div>
    </div>
</main>
@endsection