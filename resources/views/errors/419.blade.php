@extends('errors.layout')

@php $isRtl = app()->getLocale() === 'ar'; @endphp

@section('title', $isRtl ? 'انتهت صلاحية الجلسة' : 'Page Expired')
@section('code', '419')
@section('heading', $isRtl ? 'انتهت صلاحية الجلسة' : 'Page Expired')
@section('message', $isRtl ? 'انتهت صلاحية جلستك بسبب عدم النشاط. يرجى تحديث الصفحة والمحاولة مرة أخرى.' : 'Your session has expired due to inactivity. Please refresh the page and try again.')

@section('icon')
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
    <circle cx="12" cy="12" r="10"/>
    <polyline points="12 6 12 12 16 14"/>
</svg>
@endsection
