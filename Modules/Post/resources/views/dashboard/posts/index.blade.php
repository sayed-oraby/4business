@extends('layouts.dashboard.master')

@section('title', __('post::post.posts.title'))
@section('page-title', __('post::post.posts.title'))

@push('styles')
<style>
    .post-hero {
        background: linear-gradient(135deg, #eff4ff 0%, #ffffff 100%);
        border-radius: 1.5rem;
    }
    
    .post-hero__stats {
        min-width: 180px;
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
    {{-- Hero Section with Statistics --}}
    <div class="card border-0 shadow-sm mb-10 post-hero">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-7">
                <div>
                    <span class="badge badge-light-primary mb-3">{{ __('post::post.title') }}</span>
                    <h2 class="fw-bold text-gray-900 mb-2">{{ __('post::post.posts.title') }}</h2>
                    <p class="text-gray-600 mb-0">{{ __('post::post.description') }}</p>
                </div>
            </div>
            
            {{-- Statistics Cards --}}
            <div class="d-flex flex-wrap gap-6 mt-8">
                @php
                    $statBlocks = [
                        'total' => ['class' => 'text-dark', 'icon' => 'ki-briefcase', 'label' => __('post::post.stats.total')],
                        'active' => ['class' => 'text-success', 'icon' => 'ki-check-circle', 'label' => __('post::post.stats.active')],
                        'pending' => ['class' => 'text-warning', 'icon' => 'ki-time', 'label' => __('post::post.stats.pending')],
                        'expired' => ['class' => 'text-danger', 'icon' => 'ki-cross-circle', 'label' => __('post::post.stats.expired')],
                        'featured' => ['class' => 'text-primary', 'icon' => 'ki-star', 'label' => __('post::post.stats.featured')],
                    ];
                @endphp
                @foreach($statBlocks as $key => $meta)
                    <div class="post-hero__stats card border-0 shadow-sm flex-grow-1">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <span class="text-muted fw-semibold">{{ $meta['label'] }}</span>
                                <span class="symbol symbol-35px symbol-circle bg-light">
                                    <i class="ki-duotone {{ $meta['icon'] }} fs-2 {{ $meta['class'] }}">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div class="fs-1 fw-bolder text-gray-900">{{ number_format($stats[$key] ?? 0) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Posts Table Card --}}
    <div class="card card-flush shadow-sm mb-10 post-table-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">{{ __('post::post.posts.title') }}</h3>
            </div>
            <div class="card-toolbar gap-3">
                <div class="position-relative my-1">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12" id="post-search" placeholder="{{ __('post::post.search_placeholder') }}">
                </div>
                <button class="btn btn-light btn-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#postFiltersCanvas">
                    <i class="ki-duotone ki-filter fs-2"></i>{{ __('dashboard.filter_button') }}
                </button>
                <a href="{{ route('dashboard.post-types.index') }}" class="btn btn-light btn-flex align-items-center gap-2">
                    <i class="ki-duotone ki-category fs-2"></i>{{ __('post::post.types.title') }}
                </a>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4" id="posts-table">
                    <thead class="bg-transparent text-gray-500 fw-semibold">
                        <tr class="text-center align-middle">
                            <th class="min-w-200px text-center">{{ __('post::post.posts.table.title') }}</th>
                            <th class="min-w-150px text-center">{{ __('post::post.posts.table.user') }}</th>
                            <th class="min-w-120px text-center">{{ __('post::post.posts.table.type') }}</th>
                            <th class="min-w-120px text-center">{{ __('post::post.posts.table.package') }}</th>
                            <th class="min-w-120px text-center">{{ __('post::post.posts.table.status') }}</th>
                            <th class="min-w-150px text-center">{{ __('post::post.posts.table.date') }}</th>
                            <th class="text-center pe-4 min-w-100px">{{ __('post::post.posts.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Filter Offcanvas Sidebar --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="postFiltersCanvas">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-bold">{{ __('dashboard.filters_panel.title') }}</h5>
            <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="offcanvas">
                <i class="ki-duotone ki-cross fs-2"></i>
            </button>
        </div>
        <div class="offcanvas-body pt-5">
            <form id="post-filter-form" class="d-flex flex-column gap-5">
                {{-- Status Filter --}}
                <div>
                    <label class="form-label">{{ __('post::post.filters.status') }}</label>
                    <select class="form-select form-select-solid" name="status">
                        <option value="">{{ __('post::post.filters.all_statuses') }}</option>
                        <option value="pending">{{ __('post::post.statuses.pending') }}</option>
                        <option value="approved">{{ __('post::post.statuses.approved') }}</option>
                        <option value="rejected">{{ __('post::post.statuses.rejected') }}</option>
                        <option value="expired">{{ __('post::post.statuses.expired') }}</option>
                    </select>
                </div>
                
                {{-- Category Filter --}}
                <div>
                    <label class="form-label">{{ __('post::post.filters.category') }}</label>
                    <select class="form-select form-select-solid" name="category_id">
                        <option value="">{{ __('post::post.filters.all_categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->title }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Post Type Filter --}}
                <div>
                    <label class="form-label">{{ __('post::post.filters.post_type') }}</label>
                    <select class="form-select form-select-solid" name="post_type_id">
                        <option value="">{{ __('post::post.filters.all_types') }}</option>
                        @foreach($postTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Package Filter --}}
                <div>
                    <label class="form-label">{{ __('post::post.filters.package') }}</label>
                    <select class="form-select form-select-solid" name="package_id">
                        <option value="">{{ __('post::post.filters.all_packages') }}</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}">{{ $package->title }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Featured/Regular Filter --}}
                <div>
                    <label class="form-label">{{ __('post::post.filters.featured_status') }}</label>
                    <select class="form-select form-select-solid" name="is_paid">
                        <option value="">{{ __('post::post.filters.all') }}</option>
                        <option value="1">{{ __('post::post.filters.featured_only') }}</option>
                        <option value="0">{{ __('post::post.filters.regular_only') }}</option>
                    </select>
                </div>
                
                {{-- Gender Filter --}}
                <div>
                    <label class="form-label">{{ __('post::post.filters.gender') }}</label>
                    <select class="form-select form-select-solid" name="gender">
                        <option value="">{{ __('post::post.filters.all_genders') }}</option>
                        <option value="male">{{ __('post::post.form.male') }}</option>
                        <option value="female">{{ __('post::post.form.female') }}</option>
                        <option value="both">{{ __('post::post.form.both') }}</option>
                    </select>
                </div>
                
                {{-- City Filter --}}
                <div>
                    <label class="form-label">{{ __('post::post.filters.city') }}</label>
                    <select class="form-select form-select-solid" name="city_id">
                        <option value="">{{ __('post::post.filters.all_cities') }}</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Date Range --}}
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('dashboard.filters_panel.date_range') }} ({{ __('From') }})</label>
                        <input type="date" class="form-control form-control-solid" name="date_from">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('dashboard.filters_panel.date_range') }} ({{ __('To') }})</label>
                        <input type="date" class="form-control form-control-solid" name="date_to">
                    </div>
                </div>
                
                {{-- Action Buttons --}}
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
@endsection

