@php
$isFeatured = $post->is_featured ?? false;
$firstImage = $post->attachments->first();
$hasImage = (bool) $firstImage;
$siteLogo = setting_media_url($appSettings['logo'] ?? null, asset('frontend/img/logo.png'));
$imageUrl = $hasImage ? asset('storage/' . $firstImage->file_path) : $siteLogo;
@endphp

<article class="c-card c-card--horizontal @if($isFeatured) c-card--featured @endif" style="flex-direction: column;">
    <a href="{{ route('frontend.posts.show', $post->uuid) }}" class="c-card__link"
        style="display: flex; text-decoration: none; color: inherit;width: 100%;">
        <div class="c-card__image @if(!$hasImage) c-card__image--no-photo @endif" style="width:auto">
            <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="c-card__img @if(!$hasImage) c-card__img--placeholder @endif" loading="lazy">
            @if(!$hasImage)
            <span class="c-card__no-photo-label">بدون صورة</span>
            @endif
            @if($post->package && $post->package->price > 0)
            <span class="c-card__tag">{{ $post->package->title }}</span>
            @endif
        </div>
        <div class="c-card__content" style="min-width: 0; flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; gap: 12px; min-width: 0;">
                <h3 class="c-card__title" style="margin-bottom: 0; min-width: 0; flex: 1;">{{ $post->title }}</h3>
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                @php
                    $statusConfig = [
                        'pending' => ['bg' => '#FFF7ED', 'text' => '#C2410C', 'dot' => '#EA580C'], // Orange/Warning
                        'approved' => ['bg' => '#ECFDF5', 'text' => '#047857', 'dot' => '#059669'], // Green/Success
                        'active' => ['bg' => '#ECFDF5', 'text' => '#047857', 'dot' => '#059669'], // Green/Success
                        'rejected' => ['bg' => '#FEF2F2', 'text' => '#B91C1C', 'dot' => '#DC2626'], // Red/Danger
                        'stopped' => ['bg' => '#F3F4F6', 'text' => '#6B7280', 'dot' => '#9CA3AF'], // Gray/Stopped
                        'awaiting_payment' => ['bg' => '#FEF3C7', 'text' => '#92400E', 'dot' => '#F59E0B'], // Yellow/Payment
                        'payment_failed' => ['bg' => '#FEF2F2', 'text' => '#B91C1C', 'dot' => '#DC2626'], // Red/Failed
                    ];
                    $config = $statusConfig[$post->status] ?? $statusConfig['pending'];
                @endphp
                <span style="display: flex; align-items: center; color: {{ $config['text'] }}; background-color: {{ $config['bg'] }}; font-size: 0.85rem; white-space: nowrap; padding: 4px 10px; border-radius: 20px; font-weight: 500;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background-color: {{ $config['dot'] }}; margin-inline-end: 6px;"></span>
                    {{ __('frontend.post_status.' . $post->status) }}
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
                        <b class="post_end_date">
                            تاريخ انتهاء النشر      
                        </b> 
                        : 
                        {{ $post->end_date->format('Y-m-d') }}
                    </span>
                </div>
            </div>
        </div>
    </a>
    
    <!-- Action Buttons (Outside the link) -->
    <div class="c-card__actions" style="display: flex; gap: 8px; padding: 12px 16px; flex-wrap: wrap; border-top: 1px solid #E5E7EB; background: #FAFAFA; border-radius: 0 0 12px 12px;justify-content: left;">
        {{-- Edit Button --}}
        <a href="{{ route('frontend.posts.edit', $post->uuid) }}" 
           class="c-card__action-btn c-card__action-btn--edit"
           style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.3s ease; background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); color: white; border: none; cursor: pointer; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            تعديل
        </a>
        
        {{-- Delete Button --}}
        <button type="button" 
                onclick="confirmDelete('{{ $post->uuid }}')"
                class="c-card__action-btn c-card__action-btn--delete"
                style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.3s ease; background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%); color: white; border: none; cursor: pointer; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                <line x1="10" y1="11" x2="10" y2="17"/>
                <line x1="14" y1="11" x2="14" y2="17"/>
            </svg>
            حذف
        </button>
        
        {{-- Retry Payment Button --}}
        @if(in_array($post->status, ['awaiting_payment', 'payment_failed']) && !$post->is_paid)
        <button type="button"
                onclick="retryPayment('{{ $post->uuid }}')"
                class="c-card__action-btn c-card__action-btn--payment"
                style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.3s ease; background: linear-gradient(135deg, {{ $post->status === 'payment_failed' ? '#EF4444 0%, #DC2626 100%' : '#F59E0B 0%, #D97706 100%' }}); color: white; border: none; cursor: pointer; box-shadow: 0 2px 8px rgba({{ $post->status === 'payment_failed' ? '239, 68, 68' : '245, 158, 11' }}, 0.3);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                @if($post->status === 'payment_failed')
                <path d="M21 2v6h-6M3 12a9 9 0 0 1 15-6.7L21 8M3 22v-6h6M21 12a9 9 0 0 1-15 6.7L3 16"/>
                @else
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <path d="M2 10h20"/>
                @endif
            </svg>
            @if($post->status === 'payment_failed')
                إعادة محاولة الدفع
            @else
                إتمام الدفع
            @endif
        </button>
        <span style="font-size: 0.8rem; color: {{ $post->status === 'payment_failed' ? '#B91C1C' : '#92400E' }}; background: {{ $post->status === 'payment_failed' ? '#FEE2E2' : '#FEF3C7' }}; padding: 6px 12px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                @if($post->status === 'payment_failed')
                <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                @else
                <circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/>
                @endif
            </svg>
            المبلغ: {{ number_format($post->package->price, 2) }} د.ك
        </span>
        @endif

        {{-- Stop Publishing Button --}}
        @if($post->status === 'approved' || $post->status === 'active')
        <button type="button"
                onclick="confirmStop('{{ $post->uuid }}')"
                class="c-card__action-btn c-card__action-btn--stop"
                style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.3s ease; background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white; border: none; cursor: pointer; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="10" y1="15" x2="10" y2="9"/>
                <line x1="14" y1="15" x2="14" y2="9"/>
            </svg>
            إيقاف النشر
        </button>
        @elseif($post->status === 'stopped')
        <button type="button"
                onclick="confirmResume('{{ $post->uuid }}')"
                class="c-card__action-btn c-card__action-btn--resume"
                style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.3s ease; background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; border: none; cursor: pointer; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="5 3 19 12 5 21 5 3"/>
            </svg>
            إعادة النشر
        </button>
        @endif
    </div>
