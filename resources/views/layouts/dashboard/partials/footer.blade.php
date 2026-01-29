<div id="kt_app_footer" class="app-footer">
    <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
        @php($footerText = data_get($appSettings, 'branding.footer') ?? __('dashboard.footer_default', ['year' => date('Y'), 'app' => $appSettings['app_name'] ?? config('app.name')]))
        <div class="text-gray-900 order-2 order-md-1">
            <span class="text-muted fw-semibold me-1">{{ $footerText }}</span>
        </div>

        <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
            <li class="menu-item">
                <a href="#" target="_blank" class="menu-link px-2">{{ __('dashboard.footer_about') }}</a>
            </li>
            <li class="menu-item">
                <a href="#" target="_blank" class="menu-link px-2">{{ __('dashboard.footer_support') }}</a>
            </li>
        </ul>
    </div>
</div>
