<div id="kt_app_header" class="app-header">
    <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">

        <!-- Mobile toggle -->
        <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
            <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                <i class="ki-duotone ki-abstract-14 fs-2 fs-md-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
        </div>

        <!-- Mobile logo -->
        @php($mobileLogo = setting_media_url($appSettings['logo_mobile'] ?? $appSettings['logo'] ?? null, asset('metronic/media/logos/default-small.svg')))
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
            <a href="{{ route('dashboard.home') }}" class="d-lg-none">
                <img alt="Logo" src="{{ $mobileLogo }}" class="h-30px" />
            </a>
        </div>

        <!-- Header wrapper -->
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">

            <!-- Menu wrapper -->
            <div class="app-header-menu app-header-mobile-drawer align-items-stretch">
                <div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0">
                    <!-- Header menu will go here -->
                </div>
            </div>

            <!-- Navbar -->
            <div class="app-navbar flex-shrink-0">

                <!--begin::Theme mode toggle-->
                <div class="app-navbar-item ms-1 ms-lg-3">
                    <a href="#" class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px w-md-40px h-md-40px" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <i class="ki-duotone ki-night-day theme-light-show fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                            <span class="path6"></span>
                            <span class="path7"></span>
                            <span class="path8"></span>
                            <span class="path9"></span>
                            <span class="path10"></span>
                        </i>
                        <i class="ki-duotone ki-moon theme-dark-show fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-175px" data-kt-menu="true" data-kt-element="theme-mode-menu">
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3" data-kt-element="mode" data-kt-value="light">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-night-day fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                        <span class="path7"></span>
                                        <span class="path8"></span>
                                        <span class="path9"></span>
                                        <span class="path10"></span>
                                    </i>
                                </span>
                                <span class="menu-title">{{ __('dashboard.light_mode') }}</span>
                            </a>
                        </div>
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3" data-kt-element="mode" data-kt-value="dark">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-moon fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="menu-title">{{ __('dashboard.dark_mode') }}</span>
                            </a>
                        </div>
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3" data-kt-element="mode" data-kt-value="system">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-screen fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                                <span class="menu-title">{{ __('dashboard.system_default') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
                <!--end::Theme mode toggle-->

                @php($localeOptions = available_locales())
                @if(count($localeOptions) > 1)
                    <!--begin::Language selector-->
                    @php($currentEmoji = data_get($localeOptions, app()->getLocale().'.emoji', '🌐'))
                    <div class="app-navbar-item ms-1 ms-lg-3">
                        <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px w-md-40px h-md-40px" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                            <span class="fs-2">{{ $currentEmoji }}</span>
                        </div>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-200px" data-kt-menu="true">
                            @foreach($localeOptions as $code => $meta)
                                <div class="menu-item px-3">
                                    <a href="{{ route('language.switch', $code) }}" class="menu-link d-flex px-5 {{ app()->getLocale() === $code ? 'active' : '' }}">
                                        <span class="symbol symbol-20px me-4">
                                            <span class="symbol-label">{{ $meta['emoji'] ?? '🌐' }}</span>
                                        </span>
                                        <span>{{ $meta['native'] ?? $meta['name'] ?? strtoupper($code) }}</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!--end::Language selector-->
                @endif

                <!--begin::Notifications-->
                <div class="app-navbar-item ms-1 ms-lg-3" data-dashboard-notifications="root">
                    <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px w-md-40px h-md-40px position-relative"
                         data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                         data-kt-menu-attach="parent"
                         data-kt-menu-placement="bottom-end"
                         data-dashboard-notifications="toggle">
                        <i class="ki-duotone ki-notification-status fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                        <span class="position-absolute top-0 start-100 translate-middle badge badge-circle badge-danger w-18px h-18px ms-n4 mt-3 d-none"
                              data-notification-badge>0</span>
                    </div>
                    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true" id="dashboardNotificationsMenu">
                        <div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-image:url('{{ asset('metronic/media/misc/menu-header-bg.jpg') }}')">
                            <h3 class="text-white fw-semibold px-9 mt-10 mb-6">
                                {{ __('dashboard.notifications') }}
                                <span class="fs-8 opacity-75 ps-3" data-notification-subtitle>{{ __('dashboard.notifications_overview', ['count' => 0]) }}</span>
                            </h3>
                            <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-semibold px-9">
                                <li class="nav-item">
                                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active"
                                       data-bs-toggle="tab"
                                       href="#dashboard_notifications_primary">{{ __('dashboard.notifications_tab') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4"
                                       data-bs-toggle="tab"
                                       href="#dashboard_notifications_logs">{{ __('dashboard.logs') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="dashboard_notifications_primary">
                                <div class="scroll-y mh-325px my-5 px-8 notification-list" data-notification-list="notifications">
                                    <div class="text-gray-600 text-center py-10 fw-semibold" data-notification-empty="notifications">
                                        {{ __('dashboard.notifications_loading') }}
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="dashboard_notifications_logs">
                                <div class="scroll-y mh-325px my-5 px-8 notification-list" data-notification-list="logs">
                                    <div class="text-gray-600 text-center py-10 fw-semibold" data-notification-empty="logs">
                                        {{ __('dashboard.notifications_loading') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="py-3 px-8 border-top border-gray-200 d-flex flex-wrap gap-3 justify-content-between align-items-center">
                            <button type="button" class="btn btn-sm btn-light flex-grow-1" data-notification-mark-all>
                                {{ __('dashboard.mark_all_as_read') }}
                            </button>
                            <div class="d-flex flex-column gap-2 flex-grow-1">
                                <a href="{{ route('dashboard.notifications.important.index') }}" class="btn btn-sm btn-primary w-100">
                                    {{ __('dashboard.important_notifications.title') }}
                                </a>
                                <a href="{{ route('dashboard.audit-logs.index') }}" class="btn btn-sm btn-light-primary w-100">
                                    {{ __('dashboard.logs_title') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Notifications-->

                <!-- User menu -->
                <div class="app-navbar-item ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                    <div class="cursor-pointer symbol symbol-35px symbol-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <img src="{{ asset('metronic/media/avatars/300-1.jpg') }}" alt="user" />
                    </div>

                    <!-- User menu dropdown -->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">

                        <!-- User info -->
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <div class="symbol symbol-50px me-5">
                                    <img alt="Logo" src="{{ asset('metronic/media/avatars/300-1.jpg') }}" />
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bolder d-flex align-items-center fs-5">
                                        {{ auth('admin')->user()->name }}
                                    </div>
                                    <a href="#" class="fw-bold text-muted text-hover-primary fs-7">
                                        {{ auth('admin')->user()->email }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="separator my-2"></div>

                        <!-- My Profile -->
                        <div class="menu-item px-5">
                            <a href="#" class="menu-link px-5">{{ __('dashboard.my_profile') }}</a>
                        </div>

                        <!-- Sign Out -->
                        <div class="menu-item px-5">
                            <form action="{{ route('dashboard.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="menu-link px-5 w-100 text-start btn btn-link text-decoration-none">
                                    {{ __('dashboard.sign_out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
