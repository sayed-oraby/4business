import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.OrderStatuses;
    if (!config) return;

    const tableEl = $('#order-statuses-table');
    const modalEl = document.getElementById('orderStatusModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const form = document.getElementById('orderStatusForm');
    const submitBtn = document.querySelector('[data-order-status-action="submit"]');
    const errorAlert = form?.querySelector('[data-order-status-errors]');
    const searchInput = document.getElementById('order-status-search');
    const filterApplyBtn = document.querySelector('[data-filter-apply]');
    const filterResetBtn = document.querySelector('[data-filter-reset]');
    const localeTabs = document.querySelectorAll('[data-order-status-locale-tab]');
    const INVALID_TAB_CLASS = 'order-status-locale-invalid';

    let table;
    let filters = {
        search: '',
    };

    initTable();
    fetchStatuses();
    bindEvents();

    function initTable() {
        table = tableEl.DataTable({
            order: [],
            pageLength: 10,
            columns: [
                { data: 'code' },
                { data: 'title' },
                {
                    data: null,
                    render: (row) => {
                        const flags = [];
                        const flagLabels = config.flags || {};
                        if (row.is_default) flags.push(flagLabels.default || 'Default');
                        if (row.is_final) flags.push(flagLabels.final || 'Final');
                        if (row.is_cancel) flags.push(flagLabels.cancel || 'Cancel');
                        if (row.is_refund) flags.push(flagLabels.refund || 'Refund');
                        return flags.join(' / ') || '—';
                    },
                },
                { data: 'sort_order' },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: (row) => renderActions(row),
                },
            ],
        });
    }

    function bindEvents() {
        document.querySelector('[data-order-status-action="open-form"]')?.addEventListener('click', () => {
            resetForm();
            modal?.show();
        });

        submitBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            submitForm(e);
        });
        
        form?.addEventListener('submit', (e) => {
            e.preventDefault();
            submitForm(e);
        });

        tableEl.on('click', '[data-action="edit"]', function () {
            const data = table.row($(this).closest('tr')).data();
            if (!data) return;
            fillForm(data);
            modal?.show();
        });

        tableEl.on('click', '[data-action="delete"]', function () {
            const data = table.row($(this).closest('tr')).data();
            if (!data) return;
            confirmDelete().then((ok) => {
                if (!ok) return;
                deleteStatus(data.id);
            });
        });

        searchInput?.addEventListener('keyup', debounce(() => {
            filters.search = searchInput.value;
            fetchStatuses();
        }, 300));

        filterApplyBtn?.addEventListener('click', () => {
            filters.search = searchInput?.value || '';
            fetchStatuses();
            bootstrap.Offcanvas.getInstance(document.getElementById('orderStatusFiltersCanvas'))?.hide();
        });

        filterResetBtn?.addEventListener('click', () => {
            filters.search = '';
            if (searchInput) searchInput.value = '';
            fetchStatuses();
            bootstrap.Offcanvas.getInstance(document.getElementById('orderStatusFiltersCanvas'))?.hide();
        });
    }

    function fetchStatuses() {
        let list = [];
        axios.get(config.routes.data)
            .then(({ data }) => {
                list = data?.data?.statuses ?? [];
                if (filters.search) {
                    const searchLower = filters.search.toLowerCase();
                    list = list.filter(item => 
                        item.code?.toLowerCase().includes(searchLower) ||
                        item.title?.toLowerCase().includes(searchLower)
                    );
                }
                table.clear().rows.add(list).draw();
            });
    }

    function submitForm(e) {
        if (e) e.preventDefault();
        if (!form) return;
        clearErrors(errorAlert);
        toggleSubmitButton(true);
        const payload = getFormPayload();
        const statusId = payload.status_id;
        const isEdit = Boolean(statusId);
        const url = isEdit ? config.routes.update.replace('__ID__', statusId) : config.routes.store;
        const method = isEdit ? 'put' : 'post';

        axios({ method, url, data: payload })
            .then(() => {
                modal?.hide();
                resetForm();
                fetchStatuses();
                toggleSubmitButton(false);
            })
            .catch((error) => {
                handleErrors(error, errorAlert);
                toggleSubmitButton(false);
            });
    }

    function deleteStatus(id) {
        const url = config.routes.destroy.replace('__ID__', id);
        axios.delete(url).then(() => fetchStatuses());
    }

    function getFormPayload() {
        const payload = {};
        
        // Get all form inputs
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            const name = input.name;
            if (!name) return;
            
            // Handle checkboxes - convert to boolean
            if (input.type === 'checkbox') {
                payload[name] = input.checked;
                return;
            }
            
            // Handle multilingual title fields
            if (name.startsWith('title[') && name.endsWith(']')) {
                const locale = name.match(/title\[(.+)\]/)?.[1];
                if (locale) {
                    if (!payload.title) {
                        payload.title = {};
                    }
                    payload.title[locale] = input.value || '';
                }
                return;
            }
            
            // Handle regular fields
            if (input.value !== '') {
                payload[name] = input.value;
            }
        });
        
        // Ensure title object exists even if empty
        if (!payload.title) {
            payload.title = {};
        }
        
        return payload;
    }

    function resetForm() {
        if (!form) return;
        form.reset();
        form.querySelector('input[name="status_id"]').value = '';
        form.querySelector('input[name="_method"]').value = 'POST';
        const title = modalEl?.querySelector('[data-order-status-form-title]');
        if (title) title.textContent = config.labels.create;
        clearErrors(errorAlert);
    }

    function fillForm(data) {
        if (!form) return;
        form.code.value = data.code ?? '';
        
        // Handle multilingual title
        const titleTranslations = data.title_translations || {};
        if (typeof data.title === 'object' && data.title !== null) {
            Object.keys(data.title).forEach(locale => {
                const input = form.querySelector(`input[name="title[${locale}]"]`);
                if (input) {
                    input.value = data.title[locale] || '';
                }
            });
        } else if (titleTranslations) {
            Object.keys(titleTranslations).forEach(locale => {
                const input = form.querySelector(`input[name="title[${locale}]"]`);
                if (input) {
                    input.value = titleTranslations[locale] || '';
                }
            });
        }
        
        form.color.value = data.color ?? '';
        form.sort_order.value = data.sort_order ?? 0;
        form.is_default.checked = Boolean(data.is_default);
        form.is_final.checked = Boolean(data.is_final);
        form.is_cancel.checked = Boolean(data.is_cancel);
        form.is_refund.checked = Boolean(data.is_refund);
        form.querySelector('input[name="status_id"]').value = data.id;
        form.querySelector('input[name="_method"]').value = 'PUT';
        const title = modalEl?.querySelector('[data-order-status-form-title]');
        if (title) title.textContent = config.labels.edit;
    }

    function icon(name) {
        return `<i class="ki-outline ${name} fs-2"></i>`;
    }

    function renderActions(row) {
        const actions = [];
        if (config.can?.update) {
            actions.push(`
                <button class="btn btn-sm btn-icon btn-light-primary" data-action="edit" title="${config.labels?.edit || 'Edit'}" data-id="${row.id}">
                    ${icon('ki-pencil')}
                </button>
            `);
        }
        if (config.can?.delete) {
            actions.push(`
                <button class="btn btn-sm btn-icon btn-light-danger" data-action="delete" title="${config.labels?.delete || 'Delete'}" data-id="${row.id}">
                    ${icon('ki-trash')}
                </button>
            `);
        }
        return `<div class="d-flex justify-content-center gap-2">${actions.join('')}</div>`;
    }

    function handleErrors(error, alertBox) {
        if (!form) return;
        const root = form;
        const summaryMessages = new Set();

        if (error.response?.status === 422 && error.response?.data?.errors) {
            Object.entries(error.response.data.errors).forEach(([field, messages]) => {
                const firstMessage = Array.isArray(messages) ? messages[0] : messages;
                summaryMessages.add(firstMessage);
                
                const inputName = field.replace(/\./g, '[').replace(/(\[en\]|\[ar\])$/, ']$1');
                const input = root.querySelector(`[name="${field}"]`) || root.querySelector(`[name="${inputName}"]`);
                let errorElement = root.querySelector(`[data-error-for="${field}"]`);
                
                if (input) {
                    input.classList.add('is-invalid');
                }
                
                if (errorElement) {
                    errorElement.textContent = firstMessage;
                    errorElement.style.display = 'block';
                } else if (input) {
                    let feedback = input.parentNode.querySelector('.invalid-feedback');
                    if (!feedback) {
                        feedback = document.createElement('div');
                        feedback.classList.add('invalid-feedback');
                        input.parentNode.appendChild(feedback);
                    }
                    feedback.textContent = firstMessage;
                }

                const locale = field.match(/title\.(\w+)$/)?.[1];
                if (locale) {
                    markLocaleTabInvalid(locale);
                }
            });

            if (alertBox && summaryMessages.size > 0) {
                alertBox.innerHTML = '';
                const list = document.createElement('ul');
                summaryMessages.forEach(message => {
                    const li = document.createElement('li');
                    li.textContent = message;
                    list.appendChild(li);
                });
                alertBox.appendChild(list);
                alertBox.classList.remove('d-none');
            }
        } else if (alertBox) {
            const message = error.response?.data?.message || 'An error occurred';
            alertBox.innerHTML = `<ul><li>${message}</li></ul>`;
            alertBox.classList.remove('d-none');
        }
    }

    function clearErrors(alertBox) {
        if (!form) return;
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('[data-error-for]').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });
        if (alertBox) {
            alertBox.classList.add('d-none');
            alertBox.innerHTML = '';
        }
        resetLocaleTabIndicators();
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

    function toggleSubmitButton(loading) {
        if (!submitBtn) return;
        if (loading) {
            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;
        } else {
            submitBtn.setAttribute('data-kt-indicator', 'off');
            submitBtn.disabled = false;
        }
    }

    function debounce(fn, delay) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn(...args), delay);
        };
    }

    function confirmDelete() {
        if (window.Swal) {
            return Swal.fire({
                text: config.labels.delete ?? 'Delete?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: config.labels.delete ?? 'Delete',
                cancelButtonText: config.labels.cancel ?? 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-light',
                },
                buttonsStyling: false,
            }).then(result => result.isConfirmed);
        }
        return Promise.resolve(confirm(config.labels.delete ?? 'Delete?'));
    }
});
