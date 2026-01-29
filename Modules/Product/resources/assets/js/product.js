import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    initProductTable();
    initProductForm();
});

function initProductTable() {
    const config = window.ProductModule || {};
    const tableEl = $('#products-table');

    if (!tableEl.length) {
        return;
    }

    const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;
    const selectAllCheckbox = document.getElementById('select-all-products');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const searchInput = document.getElementById('product-search');
    const actionsI18n = config.actions || {};

    const filters = {
        status: '',
        state: 'active',
        featured: '',
        new_arrival: '',
        trending: '',
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

            if (filters.featured !== '') params.featured = filters.featured;
            if (filters.new_arrival !== '') params.new_arrival = filters.new_arrival;
            if (filters.trending !== '') params.trending = filters.trending;

            axios.get(config.routes.data, { params })
                .then((response) => callback(response.data))
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
                        <input class="form-check-input product-select" type="checkbox" value="${id}">
                    </div>
                `,
            },
            {
                data: null,
                render: (_, __, row) => {
                    const title = localizedText(row.title_translations, row.title) ?? '—';
                    const image = row.image_url ?? '';
                    const category = row.category?.title ?? '';
                    const brand = row.brand?.title ?? '';
                    const meta = [category, brand].filter(Boolean).join(' • ') || '—';

                    return `
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-50px me-4">
                                <span class="symbol-label bg-light">
                                    <img src="${image}" class="product-thumb" alt="${title ?? ''}">
                                </span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-gray-900">${title}</span>
                                <span class="text-muted fs-8">${meta}</span>
                            </div>
                        </div>
                    `;
                },
            },
            {
                data: 'sku',
                render: (value) => `<span class="fw-semibold">${value ?? '—'}</span>`,
            },
            {
                data: 'price',
                render: (value) => `<span class="fw-semibold text-gray-900">${formatPrice(value)}</span>`,
            },
            {
                data: null,
                render: (_, __, row) => {
                    const badgeClass = row.status === 'active'
                        ? 'badge-light-success'
                        : (row.status === 'draft' ? 'badge-light-warning' : 'badge-light-secondary');
                    const label = config.statusLabels?.[row.status] ?? row.status_label ?? row.status;

                    return `<span class="badge ${badgeClass}">${label}</span>`;
                },
            },
            {
                data: null,
                render: (_, __, row) => {
                    const flags = [];
                    if (row.is_featured) {
                        flags.push(`<span class="badge badge-light-primary product-flag-badge">${icon('ki-star')} ${config.flags?.featured ?? 'Featured'}</span>`);
                    }
                    if (row.is_new_arrival) {
                        flags.push(`<span class="badge badge-light-info product-flag-badge">${icon('ki-verify')} ${config.flags?.new_arrival ?? 'New'}</span>`);
                    }
                    if (row.is_trending) {
                        flags.push(`<span class="badge badge-light-warning product-flag-badge">${icon('ki-activity')} ${config.flags?.trending ?? 'Trending'}</span>`);
                    }

                    return flags.length ? flags.join('') : '<span class="text-muted">—</span>';
                },
            },
            {
                data: 'qty',
                render: (value) => `<span class="fw-semibold">${Number.isFinite(value) ? value : 0}</span>`,
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
                            <button class="btn btn-sm btn-icon btn-light-primary" data-product-action="edit" title="${actionsI18n.edit ?? 'Edit'}" data-id="${row.id}">
                                ${icon('ki-pencil')}
                            </button>
                        `);
                    }

                    if (config.can?.delete) {
                        actions.push(`
                            <button class="btn btn-sm btn-icon btn-light-danger" data-product-action="delete" title="${actionsI18n.delete ?? 'Delete'}" data-id="${row.id}">
                                ${icon('ki-trash')}
                            </button>
                        `);
                    }

                    return `<div class="d-flex justify-content-center gap-2">${actions.join('')}</div>`;
                },
            },
        ],
        drawCallback: () => {
            tableEl.find('.product-select').each(function () {
                this.checked = selectedIds.has(this.value);
            });
            syncSelectionState();
        },
    });

    searchInput?.addEventListener('keyup', (event) => {
        table.search(event.target.value).draw();
    });

    // Filter change handlers
    ['status', 'state', 'featured', 'new_arrival', 'trending'].forEach((key) => {
        document.querySelectorAll(`[data-product-filter="${key}"]`).forEach((select) => {
            select.addEventListener('change', (event) => {
                filters[key] = event.target.value;
                if (key === 'state' && !filters.state) {
                    filters.state = 'active';
                }
            });
        });
    });

    // Filter apply button
    document.querySelector('[data-filter-apply]')?.addEventListener('click', () => {
        table.ajax.reload();
    });

    // Filter reset button
    document.querySelector('[data-filter-reset]')?.addEventListener('click', () => {
        filters.status = '';
        filters.state = 'active';
        filters.featured = '';
        filters.new_arrival = '';
        filters.trending = '';
        
        document.querySelectorAll('[data-product-filter]').forEach((select) => {
            const key = select.getAttribute('data-product-filter');
            if (key === 'state') {
                select.value = 'active';
            } else {
                select.value = '';
            }
        });
        
        table.ajax.reload();
    });

    tableEl.on('change', '.product-select', function () {
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
        tableEl.find('.product-select').each(function () {
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

    tableEl.on('click', '[data-product-action="edit"]', function () {
        const id = this.getAttribute('data-id');
        if (!id) return;
        const editUrl = config.routes.edit?.replace('__ID__', id);
        if (editUrl) {
            window.location.href = editUrl;
        }
    });

    tableEl.on('click', '[data-product-action="delete"]', function () {
        const id = this.getAttribute('data-id');
        if (!id) return;

        confirmAction('single', 1, config.confirm).then((confirmed) => {
            if (!confirmed) return;

            axios.post(config.routes.destroy.replace('__ID__', id), { _method: 'DELETE' }, {
                headers: { 'X-CSRF-TOKEN': csrfToken },
            })
                .then(() => {
                    showToast(config.messages?.deleted ?? 'Deleted');
                    selectedIds.delete(id);
                    table.ajax.reload(null, false);
                    syncSelectionState();
                })
                .catch(() => showToast('Error', true));
        });
    });

    bulkDeleteBtn?.addEventListener('click', () => {
        if (!selectedIds.size) {
            return;
        }

        confirmAction('bulk', selectedIds.size, config.confirm).then((confirmed) => {
            if (!confirmed) return;

            axios.post(config.routes.bulkDestroy, {
                ids: Array.from(selectedIds),
                _method: 'DELETE',
            }, {
                headers: { 'X-CSRF-TOKEN': csrfToken },
            })
                .then(() => {
                    showToast(config.messages?.bulkDeleted ?? 'Deleted');
                    selectedIds.clear();
                    table.ajax.reload();
                    syncSelectionState();
                })
                .catch(() => showToast('Error', true));
        });
    });

    function syncSelectionState() {
        const checkboxes = tableEl.find('.product-select');
        const checked = tableEl.find('.product-select:checked');

        if (selectAllCheckbox) {
            selectAllCheckbox.checked = checked.length > 0 && checked.length === checkboxes.length;
            selectAllCheckbox.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
        }

        if (bulkDeleteBtn) {
            bulkDeleteBtn.disabled = selectedIds.size === 0;
        }
    }
}

function initProductForm() {
    const config = window.ProductForm || {};
    const form = document.getElementById('productForm');

    if (!form || Object.keys(config).length === 0) {
        return;
    }

    const submitBtn = document.querySelector('[data-product-submit]');
    const errorSummary = form.querySelector('[data-product-errors]');
    const localeTabs = document.querySelectorAll('[data-product-locale-tab]');
    const tagsSelect = $('[data-product-tags]');
    const categorySelect = $('[data-product-category]');
    const brandSelect = $('[data-product-brand]');
    const tagModalEl = document.getElementById('productTagModal');
    const tagModal = tagModalEl ? new bootstrap.Modal(tagModalEl) : null;
    const tagForm = document.getElementById('productTagForm');
    const dropzoneElement = document.querySelector('[data-product-dropzone]');
    const dropzonePreviews = dropzoneElement?.querySelector('[data-product-dropzone-previews]');
    const dropzoneMessage = dropzoneElement?.querySelector('[data-product-dropzone-message]');
    const dropzoneTemplate = document.getElementById('productDropzonePreviewTemplate')?.innerHTML;
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;

    let dropzone = null;
    let isSubmitting = false;
    const galleryTokenInput = form.querySelector('input[name="gallery_token"]');
    let galleryToken = galleryTokenInput?.value || config.galleryToken || generateToken();
    if (galleryTokenInput) {
        galleryTokenInput.value = galleryToken;
    }

    const productId = config.productId || null;
    let galleryRequestToken = 0;

    initTagsSelect();
    initCategorySelect();
    initBrandSelect();
    initDropzone();
    bindFormEvents();
    bindTagModal();

    function bindFormEvents() {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            submitForm();
        });

        tagForm?.addEventListener('submit', (event) => {
            event.preventDefault();
            submitTag();
        });
    }

    function submitForm() {
        if (isSubmitting) return;

        const isEdit = config.mode === 'edit';
        const targetId = isEdit ? productId : null;
        const url = isEdit
            ? config.routes.update?.replace('__ID__', targetId)
            : config.routes.store;

        if (!url) {
            return;
        }

        isSubmitting = true;
        toggleSubmitButton(true);
        clearErrors(form, { summaryEl: errorSummary, localeTabs });

        const formData = new FormData(form);
        formData.set('_method', isEdit ? 'PUT' : 'POST');
        formData.set('gallery_token', galleryToken);

        axios.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
            .then(({ data }) => {
                const product = data?.data?.product;
                if (!product) {
                    showToast(config.messages?.updated ?? 'Saved');
                    return;
                }

                if (config.mode === 'create') {
                    const editUrl = config.routes.edit?.replace('__ID__', product.id);
                    if (editUrl) {
                        window.location.href = editUrl;
                    } else if (config.routes.index) {
                        window.location.href = config.routes.index;
                    } else {
                        showToast(config.messages?.created ?? 'Created');
                    }
                } else {
                    showToast(config.messages?.updated ?? 'Updated');
                }
            })
            .catch((error) => handleErrors(error, form, { summaryEl: errorSummary, localeTabs }))
            .finally(() => {
                isSubmitting = false;
                toggleSubmitButton(false);
            });
    }

    function submitTag() {
        if (!tagForm) return;
        const formData = new FormData(tagForm);

        axios.post(config.routes.tagsStore, formData, {
            headers: { 'X-CSRF-TOKEN': csrfToken },
        })
            .then(({ data }) => {
                const tag = data?.data?.tag;
                if (!tag) {
                    return;
                }

                const option = new Option(localizedText(tag.title_translations, tag.title), tag.id, true, true);
                tagsSelect.append(option).trigger('change');
                tagForm.reset();
                tagModal?.hide();
                showToast(config.messages?.tagCreated ?? 'Tag created.');
            })
            .catch((error) => {
                const message = error.response?.data?.message ?? 'Failed to create tag.';
                showToast(message, true);
            });
    }

    function initTagsSelect() {
        if (!tagsSelect.length) return;

        tagsSelect.select2({
            placeholder: tagsSelect.data('placeholder') || '',
            allowClear: true,
            ajax: {
                url: config.routes.tagsIndex,
                dataType: 'json',
                delay: 200,
                data: (params) => ({ search: params.term }),
                processResults: (response) => ({
                    results: (response.tags || []).map((tag) => ({
                        id: tag.id,
                        text: localizedText(tag.title_translations, tag.title),
                    })),
                }),
            },
        });
    }

    function initCategorySelect() {
        if (!categorySelect.length) return;

        categorySelect.select2({
            placeholder: categorySelect.data('placeholder') || '',
            allowClear: true,
            ajax: {
                url: config.routes.categories,
                dataType: 'json',
                delay: 200,
                data: (params) => ({ search: params.term }),
                processResults: (response) => ({
                    results: (response.results || []).map((item) => ({
                        id: item.id,
                        text: item.text ?? item.title,
                    })),
                }),
            },
        });
    }

    function initBrandSelect() {
        if (!brandSelect.length) return;

        brandSelect.select2({
            placeholder: brandSelect.data('placeholder') || '',
            allowClear: true,
            ajax: {
                url: config.routes.brands,
                dataType: 'json',
                delay: 200,
                data: (params) => ({ search: params.term }),
                processResults: (response) => ({
                    results: (response.results || []).map((item) => ({
                        id: item.id,
                        text: item.text ?? item.title,
                    })),
                }),
            },
        });
    }

    function initDropzone() {
        const Dropzone = window.Dropzone;
        if (!Dropzone || !dropzoneElement) {
            return;
        }

        Dropzone.autoDiscover = false;

        dropzone = new Dropzone(dropzoneElement, {
            url: config.routes.galleryUpload,
            paramName: 'file',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            maxFilesize: config.upload?.maxFileSize ?? 5,
            acceptedFiles: 'image/*',
            addRemoveLinks: false,
            previewsContainer: dropzonePreviews || dropzoneElement,
            previewTemplate: dropzoneTemplate || undefined,
            init() {
                this.on('sending', (file, xhr, formData) => {
                    formData.append('upload_token', galleryToken);
                    if (productId) {
                        formData.append('product_id', productId);
                    }
                });

                this.on('success', (file, response) => {
                    file._galleryId = response?.gallery?.id;
                    toggleDropzoneMessage();
                });

                this.on('addedfile', () => toggleDropzoneMessage());
                this.on('error', () => showToast('Upload failed', true));

                this.on('removedfile', (file) => {
                    if (!file._galleryId) {
                        toggleDropzoneMessage();
                        return;
                    }

                    axios.delete(config.routes.galleryDestroy.replace('__ID__', file._galleryId), {
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                    })
                        .then(() => showToast(config.messages?.galleryDeleted ?? 'Image removed.'))
                        .catch(() => showToast('Failed to delete image.', true))
                        .finally(() => toggleDropzoneMessage());
                });
            },
        });

        if (productId) {
            loadExistingGallery(productId);
        }
    }

    function loadExistingGallery(id) {
        if (!dropzone) return;

        const requestId = ++galleryRequestToken;
        dropzone.removeAllFiles(true);
        if (dropzonePreviews) {
            dropzonePreviews.innerHTML = '';
        }
        toggleDropzoneMessage();

        axios.get(config.routes.galleryIndex.replace('__ID__', id))
            .then(({ data }) => {
                if (requestId !== galleryRequestToken) {
                    return;
                }

                (data.gallery || []).forEach((item) => {
                    const mockFile = {
                        name: `gallery-${item.id}`,
                        size: 1,
                        accepted: true,
                        _galleryId: item.id,
                    };
                    dropzone.emit('addedfile', mockFile);
                    dropzone.emit('thumbnail', mockFile, item.image_url);
                    dropzone.emit('complete', mockFile);
                });

                toggleDropzoneMessage();
            });
    }

    function toggleDropzoneMessage() {
        if (!dropzoneMessage || !dropzone) return;
        const count = dropzone.getAcceptedFiles().length + dropzone.getQueuedFiles().length;
        dropzoneMessage.classList.toggle('d-none', count > 0);
    }

    function bindTagModal() {
        document.querySelector('[data-product-action="open-tag-modal"]')?.addEventListener('click', () => {
            tagForm?.reset();
            tagModal?.show();
        });
    }

    function toggleSubmitButton(isLoading) {
        if (!submitBtn) return;
        submitBtn.disabled = isLoading;
        submitBtn.dataset.ktIndicator = isLoading ? 'on' : 'off';
    }
}

function confirmAction(type, count = 1, translations = {}) {
    const {
        deleteTitle = 'Delete item',
        deleteMessage = 'Are you sure you want to delete this item?',
        bulkTitle = 'Delete items',
        bulkMessage = 'Delete :count items?',
        confirm = 'Confirm',
        cancel = 'Cancel',
    } = translations;

    if (!window.Swal) {
        const message = type === 'bulk'
            ? (bulkMessage || '').replace(':count', count)
            : deleteMessage;
        return Promise.resolve(window.confirm(message || 'Are you sure?'));
    }

    const title = type === 'bulk' ? bulkTitle : deleteTitle;
    let text = type === 'bulk' ? bulkMessage : deleteMessage;
    if (type === 'bulk') {
        text = (text || '').replace(':count', count);
    }

    return Swal.fire({
        title,
        text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: confirm,
        cancelButtonText: cancel,
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-light',
        },
        buttonsStyling: false,
    }).then((result) => result.isConfirmed);
}

