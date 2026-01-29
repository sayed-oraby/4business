@php
    $logo = setting_media_url($appSettings['logo'] ?? null, asset('frontend/img/logo.png'));
    $appName = setting_localized('app_name', config('app.name', 'دليل العقار'));
    $currentLocale = app()->getLocale();
    $alternateLocale = $currentLocale === 'ar' ? 'en' : 'ar';
    $alternateLocaleName = $currentLocale === 'ar' ? 'EN' : 'العربية';
    $socialLinks = $appSettings['social_links'] ?? [];
@endphp

<div class="c-drawer-overlay" id="drawerOverlay"></div>
<aside class="c-drawer" id="drawer">
    <div class="c-drawer__header">
        <a href="{{ route('frontend.home') }}" class="c-navbar__brand">
            <img src="{{ $logo }}" alt="{{ $appName }}" class="c-navbar__logo-img">
        </a>
        <button class="c-drawer__close" id="closeDrawer" aria-label="{{ __('frontend.close') }}">
            <svg class="c-drawer__close-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    <nav class="c-drawer__nav">
        <a href="{{ route('frontend.home') }}" class="c-drawer__link {{ is_active_route('frontend.home') }}">
            <svg class="c-drawer__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            {{ __('frontend.nav.home') }}
        </a>
        @guest('admin')
            <a href="{{ route('frontend.login') }}" class="c-drawer__link {{ is_active_route('frontend.login') }}">
                <svg class="c-drawer__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                {{ __('frontend.nav.login') }}
            </a>
            <a href="{{ route('frontend.register') }}" class="c-drawer__link {{ is_active_route('frontend.register') }}">
                <svg class="c-drawer__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <line x1="20" y1="8" x2="20" y2="14"/>
                    <line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
                {{ __('frontend.nav.register') }}
            </a>
        @else
            <a href="{{ route('frontend.account.dashboard') }}" class="c-drawer__link {{ is_active_route('frontend.account.*') }}">
                <svg class="c-drawer__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                {{ __('frontend.nav.my_account') }}
            </a>
        @endguest
        <a href="{{ route('frontend.agents.index') }}" class="c-drawer__link {{ is_active_route('frontend.agents.*') }}">
            <svg class="c-drawer__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
            </svg>
            {{ __('frontend.nav.offices') }}
        </a>
        <a href="{{ route('frontend.posts.index') }}" class="c-drawer__link {{ is_active_route('frontend.posts.*') }}">
            <svg class="c-drawer__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            {{ __('frontend.nav.properties') }}
        </a>
    </nav>

    <div class="c-drawer__cta">
        <a href="{{ route('frontend.posts.create') }}" class="c-btn c-btn--primary c-btn--block">
            <svg class="c-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            {{ __('frontend.nav.add_listing') }}
        </a>
    </div>

    <div class="c-drawer__footer">
        @if(!empty($socialLinks['twitter']))
            <a href="{{ $socialLinks['twitter'] }}" target="_blank" class="c-drawer__social" aria-label="Twitter">
                <svg class="c-drawer__social-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>
                </svg>
            </a>
        @endif
        @if(!empty($socialLinks['instagram']))
            <a href="{{ $socialLinks['instagram'] }}" target="_blank" class="c-drawer__social" aria-label="Instagram">
                <svg class="c-drawer__social-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                </svg>
            </a>
        @endif
        <a href="{{ route('core.locale.switch', $alternateLocale) }}" class="c-navbar__lang u-mr-auto">{{ $alternateLocaleName }}</a>
    </div>
</aside>