</article>

@once
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(uuid) {
    event.preventDefault();
    event.stopPropagation();
    
    Swal.fire({
        title: 'هل تريد الحذف؟',
        text: 'لن تتمكن من استعادة هذا الإعلان بعد الحذف!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'لا، إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/posts/${uuid}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'تم الحذف!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#10B981'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء الحذف',
                        icon: 'error',
                        confirmButtonColor: '#EF4444'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ أثناء الحذف',
                    icon: 'error',
                    confirmButtonColor: '#EF4444'
                });
            });
        }
    });
}

function confirmStop(uuid) {
    event.preventDefault();
    event.stopPropagation();
    
    Swal.fire({
        title: 'هل تريد إيقاف النشر؟',
        text: 'سيتم إيقاف نشر هذا الإعلان فوراً',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#F59E0B',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'نعم، أوقف النشر',
        cancelButtonText: 'لا، إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/posts/${uuid}/stop`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'تم الإيقاف!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#10B981'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء إيقاف النشر',
                        icon: 'error',
                        confirmButtonColor: '#EF4444'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ أثناء إيقاف النشر',
                    icon: 'error',
                    confirmButtonColor: '#EF4444'
                });
            });
        }
    });
}

function confirmResume(uuid) {
    event.preventDefault();
    event.stopPropagation();

    Swal.fire({
        title: 'هل تريد إعادة النشر؟',
        text: 'سيتم إعادة تفعيل نشر هذا الإعلان',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'نعم، أعد النشر',
        cancelButtonText: 'لا، إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/posts/${uuid}/resume`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'تم بنجاح!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#10B981'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: data.message || 'حدث خطأ أثناء إعادة النشر',
                        icon: 'error',
                        confirmButtonColor: '#EF4444'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ أثناء إعادة النشر',
                    icon: 'error',
                    confirmButtonColor: '#EF4444'
                });
            });
        }
    });
}

async function retryPayment(uuid) {
    event.preventDefault();
    event.stopPropagation();

    Swal.fire({
        title: 'جارٍ إنشاء رابط الدفع...',
        text: 'يرجى الانتظار',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await fetch(`/posts/${uuid}/payment/retry`, {
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
            Swal.fire({
                title: 'جارٍ التوجيه للدفع',
                text: 'سيتم توجيهك لإتمام عملية الدفع...',
                icon: 'info',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
            }).then(() => {
                window.location.href = data.payment_url;
            });
        } else {
            Swal.fire({
                title: 'خطأ!',
                text: data.message || 'حدث خطأ أثناء إنشاء رابط الدفع',
                icon: 'error',
                confirmButtonColor: '#EF4444'
            });
        }
    } catch (error) {
        console.error('Payment retry error:', error);
        Swal.fire({
            title: 'خطأ!',
            text: 'حدث خطأ في الاتصال، يرجى المحاولة مرة أخرى',
            icon: 'error',
            confirmButtonColor: '#EF4444'
        });
    }
}
</script>
@endpush
@endonce