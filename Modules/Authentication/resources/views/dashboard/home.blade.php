@extends('layouts.dashboard.master')

@section('title', __('dashboard.dashboard'))
@section('page-title', __('dashboard.dashboard'))

@section('content')
    {{-- Page Toolbar --}}
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-wrap align-items-center">
            <h1 class="fw-bold me-5 my-1">{{ __('dashboard.dashboard_analytics') }}</h1>
            <span class="text-gray-400 fw-semibold fs-7">{{ __('dashboard.stats_overview') }}</span>
        </div>
    </div>

    {{-- Summary Cards - Same as Reports Page --}}
    <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
        @php
            $stats = [
                ['value' => $totalPosts, 'icon' => 'abstract-26', 'color' => 'primary', 'label' => __('dashboard.total_posts')],
                ['value' => $activePosts, 'icon' => 'check-circle', 'color' => 'success', 'label' => __('dashboard.active_posts')],
                ['value' => $pendingPosts, 'icon' => 'time', 'color' => 'warning', 'label' => __('dashboard.pending_posts')],
                ['value' => $expiredPosts, 'icon' => 'cross-circle', 'color' => 'danger', 'label' => __('dashboard.expired_posts')],
                ['value' => $featuredPosts, 'icon' => 'star', 'color' => 'info', 'label' => __('dashboard.featured_posts')],
            ];
        @endphp
        @foreach($stats as $stat)
            <div class="col">
                <div class="card bg-light-{{ $stat['color'] }} hoverable card-xl-stretch mb-xl-8">
                    <div class="card-body">
                        <i class="ki-outline ki-{{ $stat['icon'] }} text-{{ $stat['color'] }} fs-2x ms-n1"></i>
                        <div class="text-{{ $stat['color'] }} fw-bold fs-2 mb-2 mt-5">
                            {{ number_format($stat['value']) }}
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
                        <span class="card-label fw-bold text-dark">{{ __('dashboard.posts_by_status') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('dashboard.status_breakdown') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                    <th class="p-0 pb-3 min-w-150px text-start">{{ __('dashboard.status') }}</th>
                                    <th class="p-0 pb-3 min-w-100px text-end">{{ __('dashboard.count') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($byStatus as $status => $count)
                                    @if($count > 0 && $status !== 'awaiting_payment')
                                    <tr>
                                        <td class="ps-0">
                                            @php
                                                $badgeClass = match($status) {
                                                    'approved' => 'success',
                                                    'pending' => 'warning',
                                                    'rejected' => 'danger',
                                                    'expired' => 'secondary',
                                                    default => 'light'
                                                };
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">
                                                {{ __('post::post.statuses.' . $status) }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-800 fw-bold fs-6">{{ number_format($count) }}</span>
                                        </td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-gray-400 py-10">
                                            {{ __('dashboard.no_data') }}
                                        </td>
                                    </tr>
                                @endforelse
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
                        <span class="card-label fw-bold text-dark">{{ __('dashboard.posts_by_category') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('dashboard.category_distribution') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                    <th class="p-0 pb-3 min-w-150px text-start">{{ __('dashboard.category') }}</th>
                                    <th class="p-0 pb-3 min-w-100px text-end">{{ __('dashboard.posts_count') }}</th>
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
                                            {{ __('dashboard.no_data') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revenue & Members Summary --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('dashboard.financial_summary') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('dashboard.revenue_overview') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">{{ __('dashboard.total_revenue') }}</div>
                        <div class="d-flex align-items-center">
                            <span class="text-success fw-bolder fs-3">{{ number_format($totalRevenue, 3) }} {{ __('dashboard.kwd') }}</span>
                        </div>
                    </div>
                    <div class="separator separator-dashed my-5"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">{{ __('dashboard.total_members') }}</div>
                        <div class="d-flex align-items-center">
                            <span class="text-gray-900 fw-bolder fs-3">{{ number_format($totalMembers) }}</span>
                        </div>
                    </div>
                    <div class="separator separator-dashed my-5"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">{{ __('dashboard.featured_posts') }}</div>
                        <div class="d-flex align-items-center">
                            <span class="text-primary fw-bolder fs-3">{{ number_format($featuredPosts) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('dashboard.quick_actions') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('dashboard.manage_system') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="{{ route('dashboard.posts.index') }}" class="btn btn-light-primary w-100 py-4">
                                <i class="ki-outline ki-notepad-edit fs-2 mb-2"></i>
                                <span class="d-block fw-semibold">{{ __('dashboard.manage_posts') }}</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('dashboard.users.index') }}" class="btn btn-light-success w-100 py-4">
                                <i class="ki-outline ki-people fs-2 mb-2"></i>
                                <span class="d-block fw-semibold">{{ __('dashboard.manage_users') }}</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('dashboard.categories.index') }}" class="btn btn-light-warning w-100 py-4">
                                <i class="ki-outline ki-category fs-2 mb-2"></i>
                                <span class="d-block fw-semibold">{{ __('dashboard.manage_categories') }}</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('dashboard.reports.index') }}" class="btn btn-light-info w-100 py-4">
                                <i class="ki-outline ki-chart-simple fs-2 mb-2"></i>
                                <span class="d-block fw-semibold">{{ __('dashboard.view_reports') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Latest Posts Table --}}
        <div class="col-xl-12">
            <div class="card card-flush">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('dashboard.latest_posts_under_review') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('dashboard.posts_awaiting_review', ['count' => number_format($latestPendingPosts->count())]) }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ route('dashboard.posts.index') }}" class="btn btn-sm btn-light">
                            {{ __('dashboard.view_all') }}
                        </a>
                    </div>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                    <th class="p-0 pb-3 w-50px text-center">#</th>
                                    <th class="p-0 pb-3 min-w-200px">{{ __('dashboard.post') }}</th>
                                    <th class="p-0 pb-3 min-w-120px">{{ __('dashboard.user') }}</th>
                                    <th class="p-0 pb-3 min-w-100px">{{ __('dashboard.category') }}</th>
                                    <th class="p-0 pb-3 min-w-120px">{{ __('dashboard.package') }}</th>
                                    <th class="p-0 pb-3 min-w-100px">{{ __('dashboard.status') }}</th>
                                    <th class="p-0 pb-3 min-w-80px">{{ __('dashboard.date') }}</th>
                                    <th class="p-0 pb-3 min-w-80px text-end">{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestPendingPosts as $index => $post)
                                    <tr>
                                        {{-- # --}}
                                        <td class="ps-0 text-center">
                                            <span class="badge badge-light-primary fw-bold">{{ $index + 1 }}</span>
                                        </td>
                                        
                                        {{-- Post with Cover --}}
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($post->cover_image_url)
                                                    <div class="symbol symbol-50px symbol-2by3 me-3">
                                                        <div class="symbol-label" style="background-image: url('{{ $post->cover_image_url }}'); background-size: cover; background-position: center;"></div>
                                                    </div>
                                                @else
                                                    <div class="symbol symbol-50px symbol-2by3 me-3">
                                                        <div class="symbol-label bg-light-primary">
                                                            <i class="ki-duotone ki-briefcase fs-2 text-primary">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="d-flex flex-column">
                                                    <a href="{{ route('dashboard.posts.show', $post->id) }}" class="text-gray-800 text-hover-primary fw-bold">
                                                        {{ Str::limit($post->title, 35) }}
                                                    </a>
                                                    <span class="text-gray-400 fs-7">{{ $post->postType->name ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        {{-- User --}}
                                        <td>
                                            <span class="text-gray-800 fw-semibold">{{ $post->user->name ?? '-' }}</span>
                                        </td>
                                        
                                        {{-- Category --}}
                                        <td>
                                            <span class="badge badge-light-info fw-bold">{{ $post->category->title ?? '-' }}</span>
                                        </td>
                                        
                                        {{-- Package with Paid/Free badge (only shows posts ready for review) --}}
                                        <td>
                                            @php
                                                $isPaidPackage = $post->package && $post->package->price > 0;
                                                $packageTitle = $post->package->title ?? '-';
                                            @endphp
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-semibold">{{ $packageTitle }}</span>
                                                @if($isPaidPackage)
                                                    <span class="badge badge-success mt-1" style="width: fit-content;">{{ __('post::post.labels.paid') }}</span>
                                                @else
                                                    <span class="badge badge-light-primary mt-1" style="width: fit-content;">{{ __('post::post.labels.free') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        {{-- Status --}}
                                        <td>
                                            @php
                                                $statusClass = match($post->status) {
                                                    'approved' => 'badge-light-success',
                                                    'pending' => 'badge-light-warning',
                                                    'rejected' => 'badge-light-danger',
                                                    'expired' => 'badge-light-secondary',
                                                    'awaiting_payment' => 'badge-light-info',
                                                    default => 'badge-light-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ __('post::post.statuses.' . $post->status) }}</span>
                                        </td>
                                        
                                        {{-- Date --}}
                                        <td>
                                            <span class="text-gray-600 fs-7">{{ $post->created_at->format('Y-m-d') }}</span>
                                        </td>
                                        
                                        {{-- Actions --}}
                                        <td class="text-end pe-0">
                                            <a href="{{ route('dashboard.posts.show', $post->id) }}" class="btn btn-sm btn-light btn-active-light-primary">
                                                {{ __('dashboard.view') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-gray-400 py-10">
                                            {{ __('dashboard.no_posts_under_review') }}
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
