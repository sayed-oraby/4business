@extends('errors.layout')

@php $isRtl = app()->getLocale() === 'ar'; @endphp

@section('title', $isRtl ? 'الصفحة غير موجودة' : 'Page Not Found')
@section('code', '404')
@section('heading', $isRtl ? 'الصفحة غير موجودة' : 'Page Not Found')
@section('message', $isRtl ? 'عذراً، الصفحة التي تبحث عنها غير موجودة أو تم نقلها أو حذفها.' : 'Sorry, the page you are looking for does not exist, has been moved, or has been removed.')

@section('icon')
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
    <circle cx="12" cy="12" r="10"/>
    <path d="M16 16s-1.5-2-4-2-4 2-4 2"/>
    <line x1="9" y1="9" x2="9.01" y2="9"/>
    <line x1="15" y1="9" x2="15.01" y2="9"/>
</svg>
@endsection
