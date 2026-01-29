@extends('layouts.frontend.master')

@section('title', $post->title)
@section('meta-description', Str::limit($post->description, 160))
@section('body-class', 'page-ad')

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/css/pages/ad.css') }}?v=1.5.1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">
@endpush

@section('content')
@php
$images = $post->attachments;
$firstImage = $images->first();
$hasImage = $firstImage !== null;
$mainImageUrl = $hasImage ? asset('storage/' . $firstImage->file_path) : null;
$logoUrl = setting_media_url($appSettings['logo'] ?? null, asset('frontend/img/logo.png'));
@endphp



<!-- ========== MAIN CONTENT ========== -->
<main class="p-ad">
    <div class="l-container">



        <div class="p-ad__content">

            <div class="p-ad__main">

                <!-- ========== GALLERY ========== -->
                <div class="p-ad__gallery" id="ad_gallery" style="margin-top:25px">
                    
                    @if($hasImage)
                    <a href="{{ $mainImageUrl }}" class="p-ad__gallery-main-link" style="display: block; width: 100%; height: 100%;">
                        <img src="{{ $mainImageUrl }}" alt="{{ $post->title }}" class="p-ad__gallery-img"
                            id="mainGalleryImage">
                    </a>
                    @else
                    <div class="p-ad__gallery-placeholder">
                        <img src="{{ $logoUrl }}" alt="{{ $post->title }}" class="p-ad__gallery-placeholder-logo">
                        <span class="p-ad__gallery-placeholder-text">بدون صورة</span>
                    </div>
                    @endif

                    @if($images->count() > 1)
                    <div class="p-ad__gallery-dots">
                        @foreach($images as $index => $image)
                        <span class="p-ad__gallery-dot @if($loop->first) p-ad__gallery-dot--active @endif"
                            data-index="{{ $index }}" data-src="{{ asset('storage/' . $image->file_path) }}"></span>
                        @endforeach
                    </div>

                    {{-- Hidden links for extra images to be included in the gallery popup --}}
                    @foreach($images as $image)
                        <a href="{{ asset('storage/' . $image->file_path) }}" class="p-ad_popup_link" style="display: none;"></a>
                    @endforeach

                    <button class="p-ad__gallery-nav p-ad__gallery-nav--prev" id="galleryPrev">
                        <svg class="p-ad__gallery-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </button>
                    <button class="p-ad__gallery-nav p-ad__gallery-nav--next" id="galleryNext">
                        <svg class="p-ad__gallery-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="15 18 9 12 15 6" />
                        </svg>
                    </button>
                    @endif
                </div>

                <!-- Payment Status Banner -->
                @if(auth('admin')->check() && auth('admin')->id() === $post->user_id && in_array($post->status, ['awaiting_payment', 'payment_failed']) && !$post->is_paid)
                <div class="p-ad__payment-banner" style="background: linear-gradient(135deg, {{ $post->status === 'payment_failed' ? '#fee2e2 0%, #fecaca 100%' : '#fef3c7 0%, #fde68a 100%' }}); border: 2px solid {{ $post->status === 'payment_failed' ? '#ef4444' : '#f59e0b' }}; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="background: {{ $post->status === 'payment_failed' ? '#ef4444' : '#f59e0b' }}; color: white; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24">
                                @if($post->status === 'payment_failed')
                                <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                                @else
                                <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                                @endif
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 5px 0; color: {{ $post->status === 'payment_failed' ? '#991b1b' : '#92400e' }}; font-size: 18px; font-weight: 600;">
                                @if($post->status === 'payment_failed')
                                    فشل الدفع
                                @else
                                    في انتظار الدفع
                                @endif
                            </h3>
                            <p style="margin: 0; color: {{ $post->status === 'payment_failed' ? '#7f1d1d' : '#78350f' }}; font-size: 14px;">
                                @if($post->status === 'payment_failed')
                                    فشلت عملية الدفع، يرجى المحاولة مرة أخرى
                                @else
                                    إعلانك بحاجة إلى إتمام عملية الدفع لنشره
                                @endif
                            </p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button onclick="retryPayment('{{ $post->uuid }}')" class="c-btn" style="background: {{ $post->status === 'payment_failed' ? '#ef4444' : '#f59e0b' }}; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                @if($post->status === 'payment_failed')
                                <path d="M21 2v6h-6M3 12a9 9 0 0 1 15-6.7L21 8M3 22v-6h6M21 12a9 9 0 0 1-15 6.7L3 16"/>
                                @else
                                <rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>
                                @endif
                            </svg>
                            @if($post->status === 'payment_failed')
                                إعادة محاولة الدفع الآن
                            @else
                                إتمام الدفع الآن
                            @endif
                        </button>
                        <div style="background: white; padding: 12px 20px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                            <span style="color: #92400e; font-weight: 600; font-size: 18px;">{{ number_format($post->package->price, 2) }} د.ك</span>
                        </div>
                    </div>
                </div>
                @endif

                @if(auth('admin')->check() && auth('admin')->id() === $post->user_id && $post->is_paid && $post->status === 'pending')
                <div class="p-ad__payment-banner" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border: 2px solid #10b981; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="background: #10b981; color: white; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" width="24" height="24">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 5px 0; color: #065f46; font-size: 18px; font-weight: 600;">تم الدفع بنجاح</h3>
                            <p style="margin: 0; color: #047857; font-size: 14px;">إعلانك قيد المراجعة وسيتم نشره قريباً</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Breadcrumb -->
                <nav class="c-breadcrumb">
                    <a href="{{ route('frontend.home') }}" class="c-breadcrumb__link">{{ __('frontend.nav.home') }}</a>
                    <span class="c-breadcrumb__separator">›</span>
                    @if($post->postType)
                    <a href="{{ route('frontend.posts.index', ['type' => $post->postType->slug]) }}"
                        class="c-breadcrumb__link">{{ $post->postType->name }}</a>
                    <span class="c-breadcrumb__separator">›</span>
                    @endif
                    <span class="c-breadcrumb__current">{{ Str::limit($post->title, 30) }}</span>
                </nav>

                <!-- Header -->
                <div class="p-ad__header">
                    @if($post->is_featured)
                    <span class="p-ad__tag">{{ __('frontend.featured') }}</span>
                    @endif
                    <h1 class="p-ad__title">{{ $post->title }}</h1>
                    @if($post->price)
                    <div class="p-ad__price">{{ number_format($post->price) }} د.ك</div>
                    @endif
                </div>

                <!-- Meta -->
                <div class="p-ad__meta">
                    <span class="p-ad__meta-item">
                        <svg class="p-ad__meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        {{ $post->created_at->diffForHumans() }}
                    </span>
                    <span class="p-ad__meta-item">
                        <svg class="p-ad__meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        {{ $post->views_count }} {{ __('frontend.ad.views') }}
                    </span>
                    @if($post->state)
                    <span class="p-ad__meta-item">
                        <svg class="p-ad__meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        {{ @$post->state->name }} @if($post->city) - {{ @$post->city->name }} @endif
                    </span>
                    @endif
                </div>

                <!-- Features -->
                @if($post->features && count($post->features) > 0)
                <div class="p-ad__section">
                    <h2 class="p-ad__section-title">{{ __('frontend.ad.specifications') }}</h2>
                    <div class="p-ad__features">
                        @if(isset($post->features['rooms']))
                        <div class="p-ad__feature">
                            <svg class="p-ad__feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            </svg>
                            <span class="p-ad__feature-text">{{ $post->features['rooms'] }} {{ __('frontend.ad.rooms')
                                }}</span>
                        </div>
                        @endif
                        @if(isset($post->features['bathrooms']))
                        <div class="p-ad__feature">
                            <svg class="p-ad__feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                                <line x1="8" y1="21" x2="16" y2="21" />
                                <line x1="12" y1="17" x2="12" y2="21" />
                            </svg>
                            <span class="p-ad__feature-text">{{ $post->features['bathrooms'] }} {{
                                __('frontend.ad.bathrooms') }}</span>
                        </div>
                        @endif
                        @if(isset($post->features['parking']))
                        <div class="p-ad__feature">
                            <svg class="p-ad__feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="1" y="3" width="15" height="13" />
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                            </svg>
                            <span class="p-ad__feature-text">{{ $post->features['parking'] }} {{
                                __('frontend.ad.parking') }}</span>
                        </div>
                        @endif
                        @if(isset($post->features['area']))
                        <div class="p-ad__feature">
                            <svg class="p-ad__feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                            </svg>
                            <span class="p-ad__feature-text">{{ $post->features['area'] }} {{ __('frontend.ad.sqm')
                                }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Description -->
                <div class="p-ad__section">
                    <h2 class="p-ad__section-title">{{ __('frontend.ad.details') }}</h2>
                    <div class="p-ad__description">
                        {!! nl2br(e($post->description)) !!}
                    </div>
                </div>

                <!-- Related -->
                @if($relatedPosts->isNotEmpty())
                <div class="p-ad__related">
                    <h2 class="l-section__title">{{ __('frontend.ad.related') }}</h2>
                    <div class="p-ad__related-carousel">
                        <button type="button" class="p-ad__related-nav p-ad__related-nav--prev" onclick="scrollRelated(-1)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                        <div class="p-ad__related-track" id="relatedTrack">
                            @foreach($relatedPosts as $relatedPost)
                            <div class="p-ad__related-item">
                                @include('core::frontend.partials.post-card', ['post' => $relatedPost])
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="p-ad__related-nav p-ad__related-nav--next" onclick="scrollRelated(1)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                    </div>
                </div>
                @endif
            </div>



            <!-- Sidebar -->
            <aside class="p-ad__sidebar">
                <div class="p-ad__contact-card">
                    <div class="p-ad__agent">
                        <div class="p-ad__agent-avatar">
                            @if($post->user && $post->user->avatar)
                            <img src="{{ asset('storage/' . $post->user->avatar) }}" alt="{{ $post->user->name }}"
                                class="p-ad__agent-avatar-img">
                            @else
                            <svg class="p-ad__agent-avatar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            @endif
                        </div>
                        <div>
                            <div class="p-ad__agent-name">{{ $post->user?->name ?? $post->contact_name ??
                                __('frontend.contact') }}</div>
                            @if($post->user?->office_name)
                            <div class="p-ad__agent-label">{{ $post->user->office_name }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="p-ad__contact-btns">
                        @if($post->mobile_number)
                        <a href="tel:{{ $post->mobile_number }}"
                            class="c-btn p-ad__contact-btn p-ad__contact-btn--call">
                            <svg class="c-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72" />
                            </svg>
                            {{ __('frontend.ad.call') }}
                        </a>
                        @endif
                        @if($post->whatsapp ?? $post->mobile_number)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $post->whatsapp ?? $post->mobile_number) }}"
                            target="_blank" class="c-btn p-ad__contact-btn p-ad__contact-btn--whatsapp">
                            <svg class="c-btn__icon" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            {{ __('frontend.ad.whatsapp') }}
                        </a>
                        @endif
                    </div>

                    @if($post->state)
                    <div class="p-ad__location">
                        <div class="p-ad__location-label">{{ __('frontend.ad.location') }}</div>
                        <div class="p-ad__location-value">
                            <svg class="p-ad__location-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            {{ @$post->state->name }} @if($post->city) - {{ @$post->city->name }} @endif

                        </div>
                    </div>
                    @endif
                </div>
            </aside>

        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
