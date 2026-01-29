@extends('layouts.dashboard.master')

@section('title', __('reports::reports.reports.posts.title'))
@section('page-title', __('reports::reports.reports.posts.title'))

@section('content')
    {{-- Page Toolbar --}}
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-wrap align-items-center">
            <h1 class="fw-bold me-5 my-1">{{ __('reports::reports.reports.posts.title') }}</h1>
            <span class="text-gray-400 fw-semibold fs-7">{{ __('reports::reports.reports.posts.description') }}</span>
        </div>
        <div class="d-flex my-2">
            <a href="{{ route('dashboard.reports.index') }}" class="btn btn-sm btn-light me-3">
                <i class="ki-outline ki-arrow-left fs-5"></i>
                {{ __('Back to Reports') }}
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
        @php
            $stats = [
                ['key' => 'total', 'icon' => 'abstract-26', 'color' => 'primary', 'label' => __('reports::reports.reports.posts.total')],
                ['key' => 'active', 'icon' => 'check-circle', 'color' => 'success', 'label' => __('reports::reports.reports.posts.active')],
                ['key' => 'pending', 'icon' => 'time', 'color' => 'warning', 'label' => __('reports::reports.reports.posts.pending')],
                ['key' => 'expired', 'icon' => 'cross-circle', 'color' => 'danger', 'label' => __('reports::reports.reports.posts.expired')],
                ['key' => 'featured', 'icon' => 'star', 'color' => 'info', 'label' => __('reports::reports.reports.posts.featured')],
            ];
        @endphp
        @foreach($stats as $stat)
            <div class="col">
                <div class="card bg-light-{{ $stat['color'] }} hoverable card-xl-stretch mb-xl-8">
                    <div class="card-body">
                        <i class="ki-outline ki-{{ $stat['icon'] }} text-{{ $stat['color'] }} fs-2x ms-n1"></i>
                        <div class="text-{{ $stat['color'] }} fw-bold fs-2 mb-2 mt-5">
                            {{ number_format($summary[$stat['key']] ?? 0) }}
                        </div>
                        <div class="fw-semibold text-gray-600">{{ $stat['label'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-5 g-xl-8">
        {{-- Posts by Status --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('reports::reports.reports.posts.by_status') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('reports::reports.reports.posts.status_breakdown') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                    <th class="p-0 pb-3 min-w-150px text-start">{{ __('reports::reports.status') }}</th>
                                    <th class="p-0 pb-3 min-w-100px text-end">{{ __('reports::reports.count') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($byStatus as $status => $count)
                                    <tr>
                                        <td class="ps-0">
                                            <span class="badge badge-{{ ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger', 'expired' => 'secondary'][$status] ?? 'info' }}">
                                                {{ __('reports::reports.statuses.' . $status) }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold fs-6">{{ number_format($count) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Posts by Category --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('reports::reports.reports.posts.by_category') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('reports::reports.reports.posts.category_distribution') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                    <th class="p-0 pb-3 min-w-150px text-start">{{ __('reports::reports.category') }}</th>
                                    <th class="p-0 pb-3 min-w-100px text-end">{{ __('reports::reports.posts_count') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($byCategory as $item)
                                    <tr>
                                        <td class="ps-0">
                                            <span class="text-gray-800 fw-bold">{{ $item['category'] }}</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="badge badge-light-primary fw-bold">{{ number_format($item['count']) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-gray-400 py-10">
                                            {{ __('reports::reports.no_data') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Featured vs Regular --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('reports::reports.reports.posts.post_types') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('reports::reports.reports.posts.featured_vs_regular') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">{{ __('reports::reports.reports.posts.regular_posts') }}</div>
                        <div class="d-flex align-items-senter">
                            <span class="text-gray-900 fw-bolder fs-6">{{ number_format($byType['regular'] ?? 0) }}</span>
                        </div>
                    </div>
                    <div class="separator separator-dashed my-5"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">{{ __('reports::reports.reports.posts.featured_posts') }}</div>
                        <div class="d-flex align-items-senter">
                            <span class="text-gray-900 fw-bolder fs-6">{{ number_format($byType['featured'] ?? 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Cities --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('reports::reports.reports.posts.top_cities') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('reports::reports.reports.posts.geographic_distribution') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                    <th class="p-0 pb-3 min-w-150px text-start">{{ __('reports::reports.city') }}</th>
                                    <th class="p-0 pb-3 min-w-100px text-end">{{ __('reports::reports.posts_count') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($byCity as $item)
                                    <tr>
                                        <td class="ps-0">
                                            <span class="text-gray-800 fw-bold">{{ $item['city'] }}</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="badge badge-light-success fw-bold">{{ number_format($item['count']) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-gray-400 py-10">
                                            {{ __('reports::reports.no_data') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Most Active Users --}}
        <div class="col-xl-12">
            <div class="card card-flush">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('reports::reports.reports.posts.most_active_users') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('reports::reports.reports.posts.top_contributors') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                    <th class="p-0 pb-3 w-50px">#</th>
                                    <th class="p-0 pb-3 min-w-150px">{{ __('reports::reports.user') }}</th>
                                    <th class="p-0 pb-3 min-w-100px text-end">{{ __('reports::reports.posts_count') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mostActive as $index => $item)
                                    <tr>
                                        <td class="ps-0">
                                            <span class="badge badge-light-primary fw-bold">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold">{{ $item['user_name'] }}</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="badge badge-success fw-bold">{{ number_format($item['posts_count']) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-gray-400 py-10">
                                            {{ __('reports::reports.no_data') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
