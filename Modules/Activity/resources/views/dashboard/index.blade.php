@extends('layouts.dashboard.master')

@section('title', __('dashboard.logs_title'))
@section('page-title', __('dashboard.logs_title'))

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    #audit-log-table tbody tr:hover { background-color: #f8fafc; }
    .offcanvas.offcanvas-end,
    .offcanvas.offcanvas-start { width: 420px; }
</style>
@endpush

@section('content')
<div class="row g-5 g-xl-10">
    <div class="col-12">
        <div class="card card-flush shadow-sm">
            <div class="card-header align-items-center flex-wrap gap-3 py-5">
                <div class="d-flex flex-column">
                    <h3 class="card-title fw-bold mb-1">{{ __('dashboard.logs_title') }}</h3>
                    <span class="text-muted fs-7">{{ __('dashboard.logs_subtitle') }}</span>
                </div>
                <div class="card-toolbar ms-auto d-flex flex-wrap gap-3">
                    <div class="d-flex align-items-center position-relative">
                        <i class="ki-duotone ki-magnifier fs-2 position-absolute {{ app()->getLocale() === 'ar' ? 'me-5' : 'ms-5' }}">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" id="audit-log-search" class="form-control form-control-solid w-250px {{ app()->getLocale() === 'ar' ? 'pe-13' : 'ps-13' }}" placeholder="{{ __('dashboard.search_placeholder') }}" />
                    </div>
                    <button type="button"
                            id="auditLogFilters_toggle"
                            class="btn btn-light-primary"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#auditLogFilters">
                        <i class="ki-duotone ki-filter fs-2 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('dashboard.filter_button') }}
                    </button>
                </div>
            </div>
            <div class="card-body py-5 px-5">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0" id="audit-log-table">
                        <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>{{ __('dashboard.table.user') }}</th>
                            <th>{{ __('dashboard.table.action') }}</th>
                            <th>{{ __('dashboard.table.context') }}</th>
                            <th>{{ __('dashboard.table.ip') }}</th>
                            <th>{{ __('dashboard.table.device') }}</th>
                            <th class="min-w-140px">{{ __('dashboard.table.date') }}</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-{{ app()->getLocale() === 'ar' ? 'start' : 'end' }} border-0" tabindex="-1" id="auditLogFilters">
    <div class="offcanvas-header border-bottom">
        <h3 class="offcanvas-title fw-bold">{{ __('dashboard.filters_panel.title') }}</h3>
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="offcanvas">
            <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
        </button>
    </div>
    <div class="offcanvas-body py-5">
        <div class="mb-7">
            <label class="form-label fw-semibold">{{ __('dashboard.filters_panel.actions') }}</label>
            <select id="filter_actions" class="form-select" data-control="select2" data-placeholder="{{ __('dashboard.filter') }}" multiple data-allow-clear="true">
                @foreach($actions as $action)
                    <option value="{{ $action }}">{{ \Illuminate\Support\Facades\Lang::has('dashboard.actions_map.' . $action) ? __('dashboard.actions_map.' . $action) : $action }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-7">
            <label class="form-label fw-semibold">{{ __('dashboard.filters_panel.contexts') }}</label>
            <select id="filter_contexts" class="form-select" data-control="select2" data-placeholder="{{ __('dashboard.filters_panel.contexts') }}" multiple data-allow-clear="true">
                @foreach($contexts as $context)
                    <option value="{{ $context }}">
                        {{ \Illuminate\Support\Facades\Lang::has('dashboard.contexts.' . $context) ? __('dashboard.contexts.' . $context) : ($context ?? '—') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-7">
            <label class="form-label fw-semibold">{{ __('dashboard.filters_panel.user') }}</label>
            <select id="filter_user_id" class="form-select" data-control="select2" data-placeholder="{{ __('dashboard.filters_panel.user_placeholder') }}" data-allow-clear="true"></select>
        </div>
        <div class="mb-7">
            <label class="form-label fw-semibold">{{ __('dashboard.filters_panel.date_range') }}</label>
            <input type="text" id="filter_date_range" class="form-control form-control-solid" placeholder="{{ __('dashboard.filters_panel.date_range') }}" autocomplete="off" />
            <input type="hidden" id="filter_date_from">
            <input type="hidden" id="filter_date_to">
        </div>
        <div class="mb-7">
            <label class="form-label fw-semibold d-block mb-2">{{ __('dashboard.filters_panel.quick_ranges') }}</label>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-light btn-sm" data-range-shortcut="today">{{ __('dashboard.quick_ranges.today') }}</button>
                <button type="button" class="btn btn-light btn-sm" data-range-shortcut="yesterday">{{ __('dashboard.quick_ranges.yesterday') }}</button>
                <button type="button" class="btn btn-light btn-sm" data-range-shortcut="this_week">{{ __('dashboard.quick_ranges.this_week') }}</button>
                <button type="button" class="btn btn-light btn-sm" data-range-shortcut="last_week">{{ __('dashboard.quick_ranges.last_week') }}</button>
                <button type="button" class="btn btn-light btn-sm" data-range-shortcut="this_month">{{ __('dashboard.quick_ranges.this_month') }}</button>
                <button type="button" class="btn btn-light btn-sm" data-range-shortcut="last_month">{{ __('dashboard.quick_ranges.last_month') }}</button>
                <button type="button" class="btn btn-light btn-sm" data-range-shortcut="custom">{{ __('dashboard.quick_ranges.custom') }}</button>
            </div>
        </div>
    </div>
    <div class="offcanvas-footer d-flex gap-3 p-4 border-top">
        <button type="button" class="btn btn-primary w-100" data-apply-filters data-bs-dismiss="offcanvas">{{ __('dashboard.filters_panel.apply') }}</button>
        <button type="button" class="btn btn-light w-100" data-reset-filters>{{ __('dashboard.filters_panel.reset') }}</button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const offcanvasEl = document.getElementById('auditLogFilters');
    const offcanvas = offcanvasEl ? new bootstrap.Offcanvas(offcanvasEl) : null;

    $('#filter_actions').select2({
        dropdownParent: $('#auditLogFilters')
    });

    $('#filter_contexts').select2({
        dropdownParent: $('#auditLogFilters')
    });

    $('#filter_user_id').select2({
        dropdownParent: $('#auditLogFilters'),
        minimumInputLength: 1,
        ajax: {
            url: '{{ route('dashboard.audit-logs.users') }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
        },
        templateResult: function (result) {
            if (!result.id) {
                return result.text;
            }
            return $('<div class="d-flex flex-column"><span class="fw-bold">' + (result.name ?? result.text) + '</span><span class="text-muted fs-7">' + (result.email ?? '') + '</span></div>');
        },
        templateSelection: function (result) {
            return result.name ? result.name + ' — ' + result.email : result.text;
        }
    });

    const dateRangeInput = document.getElementById('filter_date_range');
    const filterDateFrom = document.getElementById('filter_date_from');
    const filterDateTo = document.getElementById('filter_date_to');

    const picker = flatpickr(dateRangeInput, {
        mode: 'range',
        dateFormat: 'Y-m-d',
        onClose: function (selectedDates) {
            if (selectedDates.length === 2) {
                setRange(selectedDates[0], selectedDates[1]);
            }
        }
    });

    document.querySelectorAll('[data-range-shortcut]').forEach(button => {
        button.addEventListener('click', () => {
            const range = button.dataset.rangeShortcut;
            if (range === 'custom') {
                picker.clear();
                filterDateFrom.value = '';
                filterDateTo.value = '';
                dateRangeInput.focus();
                return;
            }
            const { start, end } = computeRange(range);
            setRange(start, end);
            picker.setDate([start, end], true);
        });
    });

    const locale = '{{ app()->getLocale() }}';
    const languageOverrides = locale === 'ar'
        ? {
            emptyTable: '{{ __('dashboard.no_results') }}',
            info: 'عرض _START_ إلى _END_ من أصل _TOTAL_ سجل',
            infoEmpty: '{{ __('dashboard.no_results') }}',
            lengthMenu: '_MENU_',
            paginate: {
                first: 'الأول',
                last: 'الأخير',
                next: 'التالي',
                previous: 'السابق'
            }
        }
        : {
            emptyTable: '{{ __('dashboard.no_results') }}',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: '{{ __('dashboard.no_results') }}',
            lengthMenu: '_MENU_'
        };

    const table = $('#audit-log-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: '{{ route('dashboard.audit-logs.data') }}',
            data: function (d) {
                d.search = $('#audit-log-search').val();
                d.actions = $('#filter_actions').val();
                d.contexts = $('#filter_contexts').val();
                d.user_id = $('#filter_user_id').val();
                d.date_from = filterDateFrom.value;
                d.date_to = filterDateTo.value;
            },
        },
        columns: [
            { data: 'user', name: 'user', orderable: false, searchable: false },
            { data: 'action', name: 'action' },
            { data: 'context', name: 'context' },
            { data: 'ip_address', name: 'ip_address' },
            { data: 'device', name: 'device' },
            { data: 'date', name: 'occurred_at' },
        ],
        columnDefs: [
            { targets: '_all', className: 'text-start align-middle', defaultContent: '—' },
        ],
        order: [[5, 'desc']],
        language: languageOverrides
    });

    $('#audit-log-search').on('keyup', function () {
        table.ajax.reload();
    });

    document.querySelector('[data-apply-filters]').addEventListener('click', function () {
        table.ajax.reload();
        if (offcanvas) offcanvas.hide();
    });

    document.querySelector('[data-reset-filters]').addEventListener('click', function () {
        $('#filter_actions').val(null).trigger('change');
        $('#filter_contexts').val(null).trigger('change');
        $('#filter_user_id').val(null).trigger('change');
        filterDateFrom.value = '';
        filterDateTo.value = '';
        dateRangeInput.value = '';
        picker.clear();
        table.ajax.reload();
        if (offcanvas) offcanvas.hide();
    });

    function setRange(startDate, endDate) {
        filterDateFrom.value = formatDate(startDate);
        filterDateTo.value = formatDate(endDate);
        dateRangeInput.value = `${filterDateFrom.value} — ${filterDateTo.value}`;
    }

    function formatDate(date) {
        const d = new Date(date);
        d.setHours(0, 0, 0, 0);
        return d.toISOString().slice(0, 10);
    }

    function computeRange(rangeKey) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        let start = new Date(today);
        let end = new Date(today);

        const startOfWeek = (date) => {
            const day = date.getDay();
            const diff = (day + 6) % 7;
            const start = new Date(date);
            start.setDate(date.getDate() - diff);
            return start;
        };

        switch (rangeKey) {
            case 'today':
                break;
            case 'yesterday':
                start.setDate(start.getDate() - 1);
                end = new Date(start);
                break;
            case 'this_week':
                start = startOfWeek(today);
                end = new Date(start);
                end.setDate(start.getDate() + 6);
                break;
            case 'last_week':
                end = startOfWeek(today);
                end.setDate(end.getDate() - 1);
                start = new Date(end);
                start.setDate(end.getDate() - 6);
                break;
            case 'this_month':
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'last_month':
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                end = new Date(today.getFullYear(), today.getMonth(), 0);
                break;
        }

        return { start, end };
    }
});
</script>
@endpush