<script>
    $(document).ready(function() {
        // Gallery navigation
        const images = @json($images->pluck('file_path')->map(fn($p) => asset('storage/' . $p)));
        let currentIndex = 0;

        function updateGallery(index) {
            if (images.length === 0) return;
            currentIndex = (index + images.length) % images.length;
            
            // Update the visible image
            $('#mainGalleryImage').attr('src', images[currentIndex]);
            
            // Update the link for popup to match current index image
            $('.p-ad__gallery-main-link').attr('href', images[currentIndex]);

            $('.p-ad__gallery-dot').each((i, dot) => {
                $(dot).toggleClass('p-ad__gallery-dot--active', i === currentIndex);
            });
        }

        $('#galleryPrev').on('click', () => updateGallery(currentIndex - 1));
        $('#galleryNext').on('click', () => updateGallery(currentIndex + 1));

        $('.p-ad__gallery-dot').on('click', function() {
            updateGallery(parseInt($(this).data('index')));
        });

        // Initialize Magnific Popup Gallery
        $('#ad_gallery').magnificPopup({
            delegate: 'a.p-ad__gallery-main-link, a.p-ad_popup_link', 
            type: 'image',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0, 1]
            },
            image: {
                tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
            }
        });
    });

    // Related posts carousel scroll
    function scrollRelated(direction) {
        const track = document.getElementById('relatedTrack');
        if (!track) return;

        const scrollAmount = 300; // pixels to scroll
        const isRTL = document.dir === 'rtl' || document.documentElement.dir === 'rtl';

        // In RTL, direction is inverted
        const actualDirection = isRTL ? -direction : direction;
        track.scrollBy({
            left: actualDirection * scrollAmount,
            behavior: 'smooth'
        });
    }

    // Retry payment function
    async function retryPayment(postUuid) {
        try {
            const response = await fetch(`/posts/${postUuid}/payment/retry`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (response.ok && data.success && data.payment_url) {
                // Redirect to MyFatoorah payment page
                window.location.href = data.payment_url;
            } else {
                alert(data.message || 'حدث خطأ أثناء إنشاء رابط الدفع');
            }
        } catch (error) {
            console.error('Payment retry error:', error);
            alert('حدث خطأ في الاتصال، يرجى المحاولة مرة أخرى');
        }
    }
</script>
@endpush