@extends('errors.layout')

@php $isRtl = app()->getLocale() === 'ar'; @endphp

@section('title', $isRtl ? 'طلب غير صالح' : 'Bad Request')
@section('code', '400')
@section('heading', $isRtl ? 'طلب غير صالح' : 'Bad Request')
@section('message', $isRtl ? 'عذراً، الطلب الذي أرسلته غير صالح أو يحتوي على بيانات خاطئة. يرجى التحقق والمحاولة مرة أخرى.' : 'Sorry, the request you sent is invalid or contains incorrect data. Please check and try again.')

@section('icon')
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
    <circle cx="12" cy="12" r="10"/>
    <line x1="15" y1="9" x2="9" y2="15"/>
    <line x1="9" y1="9" x2="15" y2="15"/>
</svg>
@endsection
