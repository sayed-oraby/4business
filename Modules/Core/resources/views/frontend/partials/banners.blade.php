@if($banners->count() > 0)
<section class="p-banner-section">
    <div class="l-container">
        @if($banners->count() === 1)
        @php $banner = $banners->first(); @endphp
        <div class="p-banner-single" onclick="openLightbox('{{ setting_media_url($banner->image_path) }}', '{{ e($banner->title) }}', '{{ e($banner->description) }}')">
            <div class="p-banner-single__image-wrapper">
                <img src="{{ setting_media_url($banner->image_path) }}" alt="{{ $banner->title }}" class="p-banner-single__image" loading="lazy">
                <div class="p-banner-carousel__zoom">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/>
                    </svg>
                </div>
            </div>
            @if($banner->title || $banner->description || $banner->button_url)
            <div class="p-banner-carousel__overlay" style="transform: translateY(0);">
                @if($banner->title)<h3 class="p-banner-carousel__title">{{ $banner->title }}</h3>@endif
                @if($banner->description)<p class="p-banner-carousel__desc">{{ $banner->description }}</p>@endif
                @if($banner->button_url && $banner->button_label)
                <a href="{{ $banner->button_url }}" class="p-banner-carousel__btn" onclick="event.stopPropagation();">{{ $banner->button_label }}</a>
                @endif
            </div>
            @endif
        </div>
        @else
        <div class="p-banner-carousel" id="bannerCarousel">
            <div class="p-banner-carousel__track" id="bannerTrack">
                @foreach($banners as $banner)
                <div class="p-banner-carousel__slide" onclick="openLightbox('{{ setting_media_url($banner->image_path) }}', '{{ e($banner->title) }}', '{{ e($banner->description) }}')">
                    <div class="p-banner-carousel__image-wrapper">
                        <img src="{{ setting_media_url($banner->image_path) }}" alt="{{ $banner->title }}" class="p-banner-carousel__image" loading="lazy">
                        <div class="p-banner-carousel__zoom">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/>
                            </svg>
                        </div>
                    </div>
                    @if($banner->title || $banner->description || $banner->button_url)
                    <div class="p-banner-carousel__overlay">
                        @if($banner->title)<h3 class="p-banner-carousel__title">{{ $banner->title }}</h3>@endif
                        @if($banner->description)<p class="p-banner-carousel__desc">{{ $banner->description }}</p>@endif
                        @if($banner->button_url && $banner->button_label)
                        <a href="{{ $banner->button_url }}" class="p-banner-carousel__btn" onclick="event.stopPropagation();">{{ $banner->button_label }}</a>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            <button class="p-banner-carousel__nav p-banner-carousel__nav--prev" onclick="event.stopPropagation(); slideCarousel(1);"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>
            <button class="p-banner-carousel__nav p-banner-carousel__nav--next" onclick="event.stopPropagation(); slideCarousel(-1);"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>
            <div class="p-banner-carousel__dots">
                @foreach($banners as $index => $banner)
                <button class="p-banner-carousel__dot {{ $index === 0 ? 'p-banner-carousel__dot--active' : '' }}" onclick="event.stopPropagation(); goToSlide({{ $index }});"></button>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

<div class="p-lightbox" id="bannerLightbox">
    <div class="p-lightbox__content">
        <button class="p-lightbox__close" onclick="closeLightbox();"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        <img src="" alt="" class="p-lightbox__image" id="lightboxImage">
        <div class="p-lightbox__info">
            <h3 class="p-lightbox__title" id="lightboxTitle"></h3>
            <p class="p-lightbox__desc" id="lightboxDesc"></p>
        </div>
    </div>
</div>
@endif