function showToast(message, isError = false) {
    if (window.toastr) {
        isError ? toastr.error(message) : toastr.success(message);
    } else {
        isError ? alert(message) : console.log(message);
    }
}

function localizedText(translations = {}, fallback = null) {
    if (translations && typeof translations === 'object') {
        const locale = document.documentElement.lang || 'en';
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
    if (!value) return '—';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }
    return date.toLocaleString();
}

function formatPrice(value) {
    const num = Number(value);
    if (Number.isNaN(num)) {
        return '—';
    }
    return num.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function icon(name) {
    return `<i class="ki-outline ${name} fs-2"></i>`;
}

function generateToken() {
    return `${Math.random().toString(36).slice(2)}${Date.now().toString(36)}`;
}

function normalizeFieldName(field) {
    return field.replace(/\.(\w+)/g, '[$1]');
}

function escapeSelector(value) {
    if (window.CSS?.escape) {
        return CSS.escape(value);
    }
    return value.replace(/([ #;?%&,.+*~\\':"!^$[\]()=>|/@])/g, '\\$1');
}

function clearErrors(context, { summaryEl, localeTabs } = {}) {
    if (!context) return;
    context.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
    context.querySelectorAll('.invalid-feedback').forEach((el) => el.remove());
    if (summaryEl) {
        hideErrorSummary(summaryEl);
    }
    if (localeTabs) {
        resetLocaleTabIndicators(localeTabs);
    }
}

function handleErrors(error, context, { summaryEl, localeTabs } = {}) {
    if (!context) return;

    const messages = new Set();
    const errors = error.response?.data?.errors;

    if (errors) {
        Object.entries(errors).forEach(([field, fieldMessages]) => {
            const normalized = normalizeFieldName(field);
            const input = context.querySelector(`[name="${escapeSelector(normalized)}"]`);
            if (input) {
                input.classList.add('is-invalid');
                let feedback = input.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    input.parentNode?.appendChild(feedback);
                }
                feedback.textContent = fieldMessages[0];
            }

            fieldMessages.forEach((msg) => messages.add(msg));

            if ((field.startsWith('title.') || field.startsWith('description.')) && localeTabs) {
                const locale = field.split('.')[1];
                markLocaleTabInvalid(localeTabs, locale);
            }
        });
    } else if (error.response?.data?.message) {
        messages.add(error.response.data.message);
    } else {
        messages.add('Something went wrong.');
    }

    if (summaryEl) {
        showErrorSummary(summaryEl, Array.from(messages));
    } else {
        Array.from(messages).forEach((msg) => showToast(msg, true));
    }
}

function showErrorSummary(container, messages) {
    if (!container || !messages.length) {
        hideErrorSummary(container);
        return;
    }

    container.classList.remove('d-none');
    container.innerHTML = '';

    const list = document.createElement('ul');
    messages.forEach((message) => {
        const item = document.createElement('li');
        item.textContent = message;
        list.appendChild(item);
    });

    container.appendChild(list);
}

function hideErrorSummary(container) {
    if (!container) return;
    container.classList.add('d-none');
    container.innerHTML = '';
}

function markLocaleTabInvalid(localeTabs, locale) {
    localeTabs?.forEach((tab) => {
        if (tab.dataset.locale === locale) {
            tab.classList.add('product-locale-invalid');
        }
    });
}

function resetLocaleTabIndicators(localeTabs) {
    localeTabs?.forEach((tab) => tab.classList.remove('product-locale-invalid'));
}
