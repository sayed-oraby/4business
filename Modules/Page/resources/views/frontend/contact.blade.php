@extends('layouts.frontend.master')

@section('title', __('frontend.pages.contact_title'))
@section('body-class', 'page-static page-contact')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/static.css') }}">
@endpush

@section('content')
    @php
        $contact = $appSettings['contact'] ?? [];
    @endphp

    <main class="p-static">
        <div class="l-container">
            <div class="p-static__header">
                <h1 class="p-static__title">{{ __('frontend.pages.contact_title') }}</h1>
                <p class="p-static__subtitle">{{ __('frontend.pages.contact_subtitle') }}</p>
            </div>

            <div class="p-static__content" style="max-width: 1000px;">
                <div class="p-static__card">
                    <div class="p-contact__grid">
                        <!-- Contact Form -->
                        <div>
                            <h2 class="p-static__section-title">{{ __('frontend.contact.send_message') }}</h2>
                            
                            @if(session('success'))
                                <div class="c-alert c-alert--success" style="margin-bottom: var(--space-20);">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('frontend.page.send-contact') }}" method="POST" class="p-contact__form">
                                @csrf
                                <div class="c-form-group">
                                    <label class="c-form-label">{{ __('frontend.contact.full_name') }}</label>
                                    <input type="text" name="name" class="c-input" placeholder="{{ __('frontend.contact.name_placeholder') }}" required value="{{ old('name') }}">
                                    @error('name')
                                        <span class="c-form-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="c-form-group">
                                    <label class="c-form-label">{{ __('frontend.contact.email') }}</label>
                                    <input type="email" name="email" class="c-input" placeholder="example@email.com" required value="{{ old('email') }}">
                                    @error('email')
                                        <span class="c-form-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="c-form-group">
                                    <label class="c-form-label">{{ __('frontend.contact.phone') }}</label>
                                    <input type="tel" name="phone" class="c-input" placeholder="+965 5xxx xxxx" value="{{ old('phone') }}">
                                </div>
                                <div class="c-form-group">
                                    <label class="c-form-label">{{ __('frontend.contact.message') }}</label>
                                    <textarea name="message" class="c-input" style="height: 120px; resize: none; padding-top: 14px;" placeholder="{{ __('frontend.contact.message_placeholder') }}" required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <span class="c-form-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="c-btn c-btn--primary c-btn--lg">
                                    {{ __('frontend.contact.send') }}
                                    <svg class="c-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="22" y1="2" x2="11" y2="13"/>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Contact Info -->
                        <div class="p-contact__info">
                            <h3 class="p-contact__info-title">{{ __('frontend.contact.info_title') }}</h3>
                            
                            @if(!empty($contact['address']))
                                <div class="p-contact__info-item">
                                    <div class="p-contact__info-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                            <circle cx="12" cy="10" r="3"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="p-contact__info-label">{{ __('frontend.contact.address') }}</div>
                                        <div class="p-contact__info-value">{{ is_array($contact['address']) ? ($contact['address'][app()->getLocale()] ?? $contact['address']['ar'] ?? '') : $contact['address'] }}</div>
                                    </div>
                                </div>
                            @endif
                            
                            @if(!empty($contact['phone']))
                                <div class="p-contact__info-item">
                                    <div class="p-contact__info-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="p-contact__info-label">{{ __('frontend.contact.phone') }}</div>
                                        <div class="p-contact__info-value" dir="ltr">+965 {{ $contact['phone'] }}</div>
                                    </div>
                                </div>
                            @endif
                            
                            @if(!empty($contact['inbox_email']))
                                <div class="p-contact__info-item">
                                    <div class="p-contact__info-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                            <polyline points="22,6 12,13 2,6"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="p-contact__info-label">{{ __('frontend.contact.email') }}</div>
                                        <div class="p-contact__info-value">{{ $contact['inbox_email'] }}</div>
                                    </div>
                                </div>
                            @endif
                            
                            @if(!empty($contact['working_hours']))
                                <div class="p-contact__info-item">
                                    <div class="p-contact__info-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="p-contact__info-label">{{ __('frontend.contact.working_hours') }}</div>
                                        <div class="p-contact__info-value">{{ is_array($contact['working_hours']) ? ($contact['working_hours'][app()->getLocale()] ?? $contact['working_hours']['ar'] ?? '') : $contact['working_hours'] }}</div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Social -->
                            @php $social = $appSettings['social_links'] ?? []; @endphp
                            @if(!empty($social))
                                <div class="p-contact__social">
                                    @if(!empty($social['twitter']))
                                        <a href="{{ $social['twitter'] }}" class="p-contact__social-link" aria-label="Twitter" target="_blank">
                                            <svg class="p-contact__social-icon" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if(!empty($social['instagram']))
                                        <a href="{{ $social['instagram'] }}" class="p-contact__social-link" aria-label="Instagram" target="_blank">
                                            <svg class="p-contact__social-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if(!empty($social['facebook']))
                                        <a href="{{ $social['facebook'] }}" class="p-contact__social-link" aria-label="Facebook" target="_blank">
                                            <svg class="p-contact__social-icon" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if(!empty($contact['whatsapp']))
                                        <a href="https://wa.me/965{{ $contact['whatsapp'] }}" class="p-contact__social-link" aria-label="WhatsApp" target="_blank">
                                            <svg class="p-contact__social-icon" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
