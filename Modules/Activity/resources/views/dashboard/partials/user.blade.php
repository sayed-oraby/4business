<div class="d-flex flex-column">
    <span class="fw-bold text-gray-900">{{ $log->user->name ?? __('dashboard.users') }}</span>
    <span class="text-muted fs-7">{{ $log->user->email ?? '—' }}</span>
</div>
