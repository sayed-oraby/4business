@extends('layouts.dashboard.master')

@section('title', __('dashboard.important_notifications.title'))
@section('page-title', __('dashboard.important_notifications.title'))

@push('styles')
<style>
    #important-notifications-table tbody tr:hover { background-color: #f8fafc; }
</style>
@endpush

@section('content')
<div class="row g-5 g-xl-10">
    <div class="col-12">
        <div class="card card-flush shadow-sm">
            <div class="card-header align-items-center flex-wrap gap-3 py-5">
                <div class="d-flex flex-column">
                    <h3 class="card-title fw-bold mb-1">{{ __('dashboard.important_notifications.title') }}</h3>
                    <span class="text-muted fs-7">{{ __('dashboard.important_notifications.subtitle') }}</span>
                </div>
                <div class="card-toolbar ms-auto d-flex flex-wrap gap-3">
                    <div class="d-flex align-items-center position-relative">
                        <i class="ki-duotone ki-magnifier fs-2 position-absolute {{ app()->getLocale() === 'ar' ? 'me-5' : 'ms-5' }}">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text"
                               id="important-notifications-search"
                               class="form-control form-control-solid w-250px {{ app()->getLocale() === 'ar' ? 'pe-13' : 'ps-13' }}"
                               placeholder="{{ __('dashboard.important_notifications.search') }}" />
                    </div>
                    <select id="important-notifications-level" class="form-select form-select-solid w-200px">
                        <option value="">{{ __('dashboard.important_notifications.level_filter') }}</option>
                        <option value="warning">{{ __('dashboard.notification_levels.warning') }}</option>
                        <option value="danger">{{ __('dashboard.notification_levels.danger') }}</option>
                        <option value="error">{{ __('dashboard.notification_levels.error') }}</option>
                    </select>
                </div>
            </div>
            <div class="card-body py-5 px-5">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0" id="important-notifications-table">
                        <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>{{ __('dashboard.table.title') }}</th>
                            <th>{{ __('dashboard.table.message') }}</th>
                            <th>{{ __('dashboard.table.level') }}</th>
                            <th>{{ __('dashboard.table.user') }}</th>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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

    const importantTable = $('#important-notifications-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: '{{ route('dashboard.notifications.important.data') }}',
            data: function (d) {
                d.search = $('#important-notifications-search').val();
                d.level = $('#important-notifications-level').val();
            },
        },
        columns: [
            { data: 'title', name: 'title' },
            { data: 'message', name: 'message' },
            { data: 'level_badge', name: 'level', orderable: false, searchable: false },
            { data: 'user_details', name: 'user', orderable: false, searchable: false },
            { data: 'date', name: 'created_at' },
        ],
        columnDefs: [
            { targets: '_all', className: 'text-start align-middle', defaultContent: '—' },
            { targets: 2, render: function (data) { return data; } },
            { targets: 3, render: function (data) { return data; } },
        ],
        order: [[4, 'desc']],
        language: languageOverrides,
    });

    $('#important-notifications-search').on('keyup', function () {
        importantTable.ajax.reload();
    });

    $('#important-notifications-level').on('change', function () {
        importantTable.ajax.reload();
    });
});
</script>
@endpush
