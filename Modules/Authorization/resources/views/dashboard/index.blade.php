@extends('layouts.dashboard.master')

@section('title', __('authorization::authorization.title'))
@section('page-title', __('authorization::authorization.title'))

@section('content')
    <div class="row g-5 g-xl-10">
        <div class="col-xl-6">
            <div class="card card-flush shadow-sm">
            <div class="card-header align-items-center">
                <div>
                    <h3 class="card-title">{{ __('authorization::authorization.roles.table_title') }}</h3>
                    <span class="text-muted fs-7">{{ __('authorization::authorization.description') }}</span>
                </div>
                @can('authorization.update')
                    <div class="card-toolbar">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roleModal">
                            {{ __('authorization::authorization.roles.create') }}
                        </button>
                    </div>
                @endcan
            </div>
                <div class="card-body pt-5">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="roles-table">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th>{{ __('authorization::authorization.roles.name') }}</th>
                                <th>{{ __('authorization::authorization.roles.guard') }}</th>
                                <th>{{ __('authorization::authorization.roles.users') }}</th>
                                <th class="text-end">{{ __('user::users.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card card-flush shadow-sm" id="role-permissions-card">
                <div class="card-header align-items-center">
                    <div>
                        <h3 class="card-title">{{ __('authorization::authorization.permissions.title') }}</h3>
                        <span class="text-muted fs-7">{{ __('authorization::authorization.description') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info" data-role-placeholder>
                        {{ __('authorization::authorization.permissions.title') }} - {{ __('authorization::authorization.roles.table_title') }}
                    </div>

                    @can('authorization.update')
                        <form id="role-permissions-form" class="d-none">
                            <input type="hidden" name="role_id" id="permissions_role_id">
                            <div class="mb-5">
                                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#permissionModal">
                                    {{ __('authorization::authorization.permissions.create') }}
                                </button>
                            </div>
                            <div class="permission-groups" data-permission-groups></div>
                            <div class="d-flex justify-content-end mt-5">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('authorization::authorization.actions.save') }}
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info mt-5">
                            {{ __('authorization::authorization.description') }}
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    @can('authorization.update')
    <!-- Role Modal -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" data-role-modal-title>{{ __('authorization::authorization.roles.create') }}</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <form id="role-form">
                    <div class="modal-body py-10 px-10">
                        <input type="hidden" name="role_id" id="role_id">
                        <div class="mb-5">
                            <label class="form-label required">{{ __('authorization::authorization.roles.name') }}</label>
                            <input type="text" class="form-control form-control-solid" name="name" id="role_name">
                            <div class="invalid-feedback" data-error-for="name"></div>
                        </div>
                    </div>
                    <div class="modal-footer flex-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('user::users.actions.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">{{ __('authorization::authorization.actions.save') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Permission Modal -->
    <div class="modal fade" id="permissionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fs-3">{{ __('authorization::authorization.permissions.create') }}</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <form id="permission-form">
                    <div class="modal-body">
                        <div class="mb-5">
                            <label class="form-label required">{{ __('authorization::authorization.permissions.name') }}</label>
                            <input type="text" class="form-control form-control-solid" name="name">
                            <div class="invalid-feedback" data-error-for="name"></div>
                            <span class="text-muted fs-7 d-block mt-2">module.action (e.g. users.view)</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('user::users.actions.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">{{ __('authorization::authorization.actions.save') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
@endsection

@push('scripts')
    <script>
        window.AuthorizationConfig = {
            urls: {
                data: "{{ route('dashboard.authorization.roles.data') }}",
                store: "{{ route('dashboard.authorization.roles.store') }}",
                update: "{{ route('dashboard.authorization.roles.update', ['role' => '__id__']) }}",
                delete: "{{ route('dashboard.authorization.roles.destroy', ['role' => '__id__']) }}",
                permissions: "{{ route('dashboard.authorization.roles.permissions', ['role' => '__id__']) }}",
                syncPermissions: "{{ route('dashboard.authorization.roles.permissions.sync', ['role' => '__id__']) }}",
                availablePermissions: "{{ route('dashboard.authorization.permissions.available') }}",
                createPermission: "{{ route('dashboard.authorization.permissions.store') }}",
            },
            messages: {
                roleCreated: "{{ __('authorization::authorization.messages.role_created') }}",
                roleUpdated: "{{ __('authorization::authorization.messages.role_updated') }}",
                roleDeleted: "{{ __('authorization::authorization.messages.role_deleted') }}",
                permissionsSynced: "{{ __('authorization::authorization.messages.permissions_synced') }}",
                permissionCreated: "{{ __('authorization::authorization.messages.permission_created') }}",
            },
            labels: {
                create: "{{ __('authorization::authorization.roles.create') }}",
                edit: "{{ __('authorization::authorization.roles.edit') }}",
            },
            canUpdate: @json(auth('admin')->user()?->can('authorization.update')),
            confirm: {
                title: "{{ __('authorization::authorization.confirm.delete_title') }}",
                message: "{{ __('authorization::authorization.confirm.delete_message') }}",
                confirm: "{{ __('authorization::authorization.confirm.confirm') }}",
                cancel: "{{ __('authorization::authorization.confirm.cancel') }}",
            },
        };
    </script>
    @vite('Modules/Authorization/resources/assets/js/authorization.js')
@endpush
