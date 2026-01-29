@php
    $logo = setting_media_url('storage/'.$appSettings['logo_white'] ?? null, asset('frontend/img/logo.png'));
    $appName = setting_localized('app_name', config('app.name', 'دليل العقار'));
    $appDescription = setting_localized('app_description', __('frontend.footer.description'));
    $socialLinks = $appSettings['social_links'] ?? [];
    $contact = $appSettings['contact'] ?? [];
    $footerCategories = \Modules\Category\Models\Category::where('status', 'active')
        ->whereNull('parent_id')
        ->orderBy('position')
        ->orderBy('id')
        ->limit(6)
        ->get();
@endphp

<footer class="c-footer">
    <div class="l-container">
        <div class="l-footer-grid">
            <div class="c-footer__main">
                <div class="c-footer__brand">
                    <img src="{{ $logo }}" alt="{{ $appName }}" class="c-footer__logo-img">
                </div>
                <p class="c-footer__desc">{{ $appDescription }}</p>
                <img src="{{ asset('frontend/img/chracter.png') }}" alt="" class="c-footer__character">
            </div>

            <div>
                <h4 class="c-footer__title">{{ __('frontend.footer.quick_links') }}</h4>
                <div class="c-footer__links">
                    <a href="{{ route('frontend.home') }}" class="c-footer__link">{{ __('frontend.nav.home') }}</a>
                    <a href="{{ route('frontend.posts.index') }}" class="c-footer__link">{{ __('frontend.nav.properties') }}</a>
                    <a href="{{ route('frontend.agents.index') }}" class="c-footer__link">{{ __('frontend.footer.offices') }}</a>
                    <a href="{{ route('frontend.posts.create') }}" class="c-footer__link">{{ __('frontend.nav.add_listing') }}</a>
                </div>
            </div>

            <div>
                <h4 class="c-footer__title">{{ __('frontend.footer.property_types') }}</h4>
                <div class="c-footer__links">
                    @foreach($footerCategories as $category)
                        <a href="{{ route('frontend.posts.index', ['category' => $category->id]) }}" class="c-footer__link">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <h4 class="c-footer__title">{{ __('frontend.footer.contact_us') }}</h4>
                <div class="c-footer__links">
                    <a href="{{ route('frontend.page.about') }}" class="c-footer__link">{{ __('frontend.footer.about') }}</a>
                    <a href="{{ route('frontend.page.contact') }}" class="c-footer__link">{{ __('frontend.footer.contact') }}</a>
                    <a href="{{ route('frontend.page.privacy') }}" class="c-footer__link">{{ __('frontend.footer.privacy') }}</a>
                    <a href="{{ route('frontend.page.terms') }}" class="c-footer__link">{{ __('frontend.footer.terms') }}</a>
                </div>
            </div>
        </div>

        <div class="c-footer__bottom">

            <p style="text-align: center;color:#fff;font-size: 16px;line-height: 1.6;">
                © حقوق الفكرة محفوظة لـ منصة {{ $appName }}
                <br>
                برمجة وتنفيذ بواسطة
                <a href="https://gavan-tech.com/" style="color: #fff ;font-weight: bold;">
                    جافان تيك
                </a>
            </p>

            {{-- <p class="c-footer__copy">© {{ date('Y') }} {{ $appName }}. {{ __('frontend.footer.rights') }}</p> --}}
            
            <div class="c-footer__social">
                @if(!empty($socialLinks['twitter']))
                    <a href="{{ $socialLinks['twitter'] }}" target="_blank" class="c-footer__social-link" aria-label="Twitter">
                        <svg class="c-footer__social-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>
                        </svg>
                    </a>
                @endif
                @if(!empty($socialLinks['instagram']))
                    <a href="{{ $socialLinks['instagram'] }}" target="_blank" class="c-footer__social-link" aria-label="Instagram">
                        <svg class="c-footer__social-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                        </svg>
                    </a>
                @endif
                @if(!empty($socialLinks['facebook']))
                    <a href="{{ $socialLinks['facebook'] }}" target="_blank" class="c-footer__social-link" aria-label="Facebook">
                        <svg class="c-footer__social-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</footer>

