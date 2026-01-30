import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.CategoryModule || {};
    const tableEl = $('#categories-table');

    if (!tableEl.length) {
        return;
    }

    const locale = config.locale || document.documentElement.lang || 'en';
    const form = document.getElementById('categoryForm');
    const modalEl = document.getElementById('categoryFormModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const selectAllCheckbox = document.getElementById('select-all-categories');
    const parentSelect = $('[data-category-parent]');
    const errorSummary = form?.querySelector('[data-category-errors]');
    const localeTabs = document.querySelectorAll('[data-category-locale-tab]');
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;
    const submitBtn = document.querySelector('[data-category-action="submit-form"]');
    const filterForm = document.getElementById('category-filter-form');
    const filterApplyBtn = document.querySelector('[data-filter-apply]');
    const filterResetBtn = document.querySelector('[data-filter-reset]');
    const INVALID_TAB_CLASS = 'category-locale-invalid';

    const filters = {
        status: '',
        state: 'active',
        featured: '',
    };

    const selectedIds = new Set();
    let isSubmitting = false;

    initParentSelect();

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

            if (filters.featured !== '') {
                params.featured = filters.featured;
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
                        <input class="form-check-input category-select" type="checkbox" value="${id}">
                    </div>
                `,
            },
            {
                data: null,
                render: (_, __, row) => {
                    const title = localizedText(row.title_translations, row.title);
                    const parentTitle = row.parent ? localizedText(row.parent.title_translations, row.parent.title) : null;
                    return `
                        <div class="d-flex align-items-center">
                            <img src="${row.image_url ?? ''}" class="category-thumb me-4" alt="${title ?? ''}">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-gray-900">${title ?? '—'}</span>
                                <span class="text-muted fs-8">${parentTitle ?? '—'}</span>
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
                data: 'parent',
                render: (parent) => parent ? localizedText(parent.title_translations, parent.title) ?? '—' : '—',
            },
            {
                data: 'is_featured',
                render: (value, type, row) => {
                    if (!value) {
                        return `<span class="badge badge-light">${config.labels?.no ?? 'No'}</span>`;
                    }

                    const order = Number(row.featured_order) || 0;
                    const orderLabel = order > 0 ? `#${order}` : '';

                    return `
                        <span class="badge badge-light-primary category-featured-badge">
                            <i class="ki-duotone ki-star fs-5"></i>
                            ${config.labels?.yes ?? 'Yes'} ${orderLabel}
                        </span>
                    `;
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

                    if (config.can?.update) {
                        actions.push(`
                            <button class="btn btn-sm btn-icon btn-light-primary" data-category-action="edit" data-id="${row.id}" title="${config.i18n.edit}">
                                ${icon('ki-pencil')}
                            </button>
                        `);
                    }

                    if (config.can?.delete) {
                        actions.push(`
                            <button class="btn btn-sm btn-icon btn-light-danger" data-category-action="delete" data-id="${row.id}" title="${config.i18n.delete}">
                                ${icon('ki-trash')}
                            </button>
                        `);
                    }

                    return `<div class="d-flex justify-content-end gap-2">${actions.join('')}</div>`;
                },
            },
        ],
        drawCallback: () => {
            tableEl.find('.category-select').each(function () {
                this.checked = selectedIds.has(this.value);
            });
            syncSelectionState();
        },
    });

    // Search with debounce
    let searchTimeout;
    document.getElementById('category-search')?.addEventListener('keyup', (event) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            table.search(event.target.value).draw();
        }, 500);
    });

    // Filter change handlers
    document.querySelectorAll('[data-category-filter="status"]').forEach((select) => {
        select.addEventListener('change', (event) => {
            filters.status = event.target.value;
            table.ajax.reload();
        });
    });

    document.querySelectorAll('[data-category-filter="state"]').forEach((select) => {
        select.addEventListener('change', (event) => {
            filters.state = event.target.value || 'active';
            table.ajax.reload();
        });
    });

    document.querySelectorAll('[data-category-filter="featured"]').forEach((select) => {
        select.addEventListener('change', (event) => {
            filters.featured = event.target.value;
            table.ajax.reload();
        });
    });

    document.querySelectorAll('[data-category-action="open-form"]').forEach((btn) => {
        btn.addEventListener('click', () => {
            resetForm();
            modal?.show();
        });
    });

    tableEl.on('click', '[data-category-action="edit"]', function () {
        const data = table.row($(this).closest('tr')).data();
        if (!data) return;
        fillForm(data);
        modal?.show();
    });

    tableEl.on('click', '[data-category-action="delete"]', function () {
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

    tableEl.on('change', '.category-select', function () {
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
        tableEl.find('.category-select').each(function () {
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

    document.getElementById('bulk-delete-btn')?.addEventListener('click', () => {
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

    document.querySelector('[data-category-action="submit-form"]')?.addEventListener('click', () => {
        form?.requestSubmit();
    });

    function initParentSelect() {
        if (!parentSelect.length) return;

        parentSelect.select2({
            dropdownParent: $('#categoryFormModal'),
            placeholder: parentSelect.data('placeholder') || '',
            allowClear: true,
            ajax: {
                url: config.routes.parents,
                dataType: 'json',
                delay: 250,
                data: params => ({
                    search: params.term,
                    exclude: form?.querySelector('input[name="category_id"]')?.value,
                }),
                processResults: data => ({
                    results: data.results ?? [],
                }),
            },
        });
    }

    function resetForm() {
        if (!form) return;
        form.reset();
        clearErrors(form);
        form.querySelector('input[name="category_id"]').value = '';
        form.querySelector('input[name="_method"]').value = 'POST';
        parentSelect.val(null).trigger('change');
        const featuredSwitch = form.querySelector('input[name="is_featured"]');
        if (featuredSwitch) {
            featuredSwitch.checked = false;
        }

        // Reset modal title to "Create"
        const modalTitle = document.querySelector('[data-modal-title]');
        if (modalTitle) {
            modalTitle.textContent = modalTitle.dataset.titleCreate || 'Add Category';
        }

        // Hide image preview
        const imagePreviewWrapper = document.querySelector('[data-category-image-preview-wrapper]');
        if (imagePreviewWrapper) {
            imagePreviewWrapper.classList.add('d-none');
        }
    }

    function fillForm(category) {
        if (!form) return;
        resetForm();

        const idInput = form.querySelector('input[name="category_id"]');
        const methodInput = form.querySelector('input[name="_method"]');
        if (idInput) idInput.value = category.id;
        if (methodInput) methodInput.value = 'PUT';

        // Change modal title to "Edit"
        const modalTitle = document.querySelector('[data-modal-title]');
        if (modalTitle) {
            modalTitle.textContent = modalTitle.dataset.titleEdit || 'Edit Category';
        }

        // Show image preview if exists
        const imagePreviewWrapper = document.querySelector('[data-category-image-preview-wrapper]');
        const imagePreview = document.querySelector('[data-category-image-preview]');
        if (imagePreviewWrapper && imagePreview && category.image_url) {
            imagePreview.src = category.image_url;
            imagePreviewWrapper.classList.remove('d-none');
        }

        setValue('select[name="status"]', category.status ?? 'draft');
        setValue('input[name="position"]', category.position ?? 0);
        setValue('input[name="featured_order"]', category.featured_order ?? 0);

        const featuredSwitch = form.querySelector('input[name="is_featured"]');
        if (featuredSwitch) {
            featuredSwitch.checked = Boolean(category.is_featured);
        }

        Object.entries(category.title_translations ?? {}).forEach(([code, value]) => {
            setValue(`input[name="title[${code}]"]`, value);
        });

        if (category.parent) {
            ensureParentLoaded(category.parent);
            parentSelect.val(String(category.parent.id)).trigger('change');
        } else {
            parentSelect.val(null).trigger('change');
        }
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

        const categoryId = form.querySelector('input[name="category_id"]')?.value;
        const isEdit = Boolean(categoryId);
        const formData = new FormData(form);
        const url = isEdit ? config.routes.update.replace('__ID__', categoryId) : config.routes.store;

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
        root.querySelectorAll('.invalid-feedback').forEach((feedback) => {
            feedback.textContent = '';
            feedback.style.display = 'none';
        });
        root.querySelectorAll('[data-error-for]').forEach((element) => {
            element.textContent = '';
            element.style.display = 'none';
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

                const locale = extractLocaleFromField(field);
                if (locale) {
                    markLocaleTabInvalid(locale);
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

    function extractLocaleFromField(field) {
        const match = field.match(/title\.(\w+)$/);
        return match ? match[1] : null;
    }

    function markLocaleTabInvalid(locale) {
        localeTabs.forEach(tab => {
            if (tab.dataset.locale === locale) {
                tab.classList.add(INVALID_TAB_CLASS);
            }
        });
    }

    function resetLocaleTabIndicators() {
        localeTabs.forEach(tab => tab.classList.remove(INVALID_TAB_CLASS));
    }

    function syncSelectionState() {
        const checkboxes = tableEl.find('.category-select');
        const checked = tableEl.find('.category-select:checked');

        if (selectAllCheckbox) {
            selectAllCheckbox.checked = checked.length > 0 && checked.length === checkboxes.length;
            selectAllCheckbox.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
        }

        const bulkDeleteBtnEl = document.getElementById('bulk-delete-btn');
        if (bulkDeleteBtnEl) {
            bulkDeleteBtnEl.disabled = selectedIds.size === 0;
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

    function ensureParentLoaded(parent) {
        if (!parent || !parentSelect.length) return;
        const exists = Array.from(parentSelect[0].options).some(option => Number(option.value) === Number(parent.id));
        if (!exists) {
            const option = new Option(parent.title ?? '-', parent.id, false, false);
            parentSelect.append(option);
        }
    }

    function toggleSubmitButton(isLoading) {
        if (!submitBtn) return;
        submitBtn.disabled = isLoading;
        submitBtn.dataset.ktIndicator = isLoading ? 'on' : 'off';
    }


    // Filter handling
    filterApplyBtn?.addEventListener('click', () => {
        table.ajax.reload();
        bootstrap.Offcanvas.getInstance(document.getElementById('categoryFiltersCanvas'))?.hide();
    });

    filterResetBtn?.addEventListener('click', () => {
        filterForm?.reset();
        filters.status = '';
        filters.state = 'active';
        filters.featured = '';
        table.ajax.reload();
    });
});