@push('scripts')
    <script>
        window.PostModule = {
            routes: {
                index: "{{ route('dashboard.posts.index') }}",
                show: "{{ route('dashboard.posts.show', ['post' => '__ID__']) }}",
                destroy: "{{ route('dashboard.posts.destroy', ['post' => '__ID__']) }}",
            },
            messages: {
                deleted: "{{ __('post::post.messages.deleted') }}",
            },
            confirm: {
                deleteTitle: "{{ __('post::post.actions.delete') }}",
                deleteMessage: "{{ __('post::post.actions.confirm_delete') }}",
                confirm: "{{ __('post::post.actions.confirm') }}",
                cancel: "{{ __('post::post.actions.cancel') }}",
            },
            statuses: {
                pending: "{{ __('post::post.statuses.pending') }}",
                approved: "{{ __('post::post.statuses.approved') }}",
                rejected: "{{ __('post::post.statuses.rejected') }}",
                expired: "{{ __('post::post.statuses.expired') }}",
                awaiting_payment: "{{ __('post::post.statuses.awaiting_payment') }}",
                payment_failed: "{{ __('post::post.statuses.payment_failed') }}",
                active: "{{ __('post::post.statuses.active') }}",
                inactive: "{{ __('post::post.statuses.inactive') }}",
            },
            labels: {
                featured: "{{ __('post::post.labels.featured') }}",
                paid: "{{ __('post::post.labels.paid') }}",
                unpaid: "{{ __('post::post.labels.unpaid') }}",
                free: "{{ __('post::post.labels.free') }}",
            },
            locale: "{{ app()->getLocale() }}",
        };
    </script>
    @vite('Modules/Post/resources/assets/js/post.js')
@endpush
