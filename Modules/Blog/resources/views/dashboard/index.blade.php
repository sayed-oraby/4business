@extends('layouts.dashboard.master')

@section('title', __('blog::blog.title'))
@section('page-title', __('blog::blog.title'))

@push('styles')
<style>
    .blog-hero {
        background: linear-gradient(135deg, #eff4ff 0%, #ffffff 100%);
        border-radius: 1.5rem;
    }

    .blog-table-card .table thead tr {
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .04em;
    }

    .blog-table-card .table thead th,
    .blog-table-card .table tbody td {
        text-align: center !important;
        vertical-align: middle;
    }

    .blog-thumb {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 0.75rem;
        background: #f5f8fa;
    }

    .blog-dropzone {
        border: 1px dashed var(--bs-gray-300);
        border-radius: 0.75rem;
        padding: 1.5rem;
        background: #fcfcfc;
        min-height: 140px;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .blog-dropzone-previews {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .blog-dropzone-message {
        width: 100%;
    }

    .blog-dropzone .dz-preview {
        width: 130px;
        height: 150px;
        margin: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .blog-dropzone .dz-image {
        width: 100%;
        height: 110px;
        border-radius: 0.75rem;
        overflow: hidden;
    }

    .blog-dropzone .dz-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .blog-dropzone .dz-preview .dz-remove {
        font-size: 0.75rem;
        margin-top: 0.35rem;
    }

    .blog-dropzone.dz-started .blog-dropzone-message {
        display: none;
    }

    .nav-link.blog-locale-invalid {
        color: var(--bs-danger);
    }

    .nav-link.blog-locale-invalid::after {
        content: '•';
        color: var(--bs-danger);
        margin-inline-start: 0.25rem;
        font-size: 1.25rem;
        line-height: 1;
    }
</style>
@endpush

@php($availableLocales = available_locales())

@section('content')
    <div class="card border-0 shadow-sm mb-10 blog-hero">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-7">
                <div>
                    <span class="badge badge-light-primary mb-3">{{ __('dashboard.content') }}</span>
                    <h2 class="fw-bold text-gray-900 mb-2">{{ __('blog::blog.title') }}</h2>
                    <p class="text-gray-600 mb-0">{{ __('blog::blog.description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-flush shadow-sm mb-10 blog-table-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">{{ __('blog::blog.title') }}</h3>
            </div>
            <div class="card-toolbar gap-3">
                <div class="position-relative my-1">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12" id="blog-search" placeholder="{{ __('blog::blog.search_placeholder') }}">
                </div>
                <button class="btn btn-light btn-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#blogFiltersCanvas">
                    <i class="ki-duotone ki-filter fs-2"></i>{{ __('dashboard.filter_button') }}
                </button>
                @can('blogs.create')
                    <button class="btn btn-primary btn-flex align-items-center gap-2" data-blog-action="open-form">
                        <i class="ki-duotone ki-plus fs-2"></i>{{ __('blog::blog.actions.create') }}
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4" id="blogs-table">
                    <thead class="bg-transparent text-gray-500 fw-semibold">
                        <tr class="text-center align-middle">
                            <th class="w-60px text-center">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="select-all-blogs">
                                </div>
                            </th>
                            <th class="min-w-250px text-center">{{ __('blog::blog.table.blog') }}</th>
                            <th class="min-w-125px text-center">{{ __('blog::blog.table.status') }}</th>
                            <th class="min-w-150px text-center">{{ __('blog::blog.table.tags') }}</th>
                            <th class="min-w-150px text-center">{{ __('blog::blog.table.author') }}</th>
                            <th class="min-w-150px text-center">{{ __('blog::blog.table.updated_at') }}</th>
                            <th class="text-center pe-4 min-w-150px">{{ __('blog::blog.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        @can('blogs.delete')
            <div class="card-footer d-flex justify-content-between flex-wrap gap-3">
                <div class="text-muted">{{ __('blog::blog.actions.bulk_delete') }}</div>
                <button class="btn btn-light-danger" id="bulk-delete-btn" disabled>
                    <i class="ki-duotone ki-trash fs-2 me-2"></i>{{ __('blog::blog.actions.bulk_delete') }}
                </button>
            </div>
        @endcan
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="blogFiltersCanvas">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-bold">{{ __('dashboard.filters_panel.title') }}</h5>
            <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="offcanvas">
                <i class="ki-duotone ki-cross fs-2"></i>
            </button>
        </div>
        <div class="offcanvas-body pt-5">
            <form id="blog-filter-form" class="d-flex flex-column gap-5">
                <div>
                    <label class="form-label">{{ __('blog::blog.filters.status') }}</label>
                    <select class="form-select form-select-solid" name="status" data-blog-filter="status">
                        <option value="">{{ __('blog::blog.filters.status') }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ __('blog::blog.statuses.' . $status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('blog::blog.filters.state.active') }}</label>
                    <select class="form-select form-select-solid" name="state" data-blog-filter="state">
                        <option value="active">{{ __('blog::blog.filters.state.active') }}</option>
                        <option value="archived">{{ __('blog::blog.filters.state.archived') }}</option>
                        <option value="all">{{ __('blog::blog.filters.state.all') }}</option>
                    </select>
                </div>
                <div class="d-flex gap-3">
                    <button class="btn btn-light flex-grow-1" type="button" data-filter-reset>
                        <i class="ki-duotone ki-arrows-circle fs-2 me-2"></i>{{ __('dashboard.filters_panel.reset') }}
                    </button>
                    <button class="btn btn-primary flex-grow-1" type="button" data-filter-apply>
                        <i class="ki-duotone ki-check fs-2 me-2"></i>{{ __('dashboard.filters_panel.apply') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('blog::dashboard.partials.form', ['availableLocales' => $availableLocales, 'statuses' => $statuses, 'tags' => $tags])
    @include('blog::dashboard.partials.view-modal', ['availableLocales' => $availableLocales])
@endsection

@push('scripts')
    <script>
        window.BlogModule = {
            routes: {
                data: "{{ route('dashboard.blogs.data') }}",
                store: "{{ route('dashboard.blogs.store') }}",
                update: "{{ route('dashboard.blogs.update', ['blog' => '__ID__']) }}",
                destroy: "{{ route('dashboard.blogs.destroy', ['blog' => '__ID__']) }}",
                bulkDestroy: "{{ route('dashboard.blogs.bulk-destroy') }}",
                gallery: "{{ route('dashboard.blogs.gallery', ['blog' => '__ID__']) }}",
                galleryUpload: "{{ route('dashboard.blogs.gallery.upload') }}",
                galleryDestroy: "{{ route('dashboard.blogs.gallery.destroy', ['gallery' => '__ID__']) }}",
                tagsIndex: "{{ route('dashboard.blogs.tags.index') }}",
                tagsStore: "{{ route('dashboard.blogs.tags.store') }}",
                authors: "{{ route('dashboard.audit-logs.users') }}",
            },
            can: {
                update: @json(auth('admin')->user()?->can('blogs.update')),
                delete: @json(auth('admin')->user()?->can('blogs.delete')),
            },
            statuses: @json($statuses),
            tags: @json($tags),
            statusLabels: @json(collect($statuses)->mapWithKeys(fn($status) => [$status => __('blog::blog.statuses.' . $status)])),
            states: {
                active: "{{ __('blog::blog.states.active') }}",
                archived: "{{ __('blog::blog.states.archived') }}",
            },
            messages: {
                created: "{{ __('blog::blog.messages.created') }}",
                updated: "{{ __('blog::blog.messages.updated') }}",
                deleted: "{{ __('blog::blog.messages.deleted') }}",
                bulkDeleted: "{{ __('blog::blog.messages.bulk_deleted') }}",
                galleryDeleted: "{{ __('blog::blog.messages.gallery_deleted') }}",
                tagCreated: "{{ __('blog::blog.messages.tag_created') }}",
            },
            i18n: {
                view: "{{ __('blog::blog.actions.view') }}",
                edit: "{{ __('blog::blog.actions.edit') }}",
                delete: "{{ __('blog::blog.actions.delete') }}",
            },
            confirm: {
                deleteTitle: "{{ __('blog::blog.actions.delete') }}",
                deleteMessage: "{{ __('blog::blog.actions.confirm_delete') }}",
                bulkTitle: "{{ __('blog::blog.actions.bulk_delete') }}",
                bulkMessage: "{{ __('blog::blog.actions.bulk_delete_confirm') }}",
                confirm: "{{ __('blog::blog.actions.confirm') }}",
                cancel: "{{ __('blog::blog.actions.cancel') }}",
            },
            locale: "{{ app()->getLocale() }}",
            locales: @json($availableLocales),
        };
    </script>
    @vite('Modules/Blog/resources/assets/js/blog.js')
@endpush
