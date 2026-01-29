@extends('layouts.dashboard.master')

@section('title', __('reports::reports.reports.members.title'))
@section('page-title', __('reports::reports.reports.members.title'))

@section('content')
    {{-- Page Toolbar --}}
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-wrap align-items-center">
            <h1 class="fw-bold me-5 my-1">{{ __('reports::reports.reports.members.title') }}</h1>
            <span class="text-gray-400 fw-semibold fs-7">{{ __('reports::reports.reports.members.description') }}</span>
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
                ['key' => 'total_users', 'icon' => 'people', 'color' => 'primary', 'label' => __('reports::reports.reports.members.total')],
                ['key' => 'new_registrations', 'icon' => 'user-tick', 'color' => 'success', 'label' => __('reports::reports.reports.members.new')],
                ['key' => 'active_users', 'icon' => 'profile-circle', 'color' => 'info', 'label' => __('reports::reports.reports.members.active')],
                ['key' => 'growth_rate', 'icon' => 'chart-line-up', 'color' => 'warning', 'label' => __('reports::reports.reports.members.growth_rate'), 'suffix' => '%', 'decimal' => 1],
            ];
        @endphp
        @foreach($stats as $stat)
            <div class="col">
                <div class="card bg-light-{{ $stat['color'] }} hoverable card-xl-stretch mb-xl-8">
                    <div class="card-body">
                        <i class="ki-outline ki-{{ $stat['icon'] }} text-{{ $stat['color'] }} fs-2x ms-n1"></i>
                        <div class="text-{{ $stat['color'] }} fw-bold fs-2 mb-2 mt-5">
                            {{ number_format($summary[$stat['key']] ?? 0, $stat['decimal'] ?? 0) }}{{ $stat['suffix'] ?? '' }}
                        </div>
                        <div class="fw-semibold text-gray-600">{{ $stat['label'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- User Registrations Over Time --}}
    <div class="row g-5 g-xl-8">
        <div class="col-xl-12">
            <div class="card card-flush">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">{{ __('reports::reports.reports.members.registrations_over_time') }}</span>
                        <span class="text-gray-400 fw-semibold fs-7 mt-1">{{ __('reports::reports.reports.members.timeline_description') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                    <th class="p-0 pb-3 min-w-150px text-start">{{ __('reports::reports.reports.members.period') }}</th>
                                    <th class="p-0 pb-3 min-w-100px text-end">{{ __('reports::reports.reports.members.registrations') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($overTime as $item)
                                    <tr>
                                        <td class="ps-0">
                                            <div class="d-flex align-items-center">
                                                <i class="ki-outline ki-calendar text-primary fs-3 me-3"></i>
                                                <span class="text-gray-800 fw-bold">{{ $item['period'] }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="badge badge-light-success fs-7 fw-bold px-4 py-2">{{ number_format($item['count']) }}</span>
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
    </div>
@endsection
