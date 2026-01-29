import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.BannerModule || {};
    const tableEl = $('#banner-table');

    if (!tableEl.length) {
        return;
    }

    const locale = config.locale || document.documentElement.lang || 'en';
    const form = document.getElementById('bannerForm');
    const modalEl = document.getElementById('bannerFormModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const viewModalEl = document.getElementById('bannerViewModal');
    const viewModal = viewModalEl ? new bootstrap.Modal(viewModalEl) : null;
    const bulkDeleteBtn = document.querySelector('[data-banner-action="bulk-delete"]');
    const selectAllCheckbox = document.getElementById('select-all-banners');
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;

    const viewFields = viewModalEl ? {
        image: viewModalEl.querySelector('[data-banner-view="image"]'),
        title: viewModalEl.querySelector('[data-banner-view="title"]'),
        description: viewModalEl.querySelector('[data-banner-view="description"]'),
        placement: viewModalEl.querySelector('[data-banner-view="placement"]'),
        status: viewModalEl.querySelector('[data-banner-view="status"]'),
        state: viewModalEl.querySelector('[data-banner-view="state"]'),
        // starts_at: viewModalEl.querySelector('[data-banner-view="starts_at"]'),
        // ends_at: viewModalEl.querySelector('[data-banner-view="ends_at"]'),
        button_label: viewModalEl.querySelector('[data-banner-view="button_label"]'),
        button_url: viewModalEl.querySelector('[data-banner-view="button_url"]'),
        created_at: viewModalEl.querySelector('[data-banner-view="created_at"]'),
        updated_at: viewModalEl.querySelector('[data-banner-view="updated_at"]'),
    } : {};

    const filters = {
        placement: '',
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
                placement: filters.placement,
                status: filters.status,
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
            { targets: -1, orderable: false, className: 'text-center align-middle' },
        ],
        columns: [
            {
                data: 'id',
                render: (id) => `
                    <div class="form-check form-check-sm form-check-custom">
                        <input class="form-check-input banner-select" type="checkbox" value="${id}">
                    </div>
                `,
            },
            {
                data: 'image_url',
                render: (value, type, row) => `
                    <div class="d-flex align-items-center">
                        <img src="${value || ''}" class="banner-thumb me-3" alt="${row.title ?? ''}">
                    </div>
                `,
            },
            {
                data: 'title',
                render: (value, type, row) => {
                    const title = localizedText(row.title_translations, row.title);
                    const description = localizedText(row.description_translations, row.description);

                    return `
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-gray-900">${title ?? '—'}</span>
                            <span class="text-muted fs-7">${description ?? '—'}</span>
                        </div>
                    `;
                },
            },

            {
                data: 'status_label',
                render: (value, type, row) => {
                    if (row.is_deleted) {
                        return `<span class="badge badge-light-danger">${config.states?.archived ?? value}</span>`;
                    }

                    const badgeClass = row.status === 'active' ? 'badge-light-success' : 'badge-light-secondary';
                    return `<span class="badge ${badgeClass}">${value}</span>`;
                },
            },

            {
                data: null,
                render: (row) => `
                    <div class="d-flex justify-content-center gap-2">

                        <button class="btn btn-sm btn-icon btn-light-primary" data-banner-action="edit" title="${config.i18n.edit}" data-id="${row.id}">
                            ${icon('ki-pencil')}
                        </button>
                        <button class="btn btn-sm btn-icon btn-light-danger" data-banner-action="delete" title="${config.i18n.delete}" data-id="${row.id}">
                            ${icon('ki-trash')}
                        </button>
                    </div>
                `,
            },
        ],
        drawCallback: () => {
            tableEl.find('.banner-select').each(function () {
                this.checked = selectedIds.has(this.value);
            });
            syncSelectionState();
        },
    });

    document.getElementById('banner-search')?.addEventListener('keyup', (event) => {
        table.search(event.target.value).draw();
    });

    document.querySelectorAll('[data-banner-filter="placement"]').forEach((el) => {
        el.addEventListener('change', (event) => {
            filters.placement = event.target.value;
            table.ajax.reload();
        });
    });

    document.querySelectorAll('[data-banner-filter="status"]').forEach((el) => {
        el.addEventListener('change', (event) => {
            filters.status = event.target.value;
            table.ajax.reload();
        });
    });

    document.querySelectorAll('[data-banner-filter="state"]').forEach((el) => {
        el.addEventListener('change', (event) => {
            filters.state = event.target.value || 'active';
            table.ajax.reload();
        });
    });

    document.querySelectorAll('[data-banner-action="open-form"]').forEach((btn) => {
        btn.addEventListener('click', () => {
            resetForm();
            modal?.show();
        });
    });

    tableEl.on('click', '[data-banner-action="view"]', function () {
        const data = table.row($(this).closest('tr')).data();
        if (!data) return;
        populateViewModal(data);
        viewModal?.show();
    });

    tableEl.on('click', '[data-banner-action="edit"]', function () {
        const data = table.row($(this).closest('tr')).data();
        if (!data) return;
        fillForm(data);
        modal?.show();
    });

    tableEl.on('click', '[data-banner-action="delete"]', function () {
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

    tableEl.on('change', '.banner-select', function () {
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
        tableEl.find('.banner-select').each(function () {
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
        const idInput = form.querySelector('input[name="banner_id"]');
        if (idInput) idInput.value = '';
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.value = 'POST';

        const imagePreviewContainer = form.querySelector('#banner-image-preview-container');
        const imagePreview = form.querySelector('#banner-image-preview');
        if (imagePreviewContainer) imagePreviewContainer.classList.add('d-none');
        if (imagePreview) imagePreview.src = '';

        clearErrors();
    }

    function fillForm(banner) {
        if (!form) return;
        resetForm();
        const idInput = form.querySelector('input[name="banner_id"]');
        const methodInput = form.querySelector('input[name="_method"]');
        if (idInput) idInput.value = banner.id;
        if (methodInput) methodInput.value = 'PUT';

        setValue('input[name="button_label"]', banner.button?.label ?? '');
        setValue('input[name="button_url"]', banner.button?.url ?? '');
        setValue('select[name="placement"]', banner.placement ?? '');
        setValue('select[name="status"]', banner.status ?? '');
        // setValue('input[name="starts_at"]', toDateTimeInput(banner.schedule?.starts_at));
        // setValue('input[name="ends_at"]', toDateTimeInput(banner.schedule?.ends_at));
        setValue('input[name="sort_order"]', banner.sort_order ?? 0);

        Object.entries(banner.title_translations ?? {}).forEach(([locale, value]) => {
            setValue(`input[name="title[${locale}]"]`, value);
        });

        Object.entries(banner.description_translations ?? {}).forEach(([locale, value]) => {
            const field = form.querySelector(`textarea[name="description[${locale}]"]`);
            if (field) field.value = value ?? '';
        });

        const imagePreviewContainer = form.querySelector('#banner-image-preview-container');
        const imagePreview = form.querySelector('#banner-image-preview');
        if (imagePreviewContainer && imagePreview) {
            if (banner.image_url) {
                imagePreview.src = banner.image_url;
                imagePreviewContainer.classList.remove('d-none');
            } else {
                imagePreviewContainer.classList.add('d-none');
                imagePreview.src = '';
            }
        }
    }

    function setValue(selector, value) {
        if (!form) return;
        const field = form.querySelector(selector);
        if (field) field.value = value ?? '';
    }

    function submitForm() {
        const bannerId = form.querySelector('input[name="banner_id"]').value;
        const isEdit = Boolean(bannerId);
        const formData = new FormData(form);
        const url = isEdit ? config.routes.update.replace('__ID__', bannerId) : config.routes.store;
        const submitBtn = form.querySelector('[data-banner-action="submit-form"]');

        if (!isEdit) {
            formData.set('_method', 'POST');
        }

        clearErrors();

        // Show loading state
        if (submitBtn) {
            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;
        }

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
            .catch((error) => handleErrors(error))
            .finally(() => {
                // Hide loading state
                if (submitBtn) {
                    submitBtn.removeAttribute('data-kt-indicator');
                    submitBtn.disabled = false;
                }
            });
    }

    function clearErrors() {
        if (!form) return;
        form.querySelectorAll('.is-invalid').forEach((input) => input.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach((feedback) => feedback.remove());
        form.querySelectorAll('[data-error-for]').forEach((el) => {
            el.textContent = '';
            el.style.display = 'none';
        });
        const errorSummary = form.querySelector('[data-banner-errors]');
        if (errorSummary) {
            errorSummary.classList.add('d-none');
        }
    }

    function handleErrors(error) {
        if (!form) return;

        const summaryMessages = new Set();
        const errorSummary = form.querySelector('[data-banner-errors]');

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

    function showToast(message, isError = false) {
        if (window.toastr) {
            isError ? toastr.error(message) : toastr.success(message);
        } else {
            alert(message);
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

        setViewText('title', localizedText(data.title_translations, data.title));
        setViewText('description', localizedText(data.description_translations, data.description));
        setViewText('placement', data.placement_label ?? data.placement);
        setViewText('status', data.status_label ?? data.status);
        setViewText('state', data.state_label ?? (data.is_deleted ? config.states?.archived : config.states?.active));
        // setViewText('starts_at', formatDate(data.schedule?.starts_at));
        // setViewText('ends_at', formatDate(data.schedule?.ends_at));
        setViewText('button_label', data.button?.label);
        if (viewFields.button_url) {
            viewFields.button_url.textContent = data.button?.url ?? '—';
            viewFields.button_url.href = data.button?.url || '#';
        }
        setViewText('created_at', formatDate(data.created_at));
        setViewText('updated_at', formatDate(data.updated_at));
    }

    function setViewText(field, value) {
        const element = viewFields[field];
        if (!element) return;
        element.textContent = value ?? '—';
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

    function toDateTimeInput(value) {
        if (!value) {
            return '';
        }

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return '';
        }

        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');

        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    function confirmAction(type, count = 1) {
        if (!window.Swal) {
            return Promise.resolve(confirm(config.i18n.confirmDelete));
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
        const checkboxes = tableEl.find('.banner-select');
        const checked = tableEl.find('.banner-select:checked');

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

    function icon(name) {
        return `<i class="ki-outline ${name} fs-2"></i>`;
    }
});
