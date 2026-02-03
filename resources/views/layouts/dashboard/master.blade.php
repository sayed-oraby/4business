<!DOCTYPE html>
{{--  <html @if(is_rtl() == 'rtl') direction="rtl" dir="rtl" style="direction: rtl" @else lang="en" @endif>  --}}
<html lang="{{ app()->getLocale() }}" @if(is_rtl() == 'rtl') direction="rtl" dir="rtl" style="direction: rtl" @endif>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $appSettings['app_description'] ?? __('dashboard.hero_description') }}">

    <title>@yield('title', 'Dashboard') - {{ $appSettings['app_name'] ?? config('app.name', 'Kit') }}</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    @php($favicon = setting_media_url($appSettings['favicon'] ?? null, asset('metronic/media/logos/favicon.ico')))
    <link rel="shortcut icon" href="{{ $favicon }}" />

    @if($customHeadCss = data_get($appSettings, 'custom_code.head_css'))
        <style>{!! $customHeadCss !!}</style>
    @endif

    @if($customHeadJs = data_get($appSettings, 'custom_code.head_js'))
        <script>{!! $customHeadJs !!}</script>
    @endif

    <!-- Fonts -->
    @if(is_rtl() == 'rtl')
        <link href="https://fonts.googleapis.com/css?family=Cairo" rel="stylesheet" type="text/css" />
    @else
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    @endif

    <!-- Vendor Stylesheets -->
    <link href="{{ asset('metronic/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" />

    <!-- Global Stylesheets Bundle -->
    @if(is_rtl() == 'rtl')
        <link href="{{ asset('metronic/plugins/global/plugins.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('metronic/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
    @else
        <link href="{{ asset('metronic/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('metronic/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    @endif

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />

    @if(is_rtl() == 'rtl')
        <style>
            .select2-container--bootstrap5 .select2-selection__clear {
                right: unset;
                left: 3rem !important;
            }
            body, .app-default, .app-sidebar, .app-header, .app-main, .app-footer {
                font-family: 'Cairo', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            }
        </style>
    @else
        <style>
            body, .app-default, .app-sidebar, .app-header, .app-main, .app-footer {
                font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            }
        </style>
    @endif

    <!-- Custom Styles -->
    <style>
        /* Center DataTables headers and content */
        .dataTable thead th {
            text-align: center !important;
            vertical-align: middle !important;
        }
        .dataTable tbody td {
            text-align: center !important;
            vertical-align: middle !important;
        }
        /* Keep first column (checkbox) and last column (actions) as they are */
        .dataTable thead th:first-child,
        .dataTable tbody td:first-child {
            text-align: {{ is_rtl() == 'rtl' ? 'right' : 'left' }} !important;
        }
        .dataTable thead th:last-child,
        .dataTable tbody td:last-child {
            text-align: {{ is_rtl() == 'rtl' ? 'left' : 'right' }} !important;
        }
    </style>

    @stack('styles')
</head>

<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true"
      data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true"
      data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true"
      data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true"
      data-kt-app-toolbar-enabled="true" class="app-default">

    <!-- Theme mode setup -->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>

    <!-- App Root -->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!-- Page -->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">

            <!-- Header -->
            @include('layouts.dashboard.partials.header')

            <!-- Wrapper -->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

                <!-- Sidebar -->
                @include('layouts.dashboard.partials.sidebar')

                <!-- Main -->
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <!-- Content wrapper -->
                    <div class="d-flex flex-column flex-column-fluid">

                        <!-- Toolbar -->
                        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                                <!-- Page title -->
                                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                                        @yield('page-title', 'Dashboard')
                                    </h1>
                                    <!-- Breadcrumb -->
                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('dashboard.home') }}" class="text-muted text-hover-primary">{{ __('dashboard.home') }}</a>
                                        </li>
                                        @yield('breadcrumb')
                                    </ul>
                                </div>

                                <!-- Actions -->
                                <div class="d-flex align-items-center gap-2 gap-lg-3">
                                    @yield('toolbar-actions')
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container container-xxl">
                                @include('layouts.dashboard.partials.alerts')
                                @yield('content')
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    @include('layouts.dashboard.partials.footer')
                </div>
            </div>
        </div>
    </div>

    <!-- Scrolltop -->
    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <i class="ki-duotone ki-arrow-up">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    </div>

    <!-- Javascript -->
    <script>var hostUrl = "{{ asset('metronic') }}/";</script>

    <!-- Global Javascript Bundle -->
    <script src="{{ asset('metronic/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('metronic/js/scripts.bundle.js') }}"></script>

    <!-- Vendor Javascript -->
    <script src="{{ asset('metronic/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.GavanKit = window.GavanKit || {};
        window.GavanKit.notifications = {
            feedUrl: "{{ route('dashboard.notifications.feed') }}",
            markAllUrl: "{{ route('dashboard.notifications.mark-all') }}",
            alertSoundUrl: "{{ asset('metronic/sounds/alert.mp3') }}",
            translations: {
                empty: "{{ __('dashboard.notifications_empty') }}",
                loading: "{{ __('dashboard.notifications_loading') }}",
                subtitle: "{{ __('dashboard.notifications_overview', ['count' => ':count']) }}",
                marked: "{{ __('dashboard.notifications_marked') }}",
                auditToast: "{{ __('dashboard.toasts.audit', ['user' => ':user', 'action' => ':action']) }}",
                defaultToast: "{{ __('dashboard.toasts.default', ['title' => ':title']) }}",
                justNow: "{{ __('dashboard.just_now') }}",
                performedBy: "{{ __('dashboard.notifications_performed_by', ['user' => ':user']) }}",
            },
            locale: "{{ app()->getLocale() }}",
        };
    </script>

    @vite('resources/js/app.js')

    <!-- Custom Javascript -->
    <script>
        // Set default AJAX headers
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Success notification
        function showSuccess(message) {
            Swal.fire({
                text: message,
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: "Ok",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        }

        // Error notification
        function showError(message) {
            Swal.fire({
                text: message,
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        }

        // Delete confirmation
        function confirmDelete(url, successCallback) {
            Swal.fire({
                title: '{{ __("dashboard.confirm_delete") }}',
                text: "{{ __('dashboard.confirm_delete') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __("dashboard.delete") }}',
                cancelButtonText: '{{ __("dashboard.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('{{ __("dashboard.deleted_successfully") }}', response.message || '{{ __("dashboard.deleted_successfully") }}', 'success');

                                // Refresh DataTable if exists
                                if (typeof $.fn.DataTable !== 'undefined') {
                                    $('.dataTable').each(function() {
                                        if ($.fn.DataTable.isDataTable(this)) {
                                            $(this).DataTable().ajax.reload(null, false);
                                        }
                                    });
                                }

                                // Only call successCallback if provided
                                if (successCallback) {
                                    successCallback();
                                }
                            } else {
                                Swal.fire('{{ __("dashboard.error") }}', response.message || '{{ __("dashboard.operation_failed") }}', 'error');
                            }
                        },
                        error: function(xhr) {
                            var message = '{{ __("dashboard.operation_failed") }}';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Swal.fire('{{ __("dashboard.error") }}', message, 'error');
                        }
                    });
                }
            });
        }

        // Initialize tooltips
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>

    @stack('scripts')

    @if($customBodyJs = data_get($appSettings, 'custom_code.body_js'))
        <script>{!! $customBodyJs !!}</script>
    @endif
</body>
</html>
