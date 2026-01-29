@extends('layouts.dashboard.master')

@section('title', __('user::users.title'))
@section('page-title', __('user::users.title'))

@push('styles')
    <style>
        .users-hero {
            background: linear-gradient(135deg, #eff4ff 0%, #ffffff 100%);
            border-radius: 1.5rem;
        }

        .users-hero__stats {
            min-width: 180px;
        }

        .users-table-card .table thead tr {
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: .04em;
        }

        .users-table-card .table thead th,
        .users-table-card .table tbody td {
            text-align: center !important;
            vertical-align: middle;
        }

        .users-table-card .table thead th:first-child,
        .users-table-card .table thead th:last-child,
        .users-table-card .table tbody td:first-child,
        .users-table-card .table tbody td:last-child {
            text-align: center !important;
        }

        .users-table-card .table thead th:first-child,
        .users-table-card .table tbody td:first-child {
            width: 60px;
        }

        .users-table-card .users-table-avatar {
            width: 44px;
            height: 44px;
            border-radius: 999px;
        }

        .user-form-aside .image-input-wrapper {
            width: 150px;
            height: 150px;
        }

        .user-form-aside .badge-input-hint {
            font-size: 12px;
        }
    </style>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-10 users-hero">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-7">
                <div>
                    <span class="badge badge-light-primary mb-3">{{ __('dashboard.system') }}</span>
                    <h2 class="fw-bold text-gray-900 mb-2">{{ __('user::users.title') }}</h2>
                    <p class="text-gray-600 mb-0">{{ __('user::users.description') }}</p>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-6 mt-8">
                @php
                    $statBlocks = [
                        'normal' => ['class' => 'text-primary', 'icon' => 'ki-user'],
                        'admins' => ['class' => 'text-success', 'icon' => 'ki-lock'],
                        'deleted' => ['class' => 'text-danger', 'icon' => 'ki-trash'],
                    ];
                @endphp
                @foreach($statBlocks as $key => $meta)
                    <div class="users-hero__stats card border-0 shadow-sm flex-grow-1">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <span class="text-muted fw-semibold">{{ __('user::users.stats.'.$key.'.title') }}</span>
                                <span class="symbol symbol-35px symbol-circle bg-light">
                                    <i class="ki-duotone {{ $meta['icon'] }} fs-2 {{ $meta['class'] }}">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div class="fs-1 fw-bolder text-gray-900">{{ $stats[$key] ?? 0 }}</div>
                            <span class="text-muted fw-semibold">{{ __('user::users.stats.'.$key.'.subtitle') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card card-flush shadow-sm mb-10 users-table-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">{{ __('user::users.title') }}</h3>
            </div>
            <div class="card-toolbar gap-3">
                <div class="position-relative my-1">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12" id="user-search" placeholder="{{ __('user::users.filters.search') }}">
                </div>
                <button class="btn btn-light btn-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#userFiltersCanvas">
                    <i class="ki-duotone ki-filter fs-2"></i>{{ __('dashboard.filter_button') }}
                </button>
                @can('users.create')
                    <button class="btn btn-primary btn-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#userModal">
                        <i class="ki-duotone ki-plus fs-2"></i>{{ __('user::users.create') }}
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4" id="users-table">
                    <thead class="bg-transparent text-gray-500 fw-semibold">
                        <tr class="text-center align-middle">
                            <th class="w-60px text-center">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="select-all-users">
                                </div>
                            </th>
                            <th class="min-w-250px text-center">{{ __('user::users.table.name') }}</th>
                            <th class="min-w-150px text-center">{{ __('user::users.table.mobile') }}</th>
                            <th class="min-w-180px text-center">{{ __('user::users.table.role') }}</th>
                            <th class="min-w-120px text-center">{{ __('user::users.table.status') }}</th>
                            <th class="text-center pe-4 min-w-150px">{{ __('user::users.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        @can('users.delete')
            <div class="card-footer d-flex justify-content-between flex-wrap gap-3">
                <div class="text-muted">{{ __('user::users.actions.bulk_delete') }}</div>
                <button class="btn btn-light-danger" id="bulk-delete-btn">
                    <i class="ki-duotone ki-trash fs-2 me-2"></i>{{ __('user::users.actions.bulk_delete') }}
                </button>
            </div>
        @endcan
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="userFiltersCanvas">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-bold">{{ __('dashboard.filters_panel.title') }}</h5>
            <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="offcanvas">
                <i class="ki-duotone ki-cross fs-2"></i>
            </button>
        </div>
        <div class="offcanvas-body pt-5">
            <form id="user-filter-form" class="d-flex flex-column gap-5">
                <div>
                    <label class="form-label">{{ __('user::users.filters.role') }}</label>
                    <select class="form-select form-select-solid" name="role">
                        <option value="">{{ __('user::users.filters.role') }}</option>
                        <option value="__without_roles">{{ __('user::users.filters.role_without') }}</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('user::users.filters.status') }}</label>
                    <select class="form-select form-select-solid" name="status">
                        <option value="">{{ __('user::users.filters.status') }}</option>
                        <option value="active">{{ __('user::users.filters.status_active') }}</option>
                        <option value="deleted">{{ __('user::users.filters.status_deleted') }}</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('dashboard.filters_panel.date_range') }} ({{ __('From') }})</label>
                        <input type="date" class="form-control form-control-solid" name="date_from">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('dashboard.filters_panel.date_range') }} ({{ __('To') }})</label>
                        <input type="date" class="form-control form-control-solid" name="date_to">
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <button class="btn btn-light flex-grow-1" type="button" data-filter-reset>
                        <i class="ki-duotone ki-arrows-circle fs-2 me-2"></i>{{ __('dashboard.filters_panel.reset') }}
                    </button>
                    <button class="btn btn-primary flex-grow-1" type="button" data-filter-apply>
                        <i class="ki-duotone ki-check fs-2 me-2"></i>{{ __('dashboard.filters_panel.apply') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @canany(['users.create', 'users.update'])
        <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h2 class="fw-bold mb-1" data-modal-title>{{ __('user::users.create') }}</h2>
                            <span class="text-muted fs-7">{{ __('user::users.description') }}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-2"></i>
                        </button>
                    </div>
                    <form id="user-form" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" id="user_id">
                        <div class="modal-body pt-0">
                            <div class="row g-7">
                                <div class="col-lg-4">
                                    <div class="card shadow-sm border-0 user-form-aside h-100">
                                        <div class="card-body text-center">
                                            <div class="image-input image-input-outline mb-5" data-kt-image-input="true">
                                                <div class="image-input-wrapper bg-light" id="user-avatar-preview"></div>
                                                <label class="btn btn-icon btn-circle btn-active-color-primary w-35px h-35px bg-body shadow" data-kt-image-input-action="change">
                                                    <i class="ki-duotone ki-pencil fs-4">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg">
                                                </label>
                                                <span class="btn btn-icon btn-circle btn-active-color-primary w-35px h-35px bg-body shadow" data-kt-image-input-action="remove">
                                                    <i class="ki-duotone ki-cross fs-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                    <input type="hidden" name="remove_avatar" value="0">
                                                </span>
                                            </div>
                                            <div class="badge badge-light badge-input-hint">{{ __('user::users.form.avatar') }}</div>
                                            <div class="invalid-feedback d-block" data-error-for="avatar"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body p-9">
                                            <div class="row g-6">
                                                <div class="col-md-6">
                                                    <label class="form-label required">{{ __('user::users.form.name') }}</label>
                                                    <input type="text" class="form-control form-control-solid" name="name" placeholder="Jane Doe">
                                                    <div class="invalid-feedback" data-error-for="name"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label required">{{ __('user::users.form.email') }}</label>
                                                    <input type="email" class="form-control form-control-solid" name="email" placeholder="user@gavankit.test">
                                                    <div class="invalid-feedback" data-error-for="email"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('user::users.form.mobile') }}</label>
                                                    <input type="text" class="form-control form-control-solid" name="mobile" placeholder="+965 500 12345">
                                                    <div class="invalid-feedback" data-error-for="mobile"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('user::users.form.birthdate') }}</label>
                                                    <input type="date" class="form-control form-control-solid" name="birthdate">
                                                    <div class="invalid-feedback" data-error-for="birthdate"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('user::users.form.gender') }}</label>
                                                    <select class="form-select form-select-solid" name="gender">
                                                        <option value="">{{ __('user::users.form.gender') }}</option>
                                                        <option value="male">{{ __('user::users.form.gender_male') }}</option>
                                                        <option value="female">{{ __('user::users.form.gender_female') }}</option>
                                                        <option value="other">{{ __('user::users.form.gender_other') }}</option>
                                                    </select>
                                                    <div class="invalid-feedback" data-error-for="gender"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('user::users.form.roles') }}</label>
                                                    <select class="form-select form-select-solid" name="roles[]" multiple id="user-role-select">
                                                        @foreach($roles as $role)
                                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback" data-error-for="roles"></div>
                                                </div>
                                                <div class="col-md-6 password-field">
                                                    <label class="form-label required" data-password-label>{{ __('user::users.form.password') }}</label>
                                                    <input type="password" class="form-control form-control-solid" name="password" placeholder="********">
                                                    <div class="invalid-feedback" data-error-for="password"></div>
                                                </div>
                                                <div class="col-md-6 password-field">
                                                    <label class="form-label">{{ __('user::users.form.password_confirmation') }}</label>
                                                    <input type="password" class="form-control form-control-solid" name="password_confirmation" placeholder="********">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('user::users.actions.cancel') }}</button>
                            <button type="submit" class="btn btn-primary" data-submit-btn data-kt-indicator="off">
                                <span class="indicator-label">{{ __('user::users.actions.save') }}</span>
                                <span class="indicator-progress">{{ __('user::users.actions.saving') }}
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcanany

    <div class="modal fade" id="userViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h2 class="modal-title">{{ __('user::users.view') }}</h2>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-2"></i>
                    </button>
                </div>
                <div class="modal-body" id="user-view-body"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.UserModule = {
            routes: {
                data: "{{ route('dashboard.users.data') }}",
                store: "{{ route('dashboard.users.store') }}",
                show: "{{ route('dashboard.users.show', ['user' => '__id__']) }}",
                update: "{{ route('dashboard.users.update', ['user' => '__id__']) }}",
                delete: "{{ route('dashboard.users.destroy', ['user' => '__id__']) }}",
                bulkDelete: "{{ route('dashboard.users.bulk-delete') }}",
                restore: "{{ route('dashboard.users.restore', ['user' => '__id__']) }}",
            },
            can: {
                update: @json(auth('admin')->user()?->can('users.update')),
                delete: @json(auth('admin')->user()?->can('users.delete')),
            },
            messages: {
                created: "{{ __('user::users.messages.created') }}",
                updated: "{{ __('user::users.messages.updated') }}",
                deleted: "{{ __('user::users.messages.deleted') }}",
                restored: "{{ __('user::users.messages.restored') }}",
                bulkDeleted: "{{ __('user::users.messages.bulk_deleted') }}",
            },
            labels: {
                edit: "{{ __('user::users.edit') }}",
                create: "{{ __('user::users.create') }}",
                password: "{{ __('user::users.form.password') }}",
                passwordEdit: "{{ __('user::users.form.password_edit') }}",
            },
            statuses: {
                active: "{{ __('user::users.filters.status_active') }}",
                deleted: "{{ __('user::users.filters.status_deleted') }}",
            },
            locale: "{{ app()->getLocale() }}",
            view: {
                title: "{{ __('user::users.view') }}",
                email: "{{ __('user::users.form.email') }}",
                mobile: "{{ __('user::users.form.mobile') }}",
                birthdate: "{{ __('user::users.form.birthdate') }}",
                gender: "{{ __('user::users.form.gender') }}",
                gender_male: "{{ __('user::users.form.gender_male') }}",
                gender_female: "{{ __('user::users.form.gender_female') }}",
                gender_other: "{{ __('user::users.form.gender_other') }}",
                roles: "{{ __('user::users.form.roles') }}",
                status: "{{ __('user::users.table.status') }}",
                section_identity: "{{ __('user::users.view_sections.identity') }}",
                section_contact: "{{ __('user::users.view_sections.contact') }}",
                section_meta: "{{ __('user::users.view_sections.meta') }}",
                section_roles: "{{ __('user::users.view_sections.roles') }}",
                created_at: "{{ __('user::users.view_fields.created_at') }}",
                updated_at: "{{ __('user::users.view_fields.updated_at') }}",
                status_hint: "{{ __('user::users.view_hints.status') }}",
                roles_hint: "{{ __('user::users.view_hints.roles') }}",
                empty: '—',
            },
            confirm: {
                deleteTitle: "{{ __('user::users.confirm.delete_title') }}",
                deleteMessage: "{{ __('user::users.confirm.delete_message') }}",
                bulkTitle: "{{ __('user::users.confirm.bulk_title') }}",
                bulkMessage: "{{ __('user::users.confirm.bulk_message') }}",
                confirm: "{{ __('user::users.confirm.confirm') }}",
                cancel: "{{ __('user::users.confirm.cancel') }}",
            },
            validation: {
                name: {
                    required: "{{ __('validation.required', ['attribute' => __('user::users.form.name')]) }}",
                },
                email: {
                    required: "{{ __('validation.required', ['attribute' => __('user::users.form.email')]) }}",
                    email: "{{ __('validation.email', ['attribute' => __('user::users.form.email')]) }}",
                    unique: "{{ __('validation.unique', ['attribute' => __('user::users.form.email')]) }}",
                },
                password: {
                    required: "{{ __('validation.required', ['attribute' => __('user::users.form.password')]) }}",
                    min: "{{ __('validation.min.string', ['attribute' => __('user::users.form.password'), 'min' => 8]) }}",
                    confirmed: "{{ __('validation.confirmed', ['attribute' => __('user::users.form.password')]) }}",
                },
                mobile: {
                    required: "{{ __('validation.required', ['attribute' => __('user::users.form.mobile')]) }}",
                },
                birthdate: {
                    required: "{{ __('validation.required', ['attribute' => __('user::users.form.birthdate')]) }}",
                },
                gender: {
                    required: "{{ __('validation.required', ['attribute' => __('user::users.form.gender')]) }}",
                },
                roles: {
                    required: "{{ __('validation.required', ['attribute' => __('user::users.form.roles')]) }}",
                },
                avatar: {
                    required: "{{ __('validation.required', ['attribute' => __('user::users.form.avatar')]) }}",
                },
                password_confirmation: {
                    same: "{{ __('validation.same', ['attribute' => __('user::users.form.password_confirmation'), 'other' => __('user::users.form.password')]) }}",
                },
            },
        };
    </script>
    @vite('Modules/User/resources/assets/js/users.js')
@endpush
