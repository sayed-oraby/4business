@php
    $logo = setting_media_url($appSettings['logo'] ?? null, asset('frontend/img/logo.png'));
    $appName = setting_localized('app_name', config('app.name', 'دليل العقار'));
    $currentLocale = app()->getLocale();
    $alternateLocale = $currentLocale === 'ar' ? 'en' : 'ar';
    $alternateLocaleName = $currentLocale === 'ar' ? 'EN' : 'العربية';
@endphp

<nav class="c-navbar">
    <div class="l-container c-navbar__inner">
        <!-- Brand -->
        <a href="{{ route('frontend.home') }}" class="c-navbar__brand">
            <img src="{{ $logo }}" alt="{{ $appName }}" class="c-navbar__logo-img">
        </a>

        <!-- Desktop Nav Links -->
        <div class="c-navbar__links">
            <a href="{{ route('frontend.home') }}" class="c-navbar__link {{ is_active_route('frontend.home') }}">{{ __('frontend.nav.home') }}</a>
            <a href="{{ route('frontend.posts.index') }}" class="c-navbar__link {{ is_active_route('frontend.posts.*') }}">{{ __('frontend.nav.properties') }}</a>
            <a href="{{ route('frontend.agents.index') }}" class="c-navbar__link {{ is_active_route('frontend.agents.*') }}">{{ __('frontend.nav.offices') }}</a>
            @guest('admin')
                <a href="{{ route('frontend.login') }}" class="c-navbar__link {{ is_active_route('frontend.login') }}">{{ __('frontend.nav.login') }}</a>
                <a href="{{ route('frontend.register') }}" class="c-navbar__link {{ is_active_route('frontend.register') }}">{{ __('frontend.nav.register') }}</a>
            @else
                <a href="{{ route('frontend.account.dashboard') }}" class="c-navbar__link {{ is_active_route('frontend.account.*') }}">{{ __('frontend.nav.my_account') }}</a>
            @endguest
        </div>

        <!-- CTA Area -->
        <div class="c-navbar__cta">
            <a href="{{ route('core.locale.switch', $alternateLocale) }}" class="c-navbar__lang">{{ $alternateLocaleName }}</a>
            <a href="{{ route('frontend.posts.create') }}" class="c-btn c-btn--ghost c-btn--sm">
                <svg class="c-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                {{ __('frontend.nav.add_listing') }}
            </a>
        </div>

        <!-- Hamburger (Mobile) -->
        <button class="c-navbar__hamburger" id="openDrawer" aria-label="{{ __('frontend.menu') }}">
            <svg class="c-navbar__hamburger-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
    </div>
</nav>

