import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.BrandModule || {};
    const tableEl = $('#brands-table');

    if (!tableEl.length) {
        return;
    }

    const locale = config.locale || document.documentElement.lang || 'en';
    const form = document.getElementById('brandForm');
    const modalEl = document.getElementById('brandFormModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const bulkDeleteBtn = document.querySelector('[data-brand-action="bulk-delete"]');
    const selectAllCheckbox = document.getElementById('select-all-brands');
    const errorSummary = form?.querySelector('[data-brand-errors]');
    const localeTabs = document.querySelectorAll('[data-brand-locale-tab]');
    const submitBtn = document.querySelector('[data-brand-action="submit-form"]');
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;

    const FILTER_STATE_CLASS = 'brand-locale-invalid';

    const filters = {
        status: '',
        state: 'active',
    };

    const selectedIds = new Set();
    let isSubmitting = false;

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
                        <input class="form-check-input brand-select" type="checkbox" value="${id}">
                    </div>
                `,
            },
            {
                data: null,
                render: (_, __, row) => {
                    const title = localizedText(row.title_translations, row.title);
                    return `
                        <div class="d-flex align-items-center">
                            <img src="${row.image_url ?? ''}" class="brand-thumb me-4" alt="${title ?? ''}">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-gray-900">${title ?? '—'}</span>
                                <span class="text-muted fs-8">${row.status_label ?? ''}</span>
                            </div>
                        </div>
                    `;
                },
            },
            {
                data: 'status_label',
                render: (value, type, row) => {
                    const badgeClass = row.status === 'active'
                        ? 'badge-light-success'
                        : (row.status === 'draft' ? 'badge-light-warning' : 'badge-light-secondary');
                    return `<span class="badge ${badgeClass}">${value}</span>`;
                },
            },
            {
                data: 'position',
                render: (value) => `<span class="fw-semibold">${value ?? 0}</span>`,
            },
            {
                data: 'updated_at',
                render: (value) => `<span class="text-muted fw-semibold">${formatDate(value)}</span>`,
            },
            {
                data: null,
                render: (row) => {
                    const actions = [];

                    if (config.can?.update) {
                        actions.push(`
                            <button class="btn btn-sm btn-icon btn-light-primary" data-brand-action="edit" data-id="${row.id}" title="${config.actions?.edit ?? 'Edit'}">
                                ${icon('ki-pencil')}
                            </button>
                        `);
                    }

                    if (config.can?.delete) {
                        actions.push(`
                            <button class="btn btn-sm btn-icon btn-light-danger" data-brand-action="delete" data-id="${row.id}" title="${config.actions?.delete ?? 'Delete'}">
                                ${icon('ki-trash')}
                            </button>
                        `);
                    }

                    return `<div class="d-flex justify-content-end gap-2">${actions.join('')}</div>`;
                },
            },
        ],
        drawCallback: () => {
            tableEl.find('.brand-select').each(function () {
                this.checked = selectedIds.has(this.value);
            });
            syncSelectionState();
        },
    });

    document.getElementById('brand-search')?.addEventListener('keyup', (event) => {
        table.search(event.target.value).draw();
    });

    document.querySelectorAll('[data-brand-filter="status"]').forEach((select) => {
        select.addEventListener('change', (event) => {
            filters.status = event.target.value;
            table.ajax.reload();
        });
    });

    document.querySelectorAll('[data-brand-filter="state"]').forEach((select) => {
        select.addEventListener('change', (event) => {
            filters.state = event.target.value || 'active';
            table.ajax.reload();
        });
    });

    document.querySelectorAll('[data-brand-action="open-form"]').forEach((btn) => {
        btn.addEventListener('click', () => {
            resetForm();
            modal?.show();
        });
    });

    tableEl.on('click', '[data-brand-action="edit"]', function () {
        const data = table.row($(this).closest('tr')).data();
        if (!data) return;
        fillForm(data);
        modal?.show();
    });

    tableEl.on('click', '[data-brand-action="delete"]', function () {
        const id = this.getAttribute('data-id');
        if (!id) return;

        confirmAction('single').then((confirmed) => {
            if (!confirmed) return;

            axios.post(config.routes.destroy.replace('__ID__', id), { _method: 'DELETE' }, {
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

    tableEl.on('change', '.brand-select', function () {
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
        tableEl.find('.brand-select').each(function () {
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

    function resetForm() {
        if (!form) return;
        form.reset();
        clearErrors(form);
        const idInput = form.querySelector('input[name="brand_id"]');
        const methodInput = form.querySelector('input[name="_method"]');
        if (idInput) idInput.value = '';
        if (methodInput) methodInput.value = 'POST';
    }

    function fillForm(brand) {
        if (!form) return;
        resetForm();

        const idInput = form.querySelector('input[name="brand_id"]');
        const methodInput = form.querySelector('input[name="_method"]');
        if (idInput) idInput.value = brand.id;
        if (methodInput) methodInput.value = 'PUT';

        setValue('select[name="status"]', brand.status ?? 'draft');
        setValue('input[name="position"]', brand.position ?? 0);

        Object.entries(brand.title_translations ?? {}).forEach(([code, value]) => {
            setValue(`input[name="title[${code}]"]`, value);
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
        if (!form || isSubmitting) return;
        isSubmitting = true;
        toggleSubmitButton(true);

        const brandId = form.querySelector('input[name="brand_id"]')?.value;
        const isEdit = Boolean(brandId);
        const formData = new FormData(form);
        const url = isEdit ? config.routes.update.replace('__ID__', brandId) : config.routes.store;

        if (!isEdit) {
            formData.set('_method', 'POST');
        }

        clearErrors(form);

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
            .catch((error) => handleErrors(error, form))
            .finally(() => {
                isSubmitting = false;
                toggleSubmitButton(false);
            });
    }

    function clearErrors(context) {
        const root = context ?? form;
        if (!root) return;

        root.querySelectorAll('.is-invalid').forEach((input) => input.classList.remove('is-invalid'));
        root.querySelectorAll('.invalid-feedback').forEach((feedback) => feedback.remove());
        root.querySelectorAll('[data-error-for]').forEach((el) => {
            el.textContent = '';
            el.style.display = 'none';
        });

        if (!context || root === form) {
            hideErrorSummary();
            resetLocaleTabIndicators();
        }
    }

    function handleErrors(error, context) {
        const root = context ?? form;
        if (!root) return;

        const summaryMessages = new Set();

        if (error.response?.status === 422 && error.response?.data?.errors) {
            Object.entries(error.response.data.errors).forEach(([field, messages]) => {
                // Use the message directly from backend (already translated)
                const firstMessage = Array.isArray(messages) ? messages[0] : messages;
                
                // Add to summary (backend messages are already translated)
                summaryMessages.add(firstMessage);
                
                const inputName = normalizeFieldName(field);
                const input = root.querySelector(`[name="${inputName}"]`);
                // Try both field name and normalized field name for error element
                let errorElement = root.querySelector(`[data-error-for="${field}"]`);
                if (!errorElement) {
                    errorElement = root.querySelector(`[data-error-for="${inputName}"]`);
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

                const localeMatches = field.match(/title\.(\w+)$/);
                if (localeMatches) {
                    markLocaleTabInvalid(localeMatches[1]);
                }
            });

            showErrorSummary(Array.from(summaryMessages));
        } else {
            showToast('Error', true);
        }
    }

    function showErrorSummary(messages) {
        if (!errorSummary) return;
        if (!messages.length) {
            hideErrorSummary();
            return;
        }

        errorSummary.innerHTML = '';
        const list = document.createElement('ul');
        messages.forEach(message => {
            const item = document.createElement('li');
            item.textContent = message;
            list.appendChild(item);
        });
        errorSummary.appendChild(list);
        errorSummary.classList.remove('d-none');
    }

    function hideErrorSummary() {
        if (!errorSummary) return;
        errorSummary.classList.add('d-none');
        errorSummary.innerHTML = '';
    }

    function resetLocaleTabIndicators() {
        localeTabs.forEach(tab => tab.classList.remove(FILTER_STATE_CLASS));
    }

    function markLocaleTabInvalid(locale) {
        localeTabs.forEach(tab => {
            if (tab.dataset.locale === locale) {
                tab.classList.add(FILTER_STATE_CLASS);
            }
        });
    }

    function syncSelectionState() {
        const checkboxes = tableEl.find('.brand-select');
        const checked = tableEl.find('.brand-select:checked');

        if (selectAllCheckbox) {
            selectAllCheckbox.checked = checked.length > 0 && checked.length === checkboxes.length;
            selectAllCheckbox.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
        }

        if (bulkDeleteBtn) {
            bulkDeleteBtn.disabled = selectedIds.size === 0;
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

    function showToast(message, isError = false) {
        if (window.toastr) {
            isError ? toastr.error(message) : toastr.success(message);
        } else {
            alert(message);
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

    function normalizeFieldName(field) {
        return field.replace(/\.(\w+)/g, '[$1]');
    }

    function toggleSubmitButton(isLoading) {
        if (!submitBtn) return;
        submitBtn.disabled = isLoading;
        submitBtn.dataset.ktIndicator = isLoading ? 'on' : 'off';
    }
});
