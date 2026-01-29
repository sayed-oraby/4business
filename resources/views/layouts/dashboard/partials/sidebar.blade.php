<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">

    <!-- Logo -->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        @php
            $sidebarLogo = setting_media_url($appSettings['logo_white'] ?? $appSettings['logo'] ?? null, asset('metronic/media/logos/default.svg'));
            $sidebarCompact = setting_media_url($appSettings['logo_mobile'] ?? $appSettings['logo'] ?? null, asset('metronic/media/logos/default-small.svg'));
        @endphp
        <a href="{{ route('dashboard.home') }}">
            <img alt="Logo" src="{{ $sidebarLogo }}" class="h-25px app-sidebar-logo-default" />
            <img alt="Logo" src="{{ $sidebarCompact }}" class="h-20px app-sidebar-logo-minimize" />
        </a>

        <div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
            <i class="ki-duotone ki-black-left-line fs-3 rotate-180">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
    </div>

    <!-- Menu wrapper -->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
            <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">

                <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">

                @php
                    $menuAvailability = $menuAvailability ?? [];
                    $menuAvailability['categories'] = $menuAvailability['categories'] ?? \Illuminate\Support\Facades\Route::has('dashboard.categories.index');
                    $menuAvailability['users'] = $menuAvailability['users'] ?? \Illuminate\Support\Facades\Route::has('dashboard.users.index');
                    $menuAvailability['roles'] = $menuAvailability['roles'] ?? \Illuminate\Support\Facades\Route::has('dashboard.authorization.roles.index');
                    $menuAvailability['settings'] = $menuAvailability['settings'] ?? \Illuminate\Support\Facades\Route::has('dashboard.settings.index');
                    $menuAvailability['logs'] = $menuAvailability['logs'] ?? \Illuminate\Support\Facades\Route::has('dashboard.audit-logs.index');
                    $menuAvailability['banners'] = $menuAvailability['banners'] ?? \Illuminate\Support\Facades\Route::has('dashboard.banners.index');
                    $menuAvailability['pages'] = $menuAvailability['pages'] ?? \Illuminate\Support\Facades\Route::has('dashboard.pages.index');
                    $menuAvailability['blogs'] = $menuAvailability['blogs'] ?? \Illuminate\Support\Facades\Route::has('dashboard.blogs.index');
                    $menuAvailability['brands'] = $menuAvailability['brands'] ?? \Illuminate\Support\Facades\Route::has('dashboard.brands.index');
                    $menuAvailability['products'] = $menuAvailability['products'] ?? \Illuminate\Support\Facades\Route::has('dashboard.products.index');
                    // $menuAvailability['shipping'] = $menuAvailability['shipping'] ?? \Illuminate\Support\Facades\Route::has('dashboard.shipping.countries.index');
                    $menuAvailability['orders'] = $menuAvailability['orders'] ?? \Illuminate\Support\Facades\Route::has('dashboard.orders.index');
                    $menuAvailability['order_statuses'] = $menuAvailability['order_statuses'] ?? \Illuminate\Support\Facades\Route::has('dashboard.order-statuses.index');
                    $menuAvailability['reports'] = $menuAvailability['reports'] ?? \Illuminate\Support\Facades\Route::has('dashboard.reports.index');
                    $menuAvailability['posts'] = $menuAvailability['posts'] ?? \Illuminate\Support\Facades\Route::has('dashboard.posts.index');
                @endphp

                <!-- Dashboard -->
                <div class="menu-item">
                    <a class="menu-link {{ is_active_route('dashboard.home') }}" href="{{ route('dashboard.home') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-element-11 fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </span>
                        <span class="menu-title">{{ __('dashboard.dashboard') }}</span>
                    </a>
                </div>

                @if($menuAvailability['categories'] ?? false)
                    <!-- Catalog Section -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">{{ __('dashboard.catalog') }}</span>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.categories.*') }}" href="{{ route('dashboard.categories.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-category fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('dashboard.categories') }}</span>
                        </a>
                    </div>

                    {{-- Products hidden as requested
                    @if($menuAvailability['products'] ?? false)
                        <div class="menu-item">
                            <a class="menu-link {{ is_active_route('dashboard.products.*') }}" href="{{ route('dashboard.products.index') }}">
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-basket fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="menu-title">{{ __('product::product.title') }}</span>
                            </a>
                        </div>
                    @endif
                    --}}

                @endif

@if($menuAvailability['posts'] ?? false)
    <!-- Post Management Section -->
    <div class="menu-item pt-5">
        <div class="menu-content">
            <span class="menu-heading fw-bold text-uppercase fs-7">{{ __('post::post.title') }}</span>
        </div>
    </div>

    <!-- Posts -->
    <div class="menu-item">
        <a class="menu-link {{ is_active_route('dashboard.posts.*') }}" href="{{ route('dashboard.posts.index') }}">
            <span class="menu-icon">
                <i class="ki-duotone ki-briefcase fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </span>
            <span class="menu-title">{{ __('post::post.posts.title') }}</span>
        </a>
    </div>

    <!-- Packages -->
    <div class="menu-item">
        <a class="menu-link {{ is_active_route('dashboard.packages.*') }}" href="{{ route('dashboard.packages.index') }}">
            <span class="menu-icon">
                <i class="ki-duotone ki-price-tag fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
            </span>
            <span class="menu-title">{{ __('post::post.packages.title') }}</span>
        </a>
    </div>

    <!-- Post Types -->
    <div class="menu-item">
        <a class="menu-link {{ is_active_route('dashboard.post-types.*') }}" href="{{ route('dashboard.post-types.index') }}">
            <span class="menu-icon">
                <i class="ki-duotone ki-category fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                </i>
            </span>
            <span class="menu-title">{{ __('post::post.types.title') }}</span>
        </a>
    </div>
