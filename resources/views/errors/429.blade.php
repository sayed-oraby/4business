@extends('errors.layout')

@php $isRtl = app()->getLocale() === 'ar'; @endphp

@section('title', $isRtl ? 'طلبات كثيرة جداً' : 'Too Many Requests')
@section('code', '429')
@section('heading', $isRtl ? 'طلبات كثيرة جداً' : 'Too Many Requests')
@section('message', $isRtl ? 'لقد قمت بإرسال طلبات كثيرة في وقت قصير. يرجى الانتظار قليلاً ثم المحاولة مرة أخرى.' : 'You have sent too many requests in a short time. Please wait a moment and try again.')

@section('icon')
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
</svg>
@endsection
