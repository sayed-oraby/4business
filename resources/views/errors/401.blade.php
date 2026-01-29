@extends('errors.layout')

@php $isRtl = app()->getLocale() === 'ar'; @endphp

@section('title', $isRtl ? 'غير مصرح' : 'Unauthorized')
@section('code', '401')
@section('heading', $isRtl ? 'غير مصرح' : 'Unauthorized')
@section('message', $isRtl ? 'عذراً، يجب عليك تسجيل الدخول للوصول إلى هذه الصفحة.' : 'Sorry, you must be logged in to access this page.')

@section('icon')
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
    <circle cx="12" cy="7" r="4"/>
    <line x1="18" y1="8" x2="23" y2="13"/>
    <line x1="23" y1="8" x2="18" y2="13"/>
</svg>
@endsection
