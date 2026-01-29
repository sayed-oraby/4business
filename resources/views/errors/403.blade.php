@extends('errors.layout')

@php $isRtl = app()->getLocale() === 'ar'; @endphp

@section('title', $isRtl ? 'غير مصرح بالوصول' : 'Access Denied')
@section('code', '403')
@section('heading', $isRtl ? 'غير مصرح بالوصول' : 'Access Denied')
@section('message', $isRtl ? 'عذراً، ليس لديك صلاحية للوصول إلى هذه الصفحة. إذا كنت تعتقد أن هذا خطأ، يرجى التواصل مع الدعم الفني.' : 'Sorry, you do not have permission to access this page. If you believe this is a mistake, please contact support.')

@section('icon')
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
    <circle cx="12" cy="16" r="1"/>
</svg>
@endsection
