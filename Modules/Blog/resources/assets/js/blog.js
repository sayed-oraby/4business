import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.BlogModule || {};
    const tableEl = $('#blogs-table');

    if (!tableEl.length) {
        return;
    }

    const locale = config.locale || document.documentElement.lang || 'en';
    const form = document.getElementById('blogForm');
    const modalEl = document.getElementById('blogFormModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const viewModalEl = document.getElementById('blogViewModal');
    const viewModal = viewModalEl ? new bootstrap.Modal(viewModalEl) : null;
    const tagModalEl = document.getElementById('blogTagModal');
    const tagModal = tagModalEl ? new bootstrap.Modal(tagModalEl) : null;
    const bulkDeleteBtn = document.querySelector('[data-blog-action="bulk-delete"]');
    const selectAllCheckbox = document.getElementById('select-all-blogs');
    const tagsSelect = $('[data-blog-tags]');
    const dropzoneElement = document.querySelector('[data-blog-dropzone]');
    const dropzonePreviews = dropzoneElement?.querySelector('[data-blog-dropzone-previews]');
    const dropzoneMessage = dropzoneElement?.querySelector('[data-blog-dropzone-message]');
    const dropzoneTemplate = document.getElementById('blogDropzonePreviewTemplate')?.innerHTML;
    const authorSelect = $('[data-blog-author]');
    const errorSummary = form?.querySelector('[data-blog-errors]');
    const localeTabs = document.querySelectorAll('[data-blog-locale-tab]');
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;

    let galleryToken = null;
    let currentBlogId = null;
    let dropzone = null;
    let tagsCache = Array.isArray(config.tags) ? config.tags : [];
    let galleryRequestToken = 0;
    const selectedIds = new Set();

    initTagsSelect();
    initAuthorSelect();
    initDropzone();

    const filters = {
        status: '',
        state: 'active',
    };

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
                        <input class="form-check-input blog-select" type="checkbox" value="${id}">
                    </div>
                `,
            },
            {
                data: null,
                render: (_, __, row) => {
                    const title = localizedText(row.title_translations, row.title);
                    const description = localizedText(row.short_description_translations, row.short_description);
                    const image = row.image_url || '';

                    return `
                        <div class="d-flex align-items-center">
                            <img src="${image}" class="blog-thumb me-4" alt="${title ?? ''}">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-gray-900">${title ?? '—'}</span>
                                <span class="text-muted fs-7">${description ?? '—'}</span>
                            </div>
                        </div>
                    `;
                },
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
                        : (row.status === 'draft' ? 'badge-light-warning' : 'badge-light-secondary');

                    return `<span class="badge ${badgeClass}">${label}</span>`;
                },
            },
            {
                data: 'tags',
                render: (tags = []) => {
                    if (!tags.length) {
                        return '<span class="text-muted">—</span>';
                    }

                    return tags.map(tag => `<span class="badge badge-light fw-semibold me-1 mb-1">${localizedText(tag.title_translations, tag.title)}</span>`).join('');
                },
            },
            {
                data: 'author',
                render: (author) => {
                    if (!author) {
                        return '<span class="text-muted">—</span>';
                    }

                    return `
                        <div class="d-flex flex-column">
                            <span class="fw-semibold text-gray-900">${author.name ?? '—'}</span>
                            <span class="text-muted fs-7">${author.email ?? ''}</span>
                        </div>
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

                    actions.push(`
                        <button class="btn btn-sm btn-icon btn-light-info" data-blog-action="view" title="${config.i18n.view}" data-id="${row.id}">
                            ${icon('ki-eye')}
                        </button>
                    `);

                    if (config.can?.update) {
                        actions.push(`
                            <button class="btn btn-sm btn-icon btn-light-primary" data-blog-action="edit" title="${config.i18n.edit}" data-id="${row.id}">
                                ${icon('ki-pencil')}
                            </button>
                        `);
                    }

                    if (config.can?.delete) {
                        actions.push(`
                            <button class="btn btn-sm btn-icon btn-light-danger" data-blog-action="delete" title="${config.i18n.delete}" data-id="${row.id}">
                                ${icon('ki-trash')}
                            </button>
                        `);
                    }

                    return `<div class="d-flex justify-content-end gap-2">${actions.join('')}</div>`;
                },
            },
        ],
        drawCallback: () => {
            tableEl.find('.blog-select').each(function () {
                this.checked = selectedIds.has(this.value);
            });
            syncSelectionState();
        },
    });

    // filters already defined above

    document.getElementById('blog-search')?.addEventListener('keyup', (event) => {
        table.search(event.target.value).draw();
    });

    document.querySelectorAll('[data-blog-filter="status"]').forEach((select) => {
        select.addEventListener('change', (event) => {
            filters.status = event.target.value;
            table.ajax.reload();
        });
    });

    document.querySelectorAll('[data-blog-filter="state"]').forEach((select) => {
        select.addEventListener('change', (event) => {
            filters.state = event.target.value || 'active';
            table.ajax.reload();
        });
    });

    document.querySelectorAll('[data-blog-action="open-form"]').forEach((btn) => {
        btn.addEventListener('click', () => {
            resetForm();
            modal?.show();
        });
    });

    document.querySelector('[data-blog-action="open-tag-modal"]')?.addEventListener('click', () => {
        if (!tagModal) return;
        const tagForm = document.getElementById('blogTagForm');
        tagForm?.reset();
        clearErrors(tagForm);
        tagModal.show();
    });

    document.getElementById('blogTagForm')?.addEventListener('submit', (event) => {
        event.preventDefault();
        submitTagForm();
    });

    tableEl.on('click', '[data-blog-action="view"]', function () {
        const data = table.row($(this).closest('tr')).data();
        if (!data) return;
        populateViewModal(data);
        viewModal?.show();
    });

    tableEl.on('click', '[data-blog-action="edit"]', function () {
        const data = table.row($(this).closest('tr')).data();
        if (!data) return;
        fillForm(data);
        modal?.show();
    });

    tableEl.on('click', '[data-blog-action="delete"]', function () {
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

    tableEl.on('change', '.blog-select', function () {
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
        tableEl.find('.blog-select').each(function () {
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

    document.querySelector('[data-blog-action="submit-form"]')?.addEventListener('click', () => {
        form?.requestSubmit();
    });

    function initTagsSelect() {
        tagsSelect.select2({
            dropdownParent: $('#blogFormModal'),
            placeholder: tagsSelect.data('placeholder') || '',
            data: formatTagsForSelect(tagsCache),
            ajax: {
                url: config.routes.tagsIndex,
                dataType: 'json',
                delay: 200,
                data: params => ({ search: params.term }),
                processResults: data => ({
                    results: formatTagsForSelect(data.tags ?? []),
                }),
            },
        });
    }

    function formatTagsForSelect(tags) {
        return tags.map(tag => ({
            id: tag.id,
            text: localizedText(tag.title_translations, tag.title),
        }));
    }

    function initAuthorSelect() {
        if (!authorSelect.length) return;

        authorSelect.select2({
            dropdownParent: $('#blogFormModal'),
            placeholder: authorSelect.data('placeholder') || '',
            allowClear: true,
            ajax: {
                url: config.routes.authors,
                dataType: 'json',
                delay: 250,
                data: params => ({
                    search: params.term,
                    page: params.page || 1,
                }),
                processResults: (data, params) => {
                    params.page = params.page || 1;
                    const results = (data.results || []).map(user => ({
                        id: user.id,
                        text: user.text || `${user.name ?? ''} — ${user.email ?? ''}`,
                        name: user.name,
                        email: user.email,
                    }));

                    return {
                        results,
                        pagination: {
                            more: data.pagination?.more ?? false,
                        },
                    };
                },
            },
            templateResult: formatAuthorOption,
            templateSelection: formatAuthorSelection,
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
            maxFilesize: 5,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            dictRemoveFile: 'Remove',
            previewsContainer: dropzonePreviews || dropzoneElement,
            previewTemplate: dropzoneTemplate || undefined,
            init() {
                this.on('addedfile', () => toggleDropzoneMessage());
                this.on('sending', (file, xhr, formData) => {
                    formData.append('upload_token', galleryToken);
                    if (currentBlogId) {
                        formData.append('blog_id', currentBlogId);
                    }
                });

                this.on('success', (file, response) => {
                    file._galleryId = response.gallery?.id;
                    toggleDropzoneMessage();
                });

                this.on('error', () => {
                    showToast('Upload failed', true);
                });

                this.on('removedfile', (file) => {
                    if (!file._galleryId) {
                        toggleDropzoneMessage();
                        return;
                    }

                    axios.delete(config.routes.galleryDestroy.replace('__ID__', file._galleryId), {
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                    })
                        .then(() => {
                            showToast(config.messages.galleryDeleted);
                            toggleDropzoneMessage();
                        })
                        .catch(() => showToast('Error', true));
                });
            },
        });
    }

    function resetForm() {
        if (!form) return;

        form.reset();
        clearErrors(form);
        galleryToken = generateToken();
        currentBlogId = null;
        form.querySelector('input[name="blog_id"]').value = '';
        form.querySelector('input[name="_method"]').value = 'POST';
        form.querySelector('input[name="gallery_token"]').value = galleryToken;
        tagsSelect.val(null).trigger('change');
        authorSelect.val(null).trigger('change');
        dropzone?.removeAllFiles(true);
        if (dropzonePreviews) {
            dropzonePreviews.innerHTML = '';
        }
        toggleDropzoneMessage();
    }

    function fillForm(blog) {
        if (!form) return;
        resetForm();

        currentBlogId = blog.id;
        const idInput = form.querySelector('input[name="blog_id"]');
        const methodInput = form.querySelector('input[name="_method"]');
        if (idInput) idInput.value = blog.id;
        if (methodInput) methodInput.value = 'PUT';

        galleryToken = generateToken();
        form.querySelector('input[name="gallery_token"]').value = galleryToken;

        setValue('select[name="status"]', blog.status ?? 'draft');

        Object.entries(blog.title_translations ?? {}).forEach(([code, value]) => {
            setValue(`input[name="title[${code}]"]`, value);
        });

        Object.entries(blog.short_description_translations ?? {}).forEach(([code, value]) => {
            const field = form.querySelector(`textarea[name="short_description[${code}]"]`);
            if (field) field.value = value ?? '';
        });

        Object.entries(blog.description_translations ?? {}).forEach(([code, value]) => {
            const field = form.querySelector(`textarea[name="description[${code}]"]`);
            if (field) field.value = value ?? '';
        });

        const tagIds = (blog.tags || []).map(tag => tag.id);
        ensureTagsLoaded(blog.tags || []);
        tagsSelect.val(tagIds).trigger('change');

        if (blog.author) {
            ensureAuthorLoaded(blog.author);
            authorSelect.val(blog.author.id).trigger('change');
        } else {
            authorSelect.val(null).trigger('change');
        }

        loadGallery(blog.id);
    }

    function setValue(selector, value) {
        if (!form) return;
        const field = form.querySelector(selector);
        if (field) {
            field.value = value ?? '';
        }
    }

    function loadGallery(blogId) {
        if (!dropzone) return;

        const requestId = ++galleryRequestToken;
        dropzone.removeAllFiles(true);
        if (dropzonePreviews) {
            dropzonePreviews.innerHTML = '';
        }
        toggleDropzoneMessage();

        axios.get(config.routes.gallery.replace('__ID__', blogId))
            .then(({ data }) => {
                if (requestId !== galleryRequestToken || currentBlogId !== blogId) {
                    return;
                }

                const gallery = data.gallery ?? [];
                gallery.forEach(item => {
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

    function submitForm() {
        if (!form) return;

        const blogId = form.querySelector('input[name="blog_id"]')?.value;
        const isEdit = Boolean(blogId);
        const formData = new FormData(form);
        const url = isEdit ? config.routes.update.replace('__ID__', blogId) : config.routes.store;

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
            .catch((error) => handleErrors(error, form));
    }

    function submitTagForm() {
        const tagForm = document.getElementById('blogTagForm');
        if (!tagForm) return;

        const formData = new FormData(tagForm);
        clearErrors(tagForm);

        axios.post(config.routes.tagsStore, formData, {
            headers: { 'X-CSRF-TOKEN': csrfToken },
        })
            .then(({ data }) => {
                tagModal?.hide();
                showToast(config.messages.tagCreated);
                const tag = data?.data?.tag;
                if (tag) {
                    ensureTagsLoaded([tag]);
                    tagsSelect.val([...(tagsSelect.val() || []), String(tag.id)]).trigger('change');
                }
            })
            .catch((error) => handleErrors(error, tagForm));
    }

    function ensureTagsLoaded(newTags) {
        const incoming = Array.isArray(newTags) ? newTags : [];
        incoming.forEach(tag => {
            if (!tag) {
                return;
            }
            if (!tagsCache.find(existing => existing.id === tag.id)) {
                tagsCache.push(tag);
                const option = new Option(localizedText(tag.title_translations, tag.title), tag.id, false, false);
                tagsSelect.append(option);
            }
        });
    }

    function ensureAuthorLoaded(author) {
        if (!author || !authorSelect.length) return;
        const exists = Array.from(authorSelect[0].options).some(option => Number(option.value) === Number(author.id));
        if (!exists) {
            const option = new Option(author.name ?? author.text ?? '-', author.id, false, false);
            authorSelect.append(option);
        }
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

    function populateViewModal(data) {
        if (!viewModalEl) return;

        const title = localizedText(data.title_translations, data.title);
        const shortDescription = localizedText(data.short_description_translations, data.short_description);
        const description = localizedText(data.description_translations, data.description);

        setViewText('image', data.image_url);
        const imageEl = viewModalEl.querySelector('[data-blog-view="image"]');
        if (imageEl) {
            imageEl.src = data.image_url || '';
            imageEl.alt = title ?? '';
        }

        setViewText('title', title);
        setViewText('short_description', shortDescription);
        setViewText('description', description);
        setViewText('status', data.status_label ?? data.status);
        setViewText('state', data.state_label ?? (data.is_deleted ? config.states?.archived : config.states?.active));
        setViewText('author', data.author ? `${data.author.name ?? ''} ${data.author.email ? '— ' + data.author.email : ''}` : '—');
        setViewText('created_at', formatDate(data.created_at));
        setViewText('updated_at', formatDate(data.updated_at));

        const tagsContainer = viewModalEl.querySelector('[data-blog-view="tags"]');
        if (tagsContainer) {
            tagsContainer.innerHTML = '';
            (data.tags || []).forEach(tag => {
                const span = document.createElement('span');
                span.className = 'badge badge-light fw-semibold';
                span.textContent = localizedText(tag.title_translations, tag.title);
                tagsContainer.appendChild(span);
            });
            if ((data.tags || []).length === 0) {
                tagsContainer.innerHTML = '<span class="text-muted">—</span>';
            }
        }

        const galleryContainer = viewModalEl.querySelector('[data-blog-view="gallery"]');
        if (galleryContainer) {
            galleryContainer.innerHTML = '';
            (data.gallery || []).forEach(item => {
                const col = document.createElement('div');
                col.className = 'col-md-3';
                col.innerHTML = `<div class="border rounded p-2 bg-light"><img class="img-fluid rounded" src="${item.image_url}" alt=""></div>`;
                galleryContainer.appendChild(col);
            });
            if ((data.gallery || []).length === 0) {
                galleryContainer.innerHTML = '<div class="text-muted">—</div>';
            }
        }
    }

    function setViewText(field, value) {
        const element = viewModalEl?.querySelector(`[data-blog-view="${field}"]`);
        if (!element) return;
        if (field === 'image') return;
        element.textContent = value ?? '—';
    }

    function syncSelectionState() {
        const checkboxes = tableEl.find('.blog-select');
        const checked = tableEl.find('.blog-select:checked');

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

    function generateToken() {
        if (window.crypto?.randomUUID) {
            return window.crypto.randomUUID();
        }

        return `blog-${Date.now()}-${Math.random().toString(16).slice(2)}`;
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
        const match = field.match(/\.([a-z]{2})$/i);
        return match ? match[1] : null;
    }

    function markLocaleTabInvalid(locale) {
        localeTabs.forEach(tab => {
            if (tab.dataset.locale === locale) {
                tab.classList.add('blog-locale-invalid');
            }
        });
    }

    function resetLocaleTabIndicators() {
        localeTabs.forEach(tab => tab.classList.remove('blog-locale-invalid'));
    }

    function toggleDropzoneMessage() {
        if (!dropzoneMessage) return;
        const fileCount = dropzone ? dropzone.getAcceptedFiles().length + dropzone.getQueuedFiles().length : 0;
        dropzoneMessage.classList.toggle('d-none', fileCount > 0);
    }

    function formatAuthorOption(option) {
        if (!option.id) {
            return option.text;
        }

        return $(`
            <div class="d-flex flex-column">
                <span class="fw-semibold">${option.name ?? option.text}</span>
                <span class="text-muted fs-7">${option.email ?? ''}</span>
            </div>
        `);
    }

    function formatAuthorSelection(option) {
        if (!option.id) {
            return option.text;
        }
        return option.name ? `${option.name}${option.email ? ' — ' + option.email : ''}` : option.text;
    }
});
