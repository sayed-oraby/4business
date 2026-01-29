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

<article class="c-card c-card--horizontal @if($isFeatured || $isInTopPeriod) c-card--featured @endif" @if($isInTopPeriod) style="background-color: {{ $cardColor }}; border-color: {{ $packageColor }};" @endif>
    <a href="{{ route('frontend.posts.show', $post->uuid) }}" class="c-card__link">
        <div class="c-card__image @if(!$hasImage) c-card__image--no-photo @endif" style="width:auto">
            <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="c-card__img @if(!$hasImage) c-card__img--placeholder @endif" loading="lazy">
            @if(!$hasImage)
            <span class="c-card__no-photo-label">بدون صورة</span>
            @endif
            @if($showPackageBadge)
            <span class="c-card__tag" style="background-color: {{ $packageColor }};">{{ $post->package->title }}</span>
            @endif
        </div>
        <div class="c-card__content">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                <h3 class="c-card__title" style="margin-bottom: 0;">{{ $post->title }}</h3>
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                    <span style="display: flex; align-items: center; color: var(--c-text-secondary); font-size: 0.9rem; white-space: nowrap;">
                        <svg class="p-ad__location-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" style="width: 16px; height: 16px; margin-inline-end: 4px;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        {{ @$post->state->name }} @if($post->city) - {{ @$post->city->name }} @endif
                    </span>
                    
                </div>
            </div>
            <p class="c-card__desc">{{ Str::limit($post->description, 120) }}</p>
            <div class="c-card__footer">
                <div style="display: flex">
                    @if($post->price)
                    <span class="c-card__price">{{ number_format($post->price) }} د.ك</span>
                    &nbsp;
                    &nbsp;
                    @endif

                    <span class="c-card__meta" style="display: flex; align-items: center; color: var(--c-text-secondary); font-size: 0.8rem; margin: 0;">
                        <svg class="c-card__meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" style="width: 14px; height: 14px; margin-inline-end: 4px;">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        {{ $post->created_at->diffForHumans(null, true) }}
                    </span>
                </div>
                <div>
                    <span>
                        {{ $post->start_date->format('Y-m-d') }}
                    </span>
                </div>
            </div>
        </div>
    </a>
</article>