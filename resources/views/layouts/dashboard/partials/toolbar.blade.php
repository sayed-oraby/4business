<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">

        <!-- Page title -->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                @yield('page-title', 'Dashboard')
            </h1>

            @if(View::hasSection('breadcrumb'))
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.home') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    @yield('breadcrumb')
                </ul>
            @endif
        </div>

        <!-- Toolbar actions -->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @yield('toolbar-actions')
        </div>

    </div>
</div>

