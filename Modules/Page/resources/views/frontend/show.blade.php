@extends('layouts.frontend.master')

@section('title', $page->title)
@section('meta-description', Str::limit(strip_tags($page->content), 160))
@section('body-class', 'page-static')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/static.css') }}">
@endpush

@section('content')
    <main class="p-static">
        <div class="l-container">
            <nav class="c-breadcrumb">
                <a href="{{ route('frontend.home') }}" class="c-breadcrumb__link">{{ __('frontend.nav.home') }}</a>
                <span class="c-breadcrumb__separator">›</span>
                <span class="c-breadcrumb__current">{{ $page->title }}</span>
            </nav>

            <article class="p-static__content">
                <h1 class="p-static__title">{{ $page->title }}</h1>
                <div class="p-static__body">
                    {!! $page->content !!}
                </div>
            </article>
        </div>
    </main>
@endsection

