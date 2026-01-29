@extends('layouts.dashboard.master')

@section('title', __('reports::reports.reports.job_offers.title'))
@section('page-title', __('reports::reports.reports.job_offers.title'))

@section('content')
    {{-- Page Toolbar --}}
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-wrap align-items-center">
            <h1 class="fw-bold me-5 my-1">{{ __('reports::reports.reports.job_offers.title') }}</h1>
            <span class="text-gray-400 fw-semibold fs-7">{{ __('reports::reports.reports.job_offers.description') }}</span>
        </div>
        <div class="d-flex my-2">
            <a href="{{ route('dashboard.reports.index') }}" class="btn btn-sm btn-light me-3">
                <i class="ki-outline ki-arrow-left fs-5"></i>
                {{ __('reports::reports.back_to_reports') }}
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
        @php
            $stats = [
                ['key' => 'total_offers', 'icon' => 'handshake', 'color' => 'primary', 'label' => __('reports::reports.reports.job_offers.total')],
                ['key' => 'acceptance_rate', 'icon' => 'check-circle', 'color' => 'success', 'label' => __('reports::reports.reports.job_offers.acceptance_rate'), 'suffix' => '%'],
                ['key' => 'average_salary', 'icon' => 'dollar', 'color' => 'info', 'label' => __('reports::reports.reports.job_offers.avg_salary'), 'format' => 'money'],
                ['key' => 'pending', 'icon' => 'time', 'color' => 'warning', 'label' => __('reports::reports.reports.job_offers.pending')],
            ];
        @endphp
        @foreach($stats as $stat)
            <div class="col">
                <div class="card bg-light-{{ $stat['color'] }} hoverable card-xl-stretch mb-xl-8">
                    <div class="card-body">
                        <i class="ki-outline ki-{{ $stat['icon'] }} text-{{ $stat['color'] }} fs-2x ms-n1"></i>
                        <div class="text-{{ $stat['color'] }} fw-bold fs-2 mb-2 mt-5">
                            @if(isset($stat['format']) && $stat['format'] === 'money')
                                {{ number_format($summary[$stat['key']] ?? 0, 2) }} KWD
                            @else
                                {{ number_format($summary[$stat['key']] ?? 0) }}{{ $stat['suffix'] ?? '' }}
                            @endif
                        </div>
                        <div class="fw-semibold text-gray-600">{{ $stat['label'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-5 g-xl-8">
        {{-- Offers by Status --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('reports::reports.reports.job_offers.by_status') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('reports::reports.reports.job_offers.status_breakdown') }}</span>
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
                                            <span class="badge badge-{{ ['accepted' => 'success', 'pending' => 'warning', 'rejected' => 'danger'][$status] ?? 'info' }}">
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

        {{-- Top Employers --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('reports::reports.reports.job_offers.top_employers') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('reports::reports.reports.job_offers.employers_description') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                    <th class="p-0 pb-3 min-w-150px text-start">{{ __('reports::reports.user') }}</th>
                                    <th class="p-0 pb-3 min-w-100px text-end">{{ __('reports::reports.reports.job_offers.offers_sent') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topEmployers as $item)
                                    <tr>
                                        <td class="ps-0">
                                            <span class="text-gray-800 fw-bold">{{ $item['user_name'] }}</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="badge badge-light-success fw-bold">{{ number_format($item['offers_count']) }}</span>
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

        {{-- Posts with Most Offers --}}
        <div class="col-xl-12">
            <div class="card card-flush">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('reports::reports.reports.job_offers.popular_posts') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('reports::reports.reports.job_offers.posts_description') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                    <th class="p-0 pb-3">{{ __('reports::reports.reports.job_offers.post_title') }}</th>
                                    <th class="p-0 pb-3">{{ __('reports::reports.reports.job_offers.post_owner') }}</th>
                                    <th class="p-0 pb-3 text-end">{{ __('reports::reports.reports.job_offers.offers_received') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPosts as $item)
                                    <tr>
                                        <td class="ps-0">
                                            <span class="text-gray-800 fw-bold">
                                                {{ is_array($item['post_title']) ? ($item['post_title'][app()->getLocale()] ?? $item['post_title']['en'] ?? 'Unknown') : $item['post_title'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-gray-600">{{ $item['post_owner'] }}</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="badge badge-light-primary fw-bold">{{ number_format($item['offers_count']) }}</span>
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