@endif

                @if($menuAvailability['users'] ?? false)
                    <!-- Users Section -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">{{ __('dashboard.users') }}</span>
                        </div>
                    </div>

                    <!-- Users -->
                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.users.index') }}" href="{{ route('dashboard.users.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-people fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('user::users.title') }}</span>
                        </a>
                    </div>
                
                    <!-- Office Requests -->
                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.users.office-requests.*') }}" href="{{ route('dashboard.users.office-requests.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-home fs-2"></i>
                            </span>
                            <span class="menu-title">طلبات المكاتب العقارية</span>
                        </a>
                    </div>
                @endif

                @if($menuAvailability['shipping'] ?? false)
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">{{ __('dashboard.shipping') }}</span>
                        </div>
                    </div>

                    {{-- <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.shipping.countries.*') }}" href="{{ route('dashboard.shipping.countries.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-truck fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('dashboard.shipping_countries') }}</span>
                        </a>
                    </div> --}}

                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.shipping.locations.*') }}" href="{{ route('dashboard.shipping.locations.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-geolocation fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('dashboard.shipping_locations') }}</span>
                        </a>
                        </div>
                    @endif

                    {{-- Orders section hidden as requested
                    @if($menuAvailability['orders'] ?? false)
                        <div class="menu-item pt-5">
                            <div class="menu-content">
                                <span class="menu-heading fw-bold text-uppercase fs-7">{{ __('order::dashboard.orders.title') }}</span>
                            </div>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ is_active_route('dashboard.orders.*') }}" href="{{ route('dashboard.orders.index') }}">
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-receipt fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="menu-title">{{ __('order::dashboard.orders.title') }}</span>
                            </a>
                        </div>
                        @if($menuAvailability['order_statuses'] ?? false)
                            <div class="menu-item">
                                <a class="menu-link {{ is_active_route('dashboard.order-statuses.*') }}" href="{{ route('dashboard.order-statuses.index') }}">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-setting-2 fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">{{ __('order::dashboard.statuses.title') }}</span>
                                </a>
                            </div>
                        @endif
                    @endif
                    --}}

                @if($menuAvailability['banners'] ?? false)
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">{{ __('banner::banner.title') }}</span>
                        </div>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.banners.*') }}" href="{{ route('dashboard.banners.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-picture fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('banner::banner.title') }}</span>
                        </a>
                    </div>
                @endif

                @if($menuAvailability['pages'] ?? false)
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">{{ __('page::page.title') }}</span>
                        </div>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.pages.*') }}" href="{{ route('dashboard.pages.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-notepad fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('page::page.title') }}</span>
                        </a>
                    </div>
                @endif

                {{-- Brands section hidden as requested
                @if($menuAvailability['brands'] ?? false)
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">{{ __('brand::brand.title') }}</span>
                        </div>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.brands.*') }}" href="{{ route('dashboard.brands.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-abstract-26 fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('brand::brand.title') }}</span>
                        </a>
                    </div>
                @endif
                --}}

                {{-- Blogs section hidden as requested
                @if($menuAvailability['blogs'] ?? false)
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">{{ __('blog::blog.title') }}</span>
                        </div>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.blogs.*') }}" href="{{ route('dashboard.blogs.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-note fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('blog::blog.title') }}</span>
                        </a>
                    </div>
                @endif
                --}}

                @if(($menuAvailability['roles'] ?? false) || ($menuAvailability['settings'] ?? false))
                    <!-- System Section -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">{{ __('dashboard.system') }}</span>
                        </div>
                    </div>
                @endif

                @if($menuAvailability['logs'] ?? false)
                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.audit-logs.*') }}" href="{{ route('dashboard.audit-logs.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-notepad-edit fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('dashboard.logs') }}</span>
                        </a>
                    </div>
                @endif

                @if($menuAvailability['roles'])
                    <!-- Roles & Permissions -->
                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.authorization.*') }}" href="{{ route('dashboard.authorization.roles.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-shield-tick fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('dashboard.roles_permissions') }}</span>
                        </a>
                    </div>
                @endif

                @if($menuAvailability['settings'])
                    <!-- Settings -->
                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.settings.*') }}" href="{{ route('dashboard.settings.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-setting-2 fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('dashboard.settings') }}</span>
                        </a>
                    </div>
                @endif

                @if($menuAvailability['reports'] ?? true)
                    <!-- Reports Section -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">{{ __('reports::reports.title') }}</span>
                        </div>
                    </div>

                    <!-- Reports Dashboard -->
                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.reports.index') }}" href="{{ route('dashboard.reports.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-chart-simple fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('reports::reports.title') }}</span>
                        </a>
                    </div>

                    <!-- Posts Analytics -->
                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.reports.posts') }}" href="{{ route('dashboard.reports.posts') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-briefcase fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('reports::reports.reports.posts.title') }}</span>
                        </a>
                    </div>

                    <!-- Job Offers Analytics -->
                    {{-- <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.reports.job-offers') }}" href="{{ route('dashboard.reports.job-offers') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-profile-user fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('reports::reports.reports.job_offers.title') }}</span>
                        </a>
                    </div> --}}

                    <!-- Members Analytics -->
                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.reports.members') }}" href="{{ route('dashboard.reports.members') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-people fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('reports::reports.reports.members.title') }}</span>
                        </a>
                    </div>

                    <!-- Financial Analytics -->
                    <div class="menu-item">
                        <a class="menu-link {{ is_active_route('dashboard.reports.revenue') }}" href="{{ route('dashboard.reports.revenue') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-chart-line-up fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('reports::reports.reports.financial.title') }}</span>
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
</div>
