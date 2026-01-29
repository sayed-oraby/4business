{{-- Success Alert --}}
@if(session('success'))
    <div class="alert alert-success d-flex align-items-center p-5 mb-10">
        <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-success">Success</h4>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

{{-- Error Alert --}}
@if(session('error'))
    <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
        <i class="ki-duotone ki-information-5 fs-2hx text-danger me-4">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
        </i>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-danger">Error</h4>
            <span>{{ session('error') }}</span>
        </div>
    </div>
@endif

{{-- Warning Alert --}}
@if(session('warning'))
    <div class="alert alert-warning d-flex align-items-center p-5 mb-10">
        <i class="ki-duotone ki-information fs-2hx text-warning me-4">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
        </i>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-warning">Warning</h4>
            <span>{{ session('warning') }}</span>
        </div>
    </div>
@endif

{{-- Info Alert --}}
@if(session('info'))
    <div class="alert alert-info d-flex align-items-center p-5 mb-10">
        <i class="ki-duotone ki-information-4 fs-2hx text-info me-4">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
        </i>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-info">Info</h4>
            <span>{{ session('info') }}</span>
        </div>
    </div>
@endif

{{-- Validation Errors removed from here --}}
{{-- They are now displayed inside forms only --}}

