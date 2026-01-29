@extends('layouts.dashboard.master')

@section('title', __('dashboard.dashboard'))
@section('page-title', __('dashboard.dashboard'))

@push('styles')
<style>
    .stat-widget {
        transition: all 0.3s ease;
        border: 0;
    }
    .stat-widget:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075) !important;
    }
</style>
@endpush

@section('content')
    {{-- Welcome Banner --}}
    <div class="card bg-light mb-8">
        <div class="card-body py-8 px-10">
            <div class="d-flex align-items-center">
                <div class="symbol symbol-60px me-5">
                    <span class="symbol-label bg-primary">
                        <i class="ki-outline ki-user text-white fs-2x"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <span class="text-gray-800 fw-bold fs-2">{{ __('dashboard.hero_title') }}, {{ auth('admin')->user()->name ?? 'Super Admin' }}!</span>
                    <p class="text-gray-600 fw-semibold fs-6 mb-0 mt-1">{{ __('dashboard.hero_description') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Row --}}
    <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
        {{-- Active Posts --}}
        <div class="col-xl-3">
            <div class="card bg-body hoverable stat-widget card-xl-stretch">
                <div class="card-body">
                    <i class="ki-outline ki-chart-simple text-primary fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ \Modules\Post\Models\Post::where('status', 'approved')->count() }}
                    </div>
                    <div class="fw-semibold text-gray-400">{{ __('dashboard.stats.active_posts') }}</div>
                </div>
            </div>
        </div>

        {{-- Pending Posts --}}
        <div class="col-xl-3">
            <div class="card bg-body hoverable stat-widget card-xl-stretch">
                <div class="card-body">
                    <i class="ki-outline ki-time text-warning fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ \Modules\Post\Models\Post::where('status', 'pending')->count() }}
                    </div>
                    <div class="fw-semibold text-gray-400">{{ __('dashboard.stats.pending_posts') }}</div>
                </div>
            </div>
        </div>

        {{-- Total Users --}}
        <div class="col-xl-3">
            <div class="card bg-body hoverable stat-widget card-xl-stretch">
                <div class="card-body">
                    <i class="ki-outline ki-profile-circle text-info fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ \App\Models\User::count() }}
                    </div>
                    <div class="fw-semibold text-gray-400">{{ __('dashboard.stats.total_users') }}</div>
                </div>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="col-xl-3">
            <div class="card bg-body hoverable stat-widget card-xl-stretch">
                <div class="card-body">
                    <i class="ki-outline ki-dollar text-success fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ number_format(\Modules\Post\Models\Post::where('is_paid', 1)->sum('price') ?? 0, 2) }} KWD
                    </div>
                    <div class="fw-semibold text-gray-400">{{ __('dashboard.stats.total_revenue') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions & Recent Activity --}}
    <div class="row g-5 g-xl-8">
        {{-- Quick Actions --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('dashboard.quick_actions') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('dashboard.quick_actions_desc') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="d-flex flex-column gap-5">
                        <a href="{{ route('dashboard.posts.index') }}" class="d-flex align-items-center p-5 bg-light-primary rounded">
                            <i class="ki-outline ki-document text-primary fs-2x me-4"></i>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-gray-800 fs-6">{{ __('dashboard.actions.manage_posts') }}</div>
                                <div class="text-gray-600 fw-semibold fs-7">{{ __('dashboard.actions.manage_posts_desc') }}</div>
                            </div>
                            <i class="ki-outline ki-arrow-right text-primary fs-2"></i>
                        </a>
                        <a href="{{ route('dashboard.users.index') }}" class="d-flex align-items-center p-5 bg-light-info rounded">
                            <i class="ki-outline ki-people text-info fs-2x me-4"></i>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-gray-800 fs-6">{{ __('dashboard.actions.manage_users') }}</div>
                                <div class="text-gray-600 fw-semibold fs-7">{{ __('dashboard.actions.manage_users_desc') }}</div>
                            </div>
                            <i class="ki-outline ki-arrow-right text-info fs-2"></i>
                        </a>
                        <a href="{{ route('dashboard.reports.index') }}" class="d-flex align-items-center p-5 bg-light-success rounded">
                            <i class="ki-outline ki-chart-pie-simple text-success fs-2x me-4"></i>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-gray-800 fs-6">{{ __('dashboard.actions.view_reports') }}</div>
                                <div class="text-gray-600 fw-semibold fs-7">{{ __('dashboard.actions.view_reports_desc') }}</div>
                            </div>
                            <i class="ki-outline ki-arrow-right text-success fs-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Posts --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('dashboard.recent_posts') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('dashboard.recent_posts_desc') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                    <th class="p-0 pb-3">{{ __('dashboard.table.title') }}</th>
                                    <th class="p-0 pb-3 text-end">{{ __('dashboard.table.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\Modules\Post\Models\Post::latest()->take(5)->get() as $post)
                                    <tr>
                                        <td class="ps-0">
                                            <span class="text-gray-800 fw-bold">
                                                {{ is_array($post->title) ? ($post->title[app()->getLocale()] ?? $post->title['en'] ?? 'Unknown') : $post->title }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="badge badge-{{ ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'][$post->status] ?? 'secondary' }}">
                                                {{ ucfirst($post->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-gray-400 py-10">
                                            {{ __('dashboard.no_posts') }}
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
