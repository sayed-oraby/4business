import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.PageModule || {};
    const tableEl = $('#pages-table');

    if (!tableEl.length) {
        return;
    }

    const locale = config.locale || document.documentElement.lang || 'en';
    const form = document.getElementById('pageForm');
    const modalEl = document.getElementById('pageFormModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const viewModalEl = document.getElementById('pageViewModal');
    const viewModal = viewModalEl ? new bootstrap.Modal(viewModalEl) : null;
    const bulkDeleteBtn = document.querySelector('[data-page-action="bulk-delete"]');
    const selectAllCheckbox = document.getElementById('select-all-pages');
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;
    const slugInput = form?.querySelector('[data-page-slug]');
    const titleEnInput = form?.querySelector('[data-page-title-en]');

    let slugTouched = false;

    const viewFields = viewModalEl ? {
        image: viewModalEl.querySelector('[data-page-view="image"]'),
        title: viewModalEl.querySelector('[data-page-view="title"]'),
        description: viewModalEl.querySelector('[data-page-view="description"]'),
        descriptionBlock: viewModalEl.querySelector('[data-page-view="description-block"]'),
        slug: viewModalEl.querySelector('[data-page-view="slug"]'),
        status: viewModalEl.querySelector('[data-page-view="status"]'),
        state: viewModalEl.querySelector('[data-page-view="state"]'),
        translations: viewModalEl.querySelector('[data-page-view="translations"]'),
        created_at: viewModalEl.querySelector('[data-page-view="created_at"]'),
        updated_at: viewModalEl.querySelector('[data-page-view="updated_at"]'),
    } : {};

    const filters = {
        status: '',
        state: 'active',
    };

    const selectedIds = new Set();

    const table = tableEl.DataTable({
        processing: true,
        serverSide: true,
        order: [],
        ajax: (data, callback) => {
            const params = {
                draw: data.draw,
                start: data.start,
                length: data.length,
                'search[value]': data.search?.value,
                'order[0][column]': data.order?.[0]?.column,
                'order[0][dir]': data.order?.[0]?.dir,
                status: filters.status,
                state: filters.state,
            };

            if (filters.state === 'archived') {
                params.trashed = 'only';
            } else if (filters.state === 'all') {
                params.trashed = 'with';
            }

            axios.get(config.routes.data, { params })
                .then(response => callback(response.data))
                .catch(() => callback({ data: [], recordsTotal: 0, recordsFiltered: 0, draw: data.draw }));
        },
        columnDefs: [
            { targets: 0, orderable: false, className: 'text-center align-middle' },
            { targets: -1, orderable: false, className: 'text-end' },
        ],
        columns: [
            {
                data: 'id',
                render: (id) => `
                    <div class="form-check form-check-sm form-check-custom">
                        <input class="form-check-input page-select" type="checkbox" value="${id}">
                    </div>
                `,
            },
            {
                data: null,
                render: (_, __, row) => {
                    const title = localizedText(row.title_translations, row.title);
                    const description = localizedText(row.description_translations, row.description);
                    const image = row.image_url || '';

                    return `
                        <div class="d-flex align-items-center">
                            <img src="${image}" class="page-thumb me-4" alt="${title ?? ''}">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-gray-900">${title ?? '—'}</span>
                                <span class="text-muted fs-7">${description ?? '—'}</span>
                            </div>
                        </div>
                    `;
                },
            },
            {
                data: 'slug',
                render: (value) => `<span class="fw-semibold text-gray-700">${value ?? '—'}</span>`,
            },
            {
                data: null,
                render: (_, __, row) => {
                    if (row.is_deleted) {
                        return `<span class="badge badge-light-danger">${config.states?.archived ?? row.state_label}</span>`;
                    }

                    const label = config.statusLabels?.[row.status] ?? row.status_label ?? row.status;
                    const badgeClass = row.status === 'published'
                        ? 'badge-light-success'
                        : (row.status === 'draft' ? 'badge-light-warning' : 'badge-light');

                    return `<span class="badge ${badgeClass}">${label}</span>`;
                },
            },
            {
                data: 'updated_at',
                render: (value) => `<span class="text-muted fw-semibold">${formatDate(value)}</span>`,
            },
            {
                data: null,
                render: (row) => {
                    const actions = [];

                    actions.push(`
                        <button class="btn btn-sm btn-icon btn-light-info" data-page-action="view" title="${config.i18n.view}" data-id="${row.id}">
                            ${icon('ki-eye')}
                        </button>
                    `);

                    if (config.can?.update) {
                        actions.push(`
                            <button class="btn btn-sm btn-icon btn-light-primary" data-page-action="edit" title="${config.i18n.edit}" data-id="${row.id}">
                                ${icon('ki-pencil')}
                            </button>
                        `);
                    }

                    if (config.can?.delete) {
                        actions.push(`
                            <button class="btn btn-sm btn-icon btn-light-danger" data-page-action="delete" title="${config.i18n.delete}" data-id="${row.id}">
                                ${icon('ki-trash')}
                            </button>
                        `);
                    }

                    return `<div class="d-flex justify-content-end gap-2">${actions.join('')}</div>`;
                },
            },
        ],
        drawCallback: () => {
            tableEl.find('.page-select').each(function () {
                this.checked = selectedIds.has(this.value);
            });
            syncSelectionState();
        },
    });

    document.getElementById('page-search')?.addEventListener('keyup', (event) => {
        table.search(event.target.value).draw();
    });

    document.querySelectorAll('[data-page-filter="status"]').forEach((select) => {
        select.addEventListener('change', (event) => {
            filters.status = event.target.value;
            table.ajax.reload();
        });
    });

    document.querySelectorAll('[data-page-filter="state"]').forEach((select) => {
        select.addEventListener('change', (event) => {
            filters.state = event.target.value || 'active';
            table.ajax.reload();
        });
    });

    document.querySelectorAll('[data-page-action="open-form"]').forEach((btn) => {
        btn.addEventListener('click', () => {
            resetForm();
            modal?.show();
        });
    });

    tableEl.on('click', '[data-page-action="view"]', function () {
        const data = table.row($(this).closest('tr')).data();
        if (!data) return;
        populateViewModal(data);
        viewModal?.show();
    });

    tableEl.on('click', '[data-page-action="edit"]', function () {
        const data = table.row($(this).closest('tr')).data();
        if (!data) return;
        fillForm(data);
        modal?.show();
    });

    tableEl.on('click', '[data-page-action="delete"]', function () {
        const id = this.getAttribute('data-id');
        if (!id) return;

        confirmAction('single').then((confirmed) => {
            if (!confirmed) return;

            axios.post(config.routes.destroy.replace('__ID__', id), {
                _method: 'DELETE',
            }, {
                headers: { 'X-CSRF-TOKEN': csrfToken },
            })
                .then(() => {
                    showToast(config.messages.deleted);
                    selectedIds.delete(id);
                    table.ajax.reload(null, false);
                    syncSelectionState();
                })
                .catch(() => showToast('Error', true));
        });
    });

    tableEl.on('change', '.page-select', function () {
        const id = String(this.value);
        if (!id) return;

        if (this.checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }

        syncSelectionState();
    });

    selectAllCheckbox?.addEventListener('change', () => {
        const checked = selectAllCheckbox.checked;
        tableEl.find('.page-select').each(function () {
            this.checked = checked;
            const id = String(this.value);
            if (!id) return;

            if (checked) {
                selectedIds.add(id);
            } else {
                selectedIds.delete(id);
            }
        });
        syncSelectionState();
    });

    bulkDeleteBtn?.addEventListener('click', () => {
        if (selectedIds.size === 0) return;

        confirmAction('bulk', selectedIds.size).then((confirmed) => {
            if (!confirmed) return;

            axios.post(config.routes.bulkDestroy, {
                ids: Array.from(selectedIds),
                _method: 'DELETE',
            }, {
                headers: { 'X-CSRF-TOKEN': csrfToken },
            })
                .then(() => {
                    showToast(config.messages.bulkDeleted);
                    selectedIds.clear();
                    table.ajax.reload();
                    syncSelectionState();
                })
                .catch(() => showToast('Error', true));
        });
    });

    form?.addEventListener('submit', (event) => {
        event.preventDefault();
        submitForm();
    });

    slugInput?.addEventListener('input', () => {
        slugTouched = slugInput.value.trim().length > 0;
    });

    titleEnInput?.addEventListener('input', () => {
        if (!slugTouched && slugInput) {
            slugInput.value = slugify(titleEnInput.value);
        }
    });

    function resetForm() {
        if (!form) return;
        form.reset();
        slugTouched = false;
        const idInput = form.querySelector('input[name="page_id"]');
        const methodInput = form.querySelector('input[name="_method"]');
        if (idInput) idInput.value = '';
        if (methodInput) methodInput.value = 'POST';
        clearErrors();
    }

    function fillForm(page) {
        if (!form) return;
        resetForm();

        const idInput = form.querySelector('input[name="page_id"]');
        const methodInput = form.querySelector('input[name="_method"]');
        if (idInput) idInput.value = page.id;
        if (methodInput) methodInput.value = 'PUT';

        setValue('input[name="slug"]', page.slug ?? '');
        slugTouched = Boolean(page.slug);
        setValue('select[name="status"]', page.status ?? '');

        Object.entries(page.title_translations ?? {}).forEach(([localeKey, value]) => {
            setValue(`input[name="title[${localeKey}]"]`, value);
        });

        Object.entries(page.description_translations ?? {}).forEach(([localeKey, value]) => {
            const field = form.querySelector(`textarea[name="description[${localeKey}]"]`);
            if (field) field.value = value ?? '';
        });
    }

    function setValue(selector, value) {
        if (!form) return;
        const field = form.querySelector(selector);
        if (field) {
            field.value = value ?? '';
        }
    }

    function submitForm() {
        if (!form) return;

        const pageId = form.querySelector('input[name="page_id"]')?.value;
        const isEdit = Boolean(pageId);
        const formData = new FormData(form);
        const url = isEdit ? config.routes.update.replace('__ID__', pageId) : config.routes.store;

        if (!isEdit) {
            formData.set('_method', 'POST');
        }

        clearErrors();

        axios.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
            .then(() => {
                modal?.hide();
                table.ajax.reload(null, false);
                showToast(isEdit ? config.messages.updated : config.messages.created);
            })
            .catch((error) => handleErrors(error));
    }

    function clearErrors() {
        if (!form) return;
        form.querySelectorAll('.is-invalid').forEach((input) => input.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach((feedback) => feedback.remove());
        form.querySelectorAll('[data-error-for]').forEach((el) => {
            el.textContent = '';
            el.style.display = 'none';
        });
    }

    function handleErrors(error) {
        if (!form) return;

        const summaryMessages = new Set();
        const errorSummary = form.querySelector('[data-page-errors]');

        if (error.response?.status === 422 && error.response?.data?.errors) {
            Object.entries(error.response.data.errors).forEach(([field, messages]) => {
                // Use the message directly from backend (already translated)
                const firstMessage = Array.isArray(messages) ? messages[0] : messages;
                
                // Add to summary (backend messages are already translated)
                summaryMessages.add(firstMessage);
                
                const inputName = normalizeFieldName(field);
                const input = form.querySelector(`[name="${inputName}"]`);
                // Try both field name and normalized field name for error element
                let errorElement = form.querySelector(`[data-error-for="${field}"]`);
                if (!errorElement) {
                    errorElement = form.querySelector(`[data-error-for="${inputName}"]`);
                }
                
                if (input) {
                    input.classList.add('is-invalid');
                }
                
                if (errorElement) {
                    errorElement.textContent = firstMessage;
                    errorElement.style.display = 'block';
                } else if (input) {
                    // Fallback: create feedback element if data-error-for doesn't exist
                    let feedback = input.parentNode.querySelector('.invalid-feedback');
                    if (!feedback) {
                        feedback = document.createElement('div');
                        feedback.classList.add('invalid-feedback');
                        input.parentNode.appendChild(feedback);
                    }
                    feedback.textContent = firstMessage;
                }
            });

            if (errorSummary && summaryMessages.size > 0) {
                errorSummary.classList.remove('d-none');
                errorSummary.innerHTML = '<ul class="mb-0">' + Array.from(summaryMessages).map(msg => `<li>${msg}</li>`).join('') + '</ul>';
            }
        } else {
            showToast('Error', true);
        }
    }

    function normalizeFieldName(field) {
        return field.replace(/\.(\w+)/g, '[$1]');
    }

    function populateViewModal(data) {
        if (!viewModalEl) return;

        if (viewFields.image) {
            viewFields.image.src = data.image_url || '';
            viewFields.image.alt = data.title ?? '';
        }

        const localizedTitle = localizedText(data.title_translations, data.title);
        const localizedDescription = localizedText(data.description_translations, data.description);

        setViewText('title', localizedTitle);
        setViewText('description', localizedDescription);
        setViewText('descriptionBlock', localizedDescription);
        setViewText('slug', data.slug);
        setViewText('status', config.statusLabels?.[data.status] ?? data.status_label ?? data.status);
        setViewText('state', data.state_label ?? (data.is_deleted ? config.states?.archived : config.states?.active));
        setViewText('created_at', formatDate(data.created_at));
        setViewText('updated_at', formatDate(data.updated_at));

        if (viewFields.translations) {
            const container = viewFields.translations;
            container.innerHTML = '';
            let hasContent = false;

            const locales = config.locales || {};

            Object.entries(locales).forEach(([code, meta]) => {
                const titleValue = data.title_translations?.[code] ?? '';
                const descriptionValue = data.description_translations?.[code] ?? '';

                if (!titleValue && !descriptionValue) {
                    return;
                }

                hasContent = true;
                const wrapper = document.createElement('div');
                wrapper.classList.add('mb-3');
                wrapper.innerHTML = `
                    <div class="fw-bold text-gray-900">${meta.native ?? code.toUpperCase()}</div>
                    <div class="text-muted fs-7">
                        <div><span class="fw-semibold">${config.view?.fields?.title ?? 'Title'}:</span> ${titleValue || '—'}</div>
                        <div><span class="fw-semibold">${config.view?.fields?.description ?? 'Description'}:</span> ${descriptionValue || '—'}</div>
                    </div>
                `;
                container.appendChild(wrapper);
            });

            if (!hasContent) {
                container.textContent = config.view?.translationsEmpty || '—';
            }
        }
    }

    function setViewText(field, value) {
        const element = viewFields[field];
        if (!element) return;
        element.textContent = value ?? '—';
    }

    function showToast(message, isError = false) {
        if (window.toastr) {
            isError ? toastr.error(message) : toastr.success(message);
        } else {
            alert(message);
        }
    }

    function confirmAction(type, count = 1) {
        if (!window.Swal) {
            return Promise.resolve(confirm(config.confirm?.deleteMessage ?? 'Are you sure?'));
        }

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
            confirmButtonText: translations.confirm || 'OK',
            cancelButtonText: translations.cancel || 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false,
        }).then(result => result.isConfirmed);
    }

    function syncSelectionState() {
        const checkboxes = tableEl.find('.page-select');
        const checked = tableEl.find('.page-select:checked');

        if (selectAllCheckbox) {
            selectAllCheckbox.checked = checked.length > 0 && checked.length === checkboxes.length;
            selectAllCheckbox.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
        }

        if (bulkDeleteBtn) {
            bulkDeleteBtn.disabled = selectedIds.size === 0;
        }
    }

    function localizedText(translations = {}, fallback = null) {
        if (translations && typeof translations === 'object') {
            if (translations[locale]) {
                return translations[locale];
            }

            const fallbackLocale = document.documentElement.dataset.fallbackLocale || 'en';
            if (translations[fallbackLocale]) {
                return translations[fallbackLocale];
            }

            const first = Object.values(translations).find(Boolean);
            if (first) {
                return first;
            }
        }

        return fallback;
    }

    function formatDate(value) {
        if (!value) {
            return '—';
        }
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return value;
        }
        return date.toLocaleString();
    }

    function icon(name) {
        return `<i class="ki-outline ${name} fs-2"></i>`;
    }

    function slugify(value) {
        return value
            ?.toString()
            .normalize('NFKD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .replace(/-{2,}/g, '-') || '';
    }
});
