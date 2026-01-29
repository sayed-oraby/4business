@extends('layouts.dashboard.master')

@section('title', __('product::product.actions.create'))
@section('page-title', __('product::product.actions.create'))

@section('toolbar-actions')
    <a href="{{ route('dashboard.products.index') }}" class="btn btn-light">
        {{ __('product::product.actions.back') }}
    </a>
    @can('products.create')
        <button class="btn btn-primary" type="submit" form="productForm" data-product-submit data-kt-indicator="off">
            <span class="indicator-label">{{ __('product::product.form.save') }}</span>
            <span class="indicator-progress">
                {{ __('product::product.form.save') }}
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
        </button>
    @endcan
@endsection

@section('content')
    @include('product::dashboard.partials.form', [
        'mode' => 'create',
        'product' => $product,
        'statuses' => $statuses,
        'availableLocales' => $availableLocales,
        'galleryToken' => $galleryToken,
    ])
@endsection

@push('scripts')
    <script>
        window.ProductForm = {
            mode: 'create',
            product: @json($productResource ?? null),
            galleryToken: "{{ $galleryToken }}",
            locale: "{{ app()->getLocale() }}",
            locales: @json(array_keys($availableLocales)),
            statuses: @json($statuses),
            statusLabels: @json(collect($statuses)->mapWithKeys(fn($status) => [$status => __('product::product.statuses.' . $status)])),
            routes: {
                store: "{{ route('dashboard.products.store') }}",
                update: "{{ route('dashboard.products.update', ['product' => '__ID__']) }}",
                galleryUpload: "{{ route('dashboard.products.gallery.upload') }}",
                galleryIndex: "{{ route('dashboard.products.gallery.index', ['product' => '__ID__']) }}",
                galleryDestroy: "{{ route('dashboard.products.gallery.destroy', ['gallery' => '__ID__']) }}",
                tagsIndex: "{{ route('dashboard.products.tags.index') }}",
                tagsStore: "{{ route('dashboard.products.tags.store') }}",
                categories: "{{ route('dashboard.products.categories') }}",
                brands: "{{ route('dashboard.products.brands') }}",
                edit: "{{ route('dashboard.products.edit', ['product' => '__ID__']) }}",
                index: "{{ route('dashboard.products.index') }}",
            },
            labels: {
                yes: "{{ __('product::product.labels.yes') }}",
                no: "{{ __('product::product.labels.no') }}",
            },
            messages: {
                created: "{{ __('product::product.messages.created') }}",
                updated: "{{ __('product::product.messages.updated') }}",
                tagCreated: "{{ __('product::product.messages.tag_created') }}",
                galleryDeleted: "{{ __('product::product.messages.gallery_deleted') }}",
            },
            upload: {
                maxFileSize: 5,
                maxFiles: 20,
            },
        };
    </script>
    @vite('resources/js/app.js')
@endpush
