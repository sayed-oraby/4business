@extends('layouts.dashboard.master')

@section('title', __('reports::reports.title'))
@section('page-title', __('reports::reports.title'))

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
    {{-- Page Toolbar --}}
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-wrap align-items-center">
            <h1 class="fw-bold me-5 my-1">{{ __('reports::reports.title') }}</h1>
            <span class="text-gray-400 fw-semibold fs-7">{{ __('reports::reports.description') }}</span>
        </div>
    </div>

    {{-- Statistics Row --}}
    <div class="row g-5 g-xl-8">
        {{-- Total Posts --}}
        <div class="col-xl-4">
            <a href="{{ route('dashboard.reports.posts') }}" class="card bg-body hoverable stat-widget card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-duotone ki-chart-simple text-primary fs-2x ms-n1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ number_format($postsSummary['total'] ?? 0) }}
                    </div>
                    <div class="fw-semibold text-gray-400">{{ __('reports::reports.reports.posts.total') }}</div>
                    <span class="badge badge-light-success fs-base mt-3">
                        <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ number_format($postsSummary['active'] ?? 0) }} {{ __('reports::reports.reports.posts.active') }}
                    </span>
                </div>
            </a>
        </div>

        {{-- Job Offers --}}
        {{-- <div class="col-xl-4">
            <a href="{{ route('dashboard.reports.job-offers') }}" class="card bg-body hoverable stat-widget card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-duotone ki-handshake text-success fs-2x ms-n1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ number_format($jobOffersSummary['total_offers'] ?? 0) }}
                    </div>
                    <div class="fw-semibold text-gray-400">{{ __('reports::reports.reports.job_offers.total') }}</div>
                    <span class="badge badge-light-success fs-base mt-3">
                        <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ number_format($jobOffersSummary['acceptance_rate'] ?? 0) }}% {{ __('reports::reports.reports.job_offers.acceptance_rate') }}
                    </span>
                </div>
            </a>
        </div> --}}

        {{-- Total Members --}}
        <div class="col-xl-4">
            <a href="{{ route('dashboard.reports.members') }}" class="card bg-body hoverable stat-widget card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-duotone ki-profile-circle text-info fs-2x ms-n1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ number_format($usersSummary['total_users'] ?? 0) }}
                    </div>
                    <div class="fw-semibold text-gray-400">{{ __('reports::reports.reports.members.total') }}</div>
                    <span class="badge badge-light-success fs-base mt-3">
                        <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ number_format($usersSummary['new_registrations'] ?? 0) }} {{ __('reports::reports.reports.members.new') }}
                    </span>
                </div>
            </a>
        </div>

        {{-- Revenue --}}
        <div class="col-xl-4">
            <a href="{{ route('dashboard.reports.revenue') }}" class="card bg-body hoverable stat-widget card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-duotone ki-dollar text-warning fs-2x ms-n1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ number_format($financialSummary['total_revenue'] ?? 0, 2) }} KWD
                    </div>
                    <div class="fw-semibold text-gray-400">{{ __('reports::reports.reports.financial.total_revenue') }}</div>
                    <span class="badge badge-light-primary fs-base mt-3">
                        <i class="ki-duotone ki-chart-pie-simple fs-5 text-primary ms-n1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ number_format($financialSummary['paid_posts_count'] ?? 0) }} {{ __('reports::reports.reports.financial.paid_posts') }}
                    </span>
                </div>
            </a>
        </div>
    </div>

    {{-- Reports Cards --}}
    <div class="row g-5 g-xl-8">

        {{-- Posts Report --}}
        <div class="col-xxl-12">
            <div class="card card-flush h-md-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('reports::reports.reports.posts.title') }}</span>
                        <span class="text-gray-400 mt-1 fw-semibold fs-7">{{ __('reports::reports.reports.posts.description') }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ route('dashboard.reports.posts') }}" class="btn btn-sm btn-light-primary">
                            {{ __('reports::reports.view_details') }}
                            <i class="ki-duotone ki-arrow-right fs-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </a>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end px-0 pt-3 pb-5">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-800 w-100 mt-auto mb-3 px-9">
                            <span>{{ __('reports::reports.reports.posts.active') }}</span>
                            <span>{{ number_format($postsSummary['active'] ?? 0) }}</span>
                        </div>
                        <div class="h-8px mx-3 w-100 bg-light-success rounded">
                            <div class="bg-success rounded h-8px" role="progressbar" style="width: {{ $postsSummary['total'] > 0 ? ($postsSummary['active'] / $postsSummary['total'] * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end px-0 pt-0 pb-5">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-800 w-100 mt-auto mb-3 px-9">
                            <span>{{ __('reports::reports.reports.posts.pending') }}</span>
                            <span>{{ number_format($postsSummary['pending'] ?? 0) }}</span>
                        </div>
                        <div class="h-8px mx-3 w-100 bg-light-warning rounded">
                            <div class="bg-warning rounded h-8px" role="progressbar" style="width: {{ $postsSummary['total'] > 0 ? ($postsSummary['pending'] / $postsSummary['total'] * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end px-0 pt-0 pb-5">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-800 w-100 mt-auto mb-3 px-9">
                            <span>{{ __('reports::reports.reports.posts.expired') }}</span>
                            <span>{{ number_format($postsSummary['expired'] ?? 0) }}</span>
                        </div>
                        <div class="h-8px mx-3 w-100 bg-light-danger rounded">
                            <div class="bg-danger rounded h-8px" role="progressbar" style="width: {{ $postsSummary['total'] > 0 ? ($postsSummary['expired'] / $postsSummary['total'] * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Job Offers Report --}}
        {{-- <div class="col-xxl-6">
            <div class="card card-flush h-md-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('reports::reports.reports.job_offers.title') }}</span>
                        <span class="text-gray-400 mt-1 fw-semibold fs-7">{{ __('reports::reports.reports.job_offers.description') }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ route('dashboard.reports.job-offers') }}" class="btn btn-sm btn-light-success">
                            {{ __('reports::reports.view_details') }}
                            <i class="ki-duotone ki-arrow-right fs-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </a>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end px-0 pt-3 pb-5">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-800 w-100 mt-auto mb-3 px-9">
                            <span>{{ __('reports::reports.statuses.accepted') }}</span>
                            <span>{{ number_format($jobOffersSummary['accepted'] ?? 0) }}</span>
                        </div>
                        <div class="h-8px mx-3 w-100 bg-light-success rounded">
                            <div class="bg-success rounded h-8px" role="progressbar" style="width: {{ ($jobOffersSummary['total_offers'] ?? 0) > 0 ? (($jobOffersSummary['accepted'] ?? 0) / $jobOffersSummary['total_offers'] * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end px-0 pt-0 pb-5">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-800 w-100 mt-auto mb-3 px-9">
                            <span>{{ __('reports::reports.statuses.pending') }}</span>
                            <span>{{ number_format($jobOffersSummary['pending'] ?? 0) }}</span>
                        </div>
                        <div class="h-8px mx-3 w-100 bg-light-warning rounded">
                            <div class="bg-warning rounded h-8px" role="progressbar" style="width: {{ ($jobOffersSummary['total_offers'] ?? 0) > 0 ? (($jobOffersSummary['pending'] ?? 0) / $jobOffersSummary['total_offers'] * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end px-0 pt-0 pb-5">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-800 w-100 mt-auto mb-3 px-9">
                            <span>{{ __('reports::reports.statuses.rejected') }}</span>
                            <span>{{ number_format($jobOffersSummary['rejected'] ?? 0) }}</span>
                        </div>
                        <div class="h-8px mx-3 w-100 bg-light-danger rounded">
                            <div class="bg-danger rounded h-8px" role="progressbar" style="width: {{ ($jobOffersSummary['total_offers'] ?? 0) > 0 ? (($jobOffersSummary['rejected'] ?? 0) / $jobOffersSummary['total_offers'] * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- Members Growth --}}
        <div class="col-xxl-6">
            <div class="card card-flush h-md-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('reports::reports.reports.members.title') }}</span>
                        <span class="text-gray-400 mt-1 fw-semibold fs-7">{{ __('reports::reports.reports.members.description') }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ route('dashboard.reports.members') }}" class="btn btn-sm btn-light-info">
                            {{ __('reports::reports.view_details') }}
                            <i class="ki-duotone ki-arrow-right fs-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </a>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pb-0 px-0">
                    <div class="w-100">
                        <div class=" px-9 pt-5 pb-11">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fs-3x fw-bold text-gray-800 me-2 lh-1 ls-n2">{{ number_format($usersSummary['growth_rate'] ?? 0, 1) }}%</span>
                                <span class="badge badge-light-{{ ($usersSummary['growth_rate'] ?? 0) >= 0 ? 'success' : 'danger' }} fs-base">
                                    <i class="ki-duotone ki-arrow-{{ ($usersSummary['growth_rate'] ?? 0) >= 0 ? 'up' : 'down' }} fs-5 text-{{ ($usersSummary['growth_rate'] ?? 0) >= 0 ? 'success' : 'danger' }} ms-n1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ __('reports::reports.reports.members.growth_rate') }}
                                </span>
                            </div>
                            <div class="text-gray-400 fw-semibold fs-6">{{ __('reports::reports.reports.members.new') }}: {{ number_format($usersSummary['new_registrations'] ?? 0) }}</div>
                        </div>
                        <div class="separator separator-dashed"></div>
                        <div class="d-flex flex-stack px-9 py-5">
                            <div class="me-5">
                                <span class="text-gray-800 fw-bold d-block">{{ __('reports::reports.reports.members.total') }}</span>
                                <span class="text-gray-400 fw-semibold fs-6">{{ __('reports::reports.all_registered_members') }}</span>
                            </div>
                            <div class="d-flex align-items-senter">
                                <span class="text-gray-800 fw-bolder fs-2x px-6">{{ number_format($usersSummary['total_users'] ?? 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Financial Summary --}}
        <div class="col-xxl-6">
            <div class="card card-flush h-md-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('reports::reports.reports.financial.title') }}</span>
                        <span class="text-gray-400 mt-1 fw-semibold fs-7">{{ __('reports::reports.reports.financial.description') }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ route('dashboard.reports.revenue') }}" class="btn btn-sm btn-light-warning">
                            {{ __('reports::reports.view_details') }}
                            <i class="ki-duotone ki-arrow-right fs-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </a>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pb-0 px-0">
                    <div class="w-100">
                        <div class="px-9 pt-5 pb-11">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fs-3x fw-bold text-gray-800 me-2 lh-1 ls-n2">{{ number_format($financialSummary['total_revenue'] ?? 0, 2) }} KWD</span>
                                <span class="badge badge-light-primary fs-base">
                                    <i class="ki-duotone ki-chart-pie-simple fs-5 text-primary ms-n1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ __('reports::reports.total_revenue') }}
                                </span>
                            </div>
                            <div class="text-gray-400 fw-semibold fs-6">{{ __('reports::reports.reports.financial.paid_posts') }}: {{ number_format($financialSummary['paid_posts_count'] ?? 0) }}</div>
                        </div>
                        <div class="separator separator-dashed"></div>
                        <div class="d-flex flex-stack px-9 py-5">
                            <div class="me-5">
                                <span class="text-gray-800 fw-bold d-block">{{ __('reports::reports.reports.financial.arpu') }}</span>
                                <span class="text-gray-400 fw-semibold fs-6">{{ __('reports::reports.average_revenue_per_user') }}</span>
                            </div>
                            <div class="d-flex align-items-senter">
                                <span class="text-gray-800 fw-bolder fs-2x px-6">{{ number_format($financialSummary['average_revenue_per_user'] ?? 0, 2) }} KWD</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
