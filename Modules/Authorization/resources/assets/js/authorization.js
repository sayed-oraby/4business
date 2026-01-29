import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.AuthorizationConfig;
    const tableEl = $('#roles-table');

    if (!config || !tableEl.length) {
        return;
    }

    let availablePermissions = {};
    let selectedRoleId = null;

    const rolesTable = tableEl.DataTable({
        processing: true,
        serverSide: true,
        ajax: config.urls.data,
        columns: [
            { data: 'name' },
            { data: 'guard_name' },
            { data: 'users_count' },
            {
                data: null,
                orderable: false,
                className: 'text-end',
                render: (row) => actionButtons(row),
            },
        ],
    });

    function actionButtons(row) {
        const buttons = [
            `<button class="btn btn-sm btn-light select-role" data-id="${row.id}" data-name="${row.name}">
                <i class="ki-outline ki-setting-4"></i>
            </button>`,
        ];

        if (config.canUpdate) {
            buttons.push(`<button class="btn btn-sm btn-light-primary edit-role" data-id="${row.id}" data-name="${row.name}">
                <i class="ki-outline ki-pencil"></i>
            </button>`);

            if (row.name !== 'super-admin') {
                buttons.push(`<button class="btn btn-sm btn-light-danger delete-role" data-id="${row.id}">
                    <i class="ki-outline ki-trash"></i>
                </button>`);
            }
        }

        return `<div class="d-flex justify-content-end gap-2">${buttons.join('')}</div>`;
    }

    function route(path, id) {
        return path.replace('__id__', id);
    }

    function loadAvailablePermissions() {
        if (Object.keys(availablePermissions).length) {
            return Promise.resolve(availablePermissions);
        }

        return axios.get(config.urls.availablePermissions)
            .then(({ data }) => {
                availablePermissions = data.permissions || {};
                return availablePermissions;
            });
    }

    function renderPermissionGroups(selected = []) {
        const container = $('[data-permission-groups]');
        container.empty();

        Object.entries(availablePermissions).forEach(([module, permissions]) => {
            const group = $('<div class="mb-5"></div>');
            group.append(`<h5 class="fw-semibold text-gray-800 mb-3 text-uppercase">${module}</h5>`);

            const grid = $('<div class="row g-3"></div>');
            permissions.forEach((permission) => {
                const name = typeof permission === 'string' ? permission : permission.name;
                const label = typeof permission === 'string' ? permission : (permission.label ?? permission.name);
                const id = `perm_${name.replace('.', '_')}`;
                grid.append(`
                    <div class="col-md-6">
                        <label class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="${name}" name="permissions[]"
                                ${selected.includes(name) ? 'checked' : ''}>
                            <span class="form-check-label">${label}</span>
                        </label>
                    </div>
                `);
            });

            group.append(grid);
            container.append(group);
        });
    }

    tableEl.on('click', '.select-role', function () {
        const id = $(this).data('id');
        selectedRoleId = id;
        loadAvailablePermissions().then(() => {
            axios.get(route(config.urls.permissions, id))
                .then(({ data }) => {
                    $('[data-role-placeholder]').addClass('d-none');
                    $('#role-permissions-form').removeClass('d-none');
                    $('#permissions_role_id').val(id);
                    renderPermissionGroups(data.permissions || []);
                });
        });
    });

    const permissionsForm = $('#role-permissions-form');
    if (permissionsForm.length) {
        permissionsForm.on('submit', function (e) {
            e.preventDefault();
            const id = $('#permissions_role_id').val();
            const formData = $(this).serialize();
            axios.post(route(config.urls.syncPermissions, id), formData)
                .then(() => showSuccess(config.messages?.permissionsSynced || 'Saved'))
                .catch(() => showError('Unable to sync permissions'));
        });
    }

    const roleFormEl = document.getElementById('role-form');
    if (roleFormEl) {
        $('#role-form').on('submit', function (e) {
            e.preventDefault();
            const form = e.currentTarget;
            const roleId = $('#role_id').val();
            const isEdit = Boolean(roleId);
            const formData = new FormData(form);
            const url = isEdit ? route(config.urls.update, roleId) : config.urls.store;
            if (isEdit) {
                formData.append('_method', 'PUT');
            }
            clearErrors(form);

            axios.post(url, formData)
                .then(() => {
                    $('#roleModal').modal('hide');
                    form.reset();
                    rolesTable.ajax.reload(null, false);
                    const message = roleId ? config.messages?.roleUpdated : config.messages?.roleCreated;
                    showSuccess(message || 'Saved');
                })
                .catch(error => handleFormErrors(form, error));
        });

        tableEl.on('click', '.edit-role', function () {
            $('#role_id').val($(this).data('id'));
            $('#role_name').val($(this).data('name'));
            $('[data-role-modal-title]').text(config.labels?.edit || 'Edit Role');
            $('#roleModal').modal('show');
        });

        $('#roleModal').on('hidden.bs.modal', () => {
            roleFormEl.reset();
            clearErrors(roleFormEl);
            $('#role_id').val('');
            $('[data-role-modal-title]').text(config.labels?.create || 'Create Role');
        });

        tableEl.on('click', '.delete-role', function () {
            const id = $(this).data('id');
            confirmDelete().then((confirmed) => {
                if (!confirmed) {
                    return;
                }

                axios.delete(route(config.urls.delete, id))
                    .then(() => {
                        rolesTable.ajax.reload(null, false);
                        showSuccess(config.messages?.roleDeleted || 'Deleted');
                    })
                    .catch((error) => {
                        const message = error.response?.data?.message ?? 'Unable to delete role';
                        showError(message);
                    });
            });
        });

        $('#permission-form').on('submit', function (e) {
            e.preventDefault();
            const form = e.currentTarget;
            const formData = $(form).serialize();
            clearErrors(form);

            axios.post(config.urls.createPermission, formData)
                .then(() => {
                    $('#permissionModal').modal('hide');
                    form.reset();
                    availablePermissions = {};
                    loadAvailablePermissions().then(() => {
                        if (selectedRoleId) {
                            $(`.select-role[data-id="${selectedRoleId}"]`).trigger('click');
                        }
                    });
                    showSuccess(config.messages?.permissionCreated || 'Created');
                })
                .catch((error) => handleFormErrors(form, error));
        });
    }

    function clearErrors(form) {
        $(form).find('.is-invalid').removeClass('is-invalid');
        $(form).find('[data-error-for]').text('');
    }

    function handleFormErrors(form, error) {
        if (error.response?.status === 422) {
            Object.entries(error.response.data.errors || {}).forEach(([field, messages]) => {
                const input = $(form).find(`[name="${field}"]`);
                input.addClass('is-invalid');
                $(form).find(`[data-error-for="${field}"]`).text(messages[0]);
            });
        } else {
            showError('Unexpected error occurred');
        }
    }

    function confirmDelete() {
        const confirmTexts = config.confirm || {};
        return Swal.fire({
            title: confirmTexts.title || 'Delete role?',
            text: confirmTexts.message || 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: confirmTexts.confirm || 'Yes, delete',
            cancelButtonText: confirmTexts.cancel || 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false,
        }).then(result => result.isConfirmed);
    }
});
