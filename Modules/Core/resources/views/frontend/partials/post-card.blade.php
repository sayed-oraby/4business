@php
$isFeatured = $post->is_featured ?? false;
$firstImage = $post->attachments->first();
$hasImage = (bool) $firstImage;
$siteLogo = setting_media_url($appSettings['logo'] ?? null, asset('frontend/img/logo.png'));
$imageUrl = $hasImage ? asset('storage/' . $firstImage->file_path) : $siteLogo;
$isInTopPeriod = $post->package && $post->isInTopPeriod();
$isFree = $post->package && $post->package->is_free;
$showPackageBadge = ($isInTopPeriod && !$isFree) || $isFree;
$packageColor = $isFree ? '#3b82f6' : ($post->package->label_color ?? '#ef4444');
$cardColor = $post->package->card_color ?? '#eff6ff';
@endphp

<article class="c-card @if($isFeatured || $isInTopPeriod) c-card--featured @endif @if($horizontal ?? false) c-card--horizontal @endif" @if($isInTopPeriod) style="background-color: {{ $cardColor }}; border-color: {{ $packageColor }};" @endif>
    <a href="{{ route('frontend.posts.show', $post->uuid) }}" class="c-card__link">
        <div class="c-card__image @if(!$hasImage) c-card__image--no-photo @endif">
            <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="c-card__img @if(!$hasImage) c-card__img--placeholder @endif" loading="lazy">
            @if(!$hasImage)
            <span class="c-card__no-photo-label">بدون صورة</span>
            @endif
            @if($showPackageBadge)
            <span class="c-card__tag" style="background-color: {{ $packageColor }};">{{ $post->package->title }}</span>
            @endif
        </div>
        <div class="c-card__content">
            <h3 class="c-card__title">{{ $post->title }}</h3>
            <p class="c-card__desc">{{ Str::limit($post->description, 80) }}</p>
            <div class="c-card__footer">
                @if($post->price)
                <span class="c-card__price">{{ number_format($post->price) }} {{ __('frontend.currency', ['default' =>
                    'د.ك']) }}</span>
                @endif
                <span class="c-card__meta">
                    <svg class="c-card__meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    {{ $post->created_at->diffForHumans(null, true) }}
                </span>
            </div>
        </div>
    </a>
</article>