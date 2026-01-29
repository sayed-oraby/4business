import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.UserModule || {};
    const tableEl = $('#users-table');
    const rawHost = window.hostUrl || document.body.dataset.hostUrl || '/metronic/';
    const hostUrl = rawHost.endsWith('/') ? rawHost : `${rawHost}/`;
    const blankAvatar = `${hostUrl}media/avatars/blank.png`;
    const validationKeywordMap = {
        required: ['required'],
        email: ['email'],
        unique: ['unique', 'already been taken'],
        confirmed: ['confirm', 'match'],
        same: ['same', 'match'],
        min: ['at least', 'minimum', 'min'],
    };

    if (!tableEl.length) {
        return;
    }

    const routes = config.routes;
    const filterForm = document.getElementById('user-filter-form');
    const filterApplyBtn = document.querySelector('[data-filter-apply]');
    const filterResetBtn = document.querySelector('[data-filter-reset]');
    const avatarPreview = $('#user-avatar-preview');
    const removeAvatarInput = $('[name="remove_avatar"]');

    setAvatarPreview(null);

    const table = tableEl.DataTable({
        processing: true,
        serverSide: true,
        ajax: (data, callback) => {
            data.filters = getFilters();
            axios.get(routes.data, { params: data })
                .then(response => callback(response.data))
                .catch(() => callback({ data: [], recordsTotal: 0, recordsFiltered: 0 }));
        },
        columns: [
            {
                data: 'id',
                orderable: false,
                className: 'text-center align-middle',
                render: (data) => `
                    <div class="form-check form-check-sm form-check-custom">
                        <input class="form-check-input user-select" type="checkbox" value="${data}">
                    </div>
                `,
            },
            {
                data: 'name',
                className: 'text-center align-middle',
                render: (data, type, row) => {
                    const email = row.email ?? '';
                    const avatar = row.avatar ?? blankAvatar;

                    return `
                        <div class="d-flex flex-column align-items-center text-center gap-2">
                            <div class="symbol symbol-40px users-table-avatar">
                                <span class="symbol-label bg-light" style="background-size: cover; background-position: center; background-image: url('${avatar}');"></span>
                            </div>
                            <span class="text-gray-900 fw-bold lh-sm">${data}</span>
                            <span class="text-muted fw-semibold fs-7">${email}</span>
                        </div>
                    `;
                },
            },
            {
                data: 'mobile',
                defaultContent: '—',
                render: (value) => `<span class="text-gray-900 fw-semibold">${value ?? '—'}</span>`,
                className: 'text-center align-middle',
            },
            {
                data: 'roles',
                defaultContent: [],
                render: (roles) => {
                    const list = Array.isArray(roles) ? roles : (roles ? [roles] : []);
                    if (!list.length) {
                        return '<span class="text-muted">—</span>';
                    }

                return list.map(role => `
                    <span class="badge badge-light-primary me-1 mb-1">${role}</span>
                `).join('');
                },
                className: 'text-center align-middle',
            },
            {
                data: 'status',
                render: (value) => {
                    if (value === 'deleted') {
                        return `<span class="badge badge-light-danger">${config.statuses?.deleted || 'Archived'}</span>`;
                    }

                    return `<span class="badge badge-light-success">${config.statuses?.active || 'Active'}</span>`;
                },
                className: 'text-center align-middle',
            },
            {
                data: null,
                orderable: false,
                className: 'text-center align-middle',
                render: (row) => actionButtons(row),
            },
        ],
    });

    function actionButtons(row) {
        const chunks = [];
        chunks.push(`
            <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary me-1 view-user" data-id="${row.id}" title="View">
                ${icon('ki-eye')}
            </button>
        `);

        if (config.can?.update && row.status !== 'deleted') {
            chunks.push(`
                <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary me-1 edit-user" data-id="${row.id}" title="Edit">
                    ${icon('ki-pencil')}
                </button>
            `);
        }

        if (config.can?.delete) {
            if (row.status === 'deleted') {
                chunks.push(`
                    <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-success restore-user" data-id="${row.id}" title="Restore">
                        ${icon('ki-arrow-up')}
                    </button>
                `);
            } else {
                chunks.push(`
                    <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-danger delete-user" data-id="${row.id}" title="Delete">
                        ${icon('ki-trash')}
                    </button>
                `);
            }
        }

        return `<div class="d-flex justify-content-center flex-shrink-0">${chunks.join('')}</div>`;
    }

    $('#user-search').on('keyup', function () {
        table.search(this.value).draw();
    });

    filterApplyBtn?.addEventListener('click', () => {
        table.ajax.reload();
        bootstrap.Offcanvas.getInstance(document.getElementById('userFiltersCanvas'))?.hide();
    });

    filterResetBtn?.addEventListener('click', () => {
        filterForm?.reset();
        table.ajax.reload();
    });

    $('#select-all-users').on('change', function () {
        $('.user-select').prop('checked', $(this).is(':checked'));
    });

    $('#users-table').on('change', '.user-select', function () {
        if (!$(this).is(':checked')) {
            $('#select-all-users').prop('checked', false);
        }
    });

    $('#user-form').on('submit', function (e) {
        e.preventDefault();
        const form = e.currentTarget;
        const formData = new FormData(form);
        const userId = formData.get('user_id');
        const isEdit = Boolean(userId);
        const url = isEdit ? routes.update.replace('__id__', userId) : routes.store;
        const method = isEdit ? 'post' : 'post';
        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        clearErrors(form);
        const submitButton = form.querySelector('[data-submit-btn]');
        toggleButtonLoading(submitButton, true);

        axios({
            method,
            url,
            data: formData,
            headers: { 'Content-Type': 'multipart/form-data' },
        })
            .then(() => {
                $('#userModal').modal('hide');
                form.reset();
                table.ajax.reload(null, false);
                const message = isEdit ? config.messages?.updated : config.messages?.created;
                showSuccess(message || 'Success');
            })
            .catch((error) => handleFormErrors(form, error))
            .finally(() => toggleButtonLoading(submitButton, false));
    });

    $('#users-table').on('click', '.edit-user', function () {
        const id = $(this).data('id');
        axios.get(routes.show.replace('__id__', id))
            .then(({ data }) => {
                const form = document.getElementById('user-form');
                form.reset();
                clearErrors(form);
                $('[name="user_id"]').val(data.user.id);
                $('[name="name"]').val(data.user.name);
                $('[name="email"]').val(data.user.email);
                $('[name="mobile"]').val(data.user.mobile);
                $('[name="birthdate"]').val(data.user.birthdate);
                $('[name="gender"]').val(data.user.gender);
                $('#user-role-select').val(data.user.roles).trigger('change');
                $('.password-field').hide();
                removeAvatarInput.val(0);
                setAvatarPreview(data.user.avatar_url);
                $('[data-password-label]').text(config.labels?.passwordEdit || 'New password');
                $('[data-modal-title]').text(config.labels?.edit || 'Edit User');
                $('#userModal').modal('show');
            });
    });

    $('#userModal').on('hidden.bs.modal', () => {
        const form = document.getElementById('user-form');
        form.reset();
        clearErrors(form);
        $('[name="user_id"]').val('');
        removeAvatarInput.val(0);
        setAvatarPreview(null);
        $('[data-password-label]').text(config.labels?.password || 'Password');
        $('[data-modal-title]').text(config.labels?.create || 'Create User');
        $('.password-field').show();
    });

    $('#users-table').on('click', '.delete-user', function () {
        const id = $(this).data('id');
        confirmAction('delete').then((confirmed) => {
            if (!confirmed) {
                return;
            }

            axios.delete(routes.delete.replace('__id__', id))
                .then(() => {
                    table.ajax.reload(null, false);
                    showSuccess(config.messages?.deleted || 'Deleted');
                });
        });
    });

    $('#users-table').on('click', '.restore-user', function () {
        const id = $(this).data('id');
        axios.post(routes.restore.replace('__id__', id))
            .then(() => {
                table.ajax.reload(null, false);
                showSuccess(config.messages?.restored || 'Restored');
            });
    });

    $('#users-table').on('click', '.view-user', function () {
        const id = $(this).data('id');
        axios.get(routes.show.replace('__id__', id))
            .then(({ data }) => {
                const user = data.user;
                const viewConfig = config.view || {};
                const empty = viewConfig.empty || '—';
                const statusBadge = user.status === 'deleted'
                    ? `<span class="badge badge-light-danger">${user.status_label ?? config.statuses?.deleted ?? 'Archived'}</span>`
                    : `<span class="badge badge-light-success">${user.status_label ?? config.statuses?.active ?? 'Active'}</span>`;
                const genderLabel = user.gender_label
                    || (user.gender ? viewConfig[`gender_${user.gender}`] : null)
                    || empty;

                const infoRow = (iconName, label, value, raw = false) => `
                    <div class="d-flex align-items-center mb-4">
                        <span class="symbol symbol-35px symbol-circle bg-light-primary me-3">
                            ${icon(iconName)}
                        </span>
                        <div>
                            <div class="text-muted fw-semibold fs-8 text-uppercase">${label}</div>
                            <div class="fw-bold text-gray-900">${raw ? value : (value ?? empty)}</div>
                        </div>
                    </div>
                `;

                const contactSection = [
                    infoRow('ki-sms', viewConfig.email, user.email ?? empty),
                    infoRow('ki-call', viewConfig.mobile, user.mobile ?? empty),
                    infoRow('ki-calendar-8', viewConfig.birthdate, formatDate(user.birthdate)),
                    infoRow('ki-user', viewConfig.gender, genderLabel),
                ].join('');

                const metaSection = [
                    infoRow('ki-calendar-edit', viewConfig.created_at, formatDate(user.created_at)),
                    infoRow('ki-refresh-left', viewConfig.updated_at, formatDate(user.updated_at)),
                    infoRow('ki-shield-tick', viewConfig.status, statusBadge, true),
                ].join('');

                const rolesMarkup = Array.isArray(user.roles) && user.roles.length
                    ? user.roles.map(role => `<span class="badge badge-light-primary me-2 mb-2 px-3 py-2">${role}</span>`).join('')
                    : `<span class="text-muted">${empty}</span>`;

                $('#user-view-body').html(`
                    <div class="card border-0 shadow-sm mb-6">
                        <div class="card-body text-center py-6">
                            <div class="symbol symbol-90px symbol-circle mb-4">
                                <img src="${user.avatar_url ?? blankAvatar}" alt="${user.name}">
                            </div>
                            <h3 class="fw-bold mb-1">${user.name}</h3>
                            <div class="text-muted mb-4">${user.email ?? empty}</div>
                            <div class="d-flex flex-wrap justify-content-center gap-4">
                                <div class="border border-dashed rounded py-3 px-5 text-start">
                                    <span class="text-muted text-uppercase fs-8 d-block mb-1">${viewConfig.status_hint}</span>
                                    <div class="fw-bold fs-5">${statusBadge}</div>
                                </div>
                                <div class="border border-dashed rounded py-3 px-5 text-start">
                                    <span class="text-muted text-uppercase fs-8 d-block mb-1">${viewConfig.roles_hint}</span>
                                    <div class="fw-bold fs-5">${user.roles_count ?? (Array.isArray(user.roles) ? user.roles.length : 0)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-5">
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0">
                                    <h5 class="mb-0">${viewConfig.section_contact}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    ${contactSection}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0">
                                    <h5 class="mb-0">${viewConfig.section_meta}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    ${metaSection}
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-transparent border-0">
                                    <h5 class="mb-0">${viewConfig.section_roles}</h5>
                                </div>
                                <div class="card-body">
                                    ${rolesMarkup}
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                $('#userViewModal').modal('show');
            });
    });

    $('#bulk-delete-btn').on('click', () => {
        const ids = $('.user-select:checked').map((_, el) => $(el).val()).get();
        if (!ids.length) {
            return;
        }
        confirmAction('bulk', ids.length).then((confirmed) => {
            if (!confirmed) {
                return;
            }

            axios.post(routes.bulkDelete, { ids })
                .then(() => {
                    table.ajax.reload(null, false);
                    showSuccess(config.messages?.bulkDeleted || 'Deleted');
                });
        });
    });

    $('[name="avatar"]').on('change', function () {
        const file = this.files?.[0];
        if (!file) {
            setAvatarPreview(null);
            return;
        }
        const reader = new FileReader();
        reader.onload = (event) => setAvatarPreview(event.target?.result);
        reader.readAsDataURL(file);
        removeAvatarInput.val(0);
    });

    $('[data-kt-image-input-action="remove"]').on('click', () => {
        setAvatarPreview(null);
        removeAvatarInput.val(1);
    });

    function getFilters() {
        if (!filterForm) {
            return {};
        }

        return {
            role: filterForm.role?.value || '',
            status: filterForm.status?.value || '',
            date_from: filterForm.date_from?.value || '',
            date_to: filterForm.date_to?.value || '',
        };
    }

    function setAvatarPreview(source) {
        avatarPreview.css('background-image', `url('${source || blankAvatar}')`);
    }

    function clearErrors(form) {
        $(form).find('.is-invalid').removeClass('is-invalid');
        $(form).find('[data-error-for]').text('');
    }

    function handleFormErrors(form, error) {
        if (error.response?.status === 422) {
            const errors = error.response.data.errors || {};
            Object.entries(errors).forEach(([field, messages]) => {
                const baseField = field.replace(/\.\d+$/, '');
                const fieldSelector = $(form).find(`[name="${baseField}"]`).length
                    ? `[name="${baseField}"]`
                    : `[name="${baseField}[]"]`;
                const input = $(form).find(fieldSelector);
                input.addClass('is-invalid');
                const translated = translateValidation(baseField, messages[0]);
                $(form).find(`[data-error-for="${baseField}"]`).text(translated);
            });
        } else {
            showError('Something went wrong.');
        }
    }

    function icon(name) {
        return `<i class="ki-outline ${name} fs-2"></i>`;
    }

    function translateValidation(field, message) {
        const fieldMessages = config.validation?.[field];
        if (!fieldMessages) {
            return message;
        }

        const normalized = (message || '').toLowerCase();

        for (const [rule, translation] of Object.entries(fieldMessages)) {
            const keywords = validationKeywordMap[rule];
            if (!keywords) {
                continue;
            }

            const matched = keywords.some(keyword => normalized.includes(keyword));
            if (matched) {
                return translation;
            }
        }

        return fieldMessages.default ?? message;
    }

    function formatDate(value) {
        if (!value) {
            return config.view?.empty || '—';
        }

        try {
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) {
                return value;
            }

            return new Intl.DateTimeFormat(config.locale || 'en', {
                year: 'numeric',
                month: 'short',
                day: '2-digit',
            }).format(date);
        } catch (error) {
            return value;
        }
    }

    function confirmAction(type, count = 1) {
        const translations = config.confirm || {};
        const title = type === 'bulk' ? translations.bulkTitle : translations.deleteTitle;
        let message = type === 'bulk' ? translations.bulkMessage : translations.deleteMessage;

        if (type === 'bulk') {
            message = message?.replace(':count', count);
        }

        return Swal.fire({
            title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: translations.confirm || 'Yes',
            cancelButtonText: translations.cancel || 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false,
        }).then(result => result.isConfirmed);
    }

    function toggleButtonLoading(button, isLoading) {
        if (!button) {
            return;
        }

        if (isLoading) {
            button.setAttribute('data-kt-indicator', 'on');
            button.disabled = true;
        } else {
            button.setAttribute('data-kt-indicator', 'off');
            button.disabled = false;
        }
    }
});
