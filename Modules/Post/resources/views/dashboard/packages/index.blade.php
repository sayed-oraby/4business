@extends('layouts.dashboard.master')

@section('title', __('post::post.packages.title'))
@section('page-title', __('post::post.packages.title'))

@push('styles')
<style>
    .post-hero {
        background: linear-gradient(135deg, #eff4ff 0%, #ffffff 100%);
        border-radius: 1.5rem;
    }

    .post-table-card .table thead tr {
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .04em;
    }

    .post-table-card .table thead th,
    .post-table-card .table tbody td {
        text-align: center !important;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-10 post-hero">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-7">
                <div>
                    <span class="badge badge-light-primary mb-3">{{ __('post::post.title') }}</span>
                    <h2 class="fw-bold text-gray-900 mb-2">{{ __('post::post.packages.title') }}</h2>
                    <p class="text-gray-600 mb-0">{{ __('post::post.packages.description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-flush shadow-sm mb-10 post-table-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">{{ __('post::post.packages.title') }}</h3>
            </div>
            <div class="card-toolbar gap-3">
                <div class="position-relative my-1">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12" id="package-search" placeholder="{{ __('post::post.packages.search_placeholder') }}">
                </div>
                <button class="btn btn-primary btn-flex align-items-center gap-2" data-package-action="open-form">
                    <i class="ki-duotone ki-plus fs-2"></i>{{ __('post::post.actions.create') }}
                </button>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4" id="packages-table">
                    <thead class="bg-transparent text-gray-500 fw-semibold">
                        <tr class="text-center align-middle">
                            <th class="min-w-200px text-start">{{ __('post::post.packages.table.title') }}</th>
                            <th class="min-w-100px text-center">{{ __('post::post.packages.table.price') }}</th>
                            <th class="min-w-100px text-center">{{ __('post::post.packages.table.period') }}</th>
                            <th class="min-w-100px text-center">{{ __('post::post.packages.table.top_days') }}</th>
                            <th class="min-w-100px text-center">{{ __('post::post.packages.table.is_free') }}</th>
                            <th class="min-w-100px text-center">{{ __('post::post.packages.table.status') }}</th>
                            <th class="text-center pe-4 min-w-100px">{{ __('post::post.packages.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    @include('post::dashboard.packages.partials.form')
@endsection

@push('scripts')
    <script>
        window.PackageModule = {
            routes: {
                index: "{{ route('dashboard.packages.index') }}",
                store: "{{ route('dashboard.packages.store') }}",
                update: "{{ route('dashboard.packages.update', ['package' => '__ID__']) }}",
                destroy: "{{ route('dashboard.packages.destroy', ['package' => '__ID__']) }}",
            },
            messages: {
                created: "{{ __('post::post.messages.created') }}",
                updated: "{{ __('post::post.messages.updated') }}",
                deleted: "{{ __('post::post.messages.deleted') }}",
                create_title: "{{ __('post::post.packages.form.create_title') }}",
                edit_title: "{{ __('post::post.packages.form.edit_title') }}",
            },
            confirm: {
                deleteTitle: "{{ __('post::post.actions.delete') }}",
                deleteMessage: "{{ __('post::post.actions.confirm_delete') }}",
                confirm: "{{ __('post::post.actions.confirm') }}",
                cancel: "{{ __('post::post.actions.cancel') }}",
            },
            labels: {
                free: "{{ __('post::post.packages.labels.free') }}",
                paid: "{{ __('post::post.packages.labels.paid') }}",
                days: "{{ __('post::post.packages.table.period') == 'المدة (أيام)' ? 'يوم' : 'days' }}",
                days_top: "{{ __('post::post.packages.table.period') == 'المدة (أيام)' ? 'يوم مثبت' : 'days top' }}",
                credits: "{{ __('post::post.packages.table.period') == 'المدة (أيام)' ? 'إعلان' : 'ads' }}",
            },
            locale: "{{ app()->getLocale() }}",
        };
    </script>
    @vite('Modules/Post/resources/assets/js/package.js')
@endpush
