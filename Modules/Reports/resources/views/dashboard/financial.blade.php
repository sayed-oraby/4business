@extends('layouts.dashboard.master')

@section('title', __('reports::reports.reports.financial.title'))
@section('page-title', __('reports::reports.reports.financial.title'))

@section('content')
    {{-- Page Toolbar --}}
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-wrap align-items-center">
            <h1 class="fw-bold me-5 my-1">{{ __('reports::reports.reports.financial.title') }}</h1>
            <span class="text-gray-400 fw-semibold fs-7">{{ __('reports::reports.reports.financial.description') }}</span>
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
                ['key' => 'total_revenue', 'icon' => 'chart-pie-3', 'color' => 'primary', 'label' => __('reports::reports.reports.financial.total_revenue'), 'format' => 'money'],
                ['key' => 'paid_posts_count', 'icon' => 'check-square', 'color' => 'success', 'label' => __('reports::reports.reports.financial.paid_posts')],
                ['key' => 'average_revenue_per_user', 'icon' => 'user', 'color' => 'info', 'label' => __('reports::reports.reports.financial.arpu'), 'format' => 'money'],
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
                                {{ number_format($summary[$stat['key']] ?? 0) }}
                            @endif
                        </div>
                        <div class="fw-semibold text-gray-600">{{ $stat['label'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-5 g-xl-8">
        {{-- Revenue by Package --}}
        <div class="col-xl-12">
            <div class="card card-flush h-xl-100 mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('reports::reports.reports.financial.by_package') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('reports::reports.reports.financial.package_breakdown') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                    <th class="p-0 pb-3 min-w-200px text-start">{{ __('reports::reports.reports.financial.package_name') }}</th>
                                    <th class="p-0 pb-3 min-w-100px text-center">{{ __('reports::reports.reports.financial.sales') }}</th>
                                    <th class="p-0 pb-3 min-w-120px text-end">{{ __('reports::reports.reports.financial.revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($byPackage as $item)
                                    <tr>
                                        <td class="ps-0">
                                            <div class="d-flex align-items-center">
                                                <i class="ki-outline ki-package text-warning fs-3 me-3"></i>
                                                <span class="text-gray-800 fw-bold">
                                                    {{ is_array($item['package_name']) ? ($item['package_name'][app()->getLocale()] ?? $item['package_name']['en'] ?? 'Unknown') : $item['package_name'] }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-light-success fw-bold">{{ number_format($item['sales_count']) }}</span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="text-gray-900 fw-bold fs-6">{{ number_format($item['total_revenue'], 2) }} KWD</span>
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

        {{-- Top Packages --}}
        @if(!empty($summary['top_packages']))
            @php
                $colors = ['primary', 'success', 'warning'];
            @endphp
            @foreach($summary['top_packages'] as $index => $package)
                @php
                    $color = $colors[$index] ?? 'info';
                @endphp
                <div class="col-xl-4">
                    <div class="card card-flush h-xl-100" style="border-left: 4px solid var(--bs-{{ $color }});">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-5">
                                <span class="badge badge-{{ $color }} fs-2 fw-bold me-3">#{{ $index + 1 }}</span>
                                <h4 class="fw-bold text-gray-800 mb-0">
                                    {{ is_array($package['package_name']) ? ($package['package_name'][app()->getLocale()] ?? $package['package_name']['en'] ?? 'Unknown') : $package['package_name'] }}
                                </h4>
                            </div>
                            <div class="separator separator-dashed my-4"></div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-gray-600 fw-semibold">{{ __('reports::reports.reports.financial.sales') }}</span>
                                <span class="badge badge-light-{{ $color }} fs-6 fw-bold px-3 py-2">{{ number_format($package['sales_count']) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-gray-600 fw-semibold">{{ __('reports::reports.reports.financial.revenue') }}</span>
                                <span class="text-gray-900 fw-bold fs-5">{{ number_format($package['total_revenue'], 2) }} KWD</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
