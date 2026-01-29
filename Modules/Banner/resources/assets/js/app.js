import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.BannerModule || {};
    const tableEl = $('#banner-table');

    if (!tableEl.length) {
        return;
    }

    const filters = {
        placement: '',
        status: '',
    };

    const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const modalElement = document.getElementById('bannerFormModal');
    const modal = modalElement ? new bootstrap.Modal(modalElement) : null;
    const form = document.getElementById('bannerForm');
    const localeTabs = document.querySelectorAll('[data-banner-locale-tab]');

    const table = tableEl.DataTable({
        processing: true,
        serverSide: true,
        ajax: (data, callback) => {
            axios.get(config.routes.data, {
                params: {
                    draw: data.draw,
                    start: data.start,
                    length: data.length,
                    'search[value]': data.search.value,
                    'order[0][column]': data.order?.[0]?.column,
                    'order[0][dir]': data.order?.[0]?.dir,
                    placement: filters.placement,
                    status: filters.status,
                },
            })
                .then((response) => callback(response.data))
                .catch(() => callback({ data: [], recordsTotal: 0, recordsFiltered: 0, draw: data.draw }));
        },
        columns: [
            {
                data: 'image_url',
                orderable: false,
                render: (value, type, row) => `
                    <div class="d-flex align-items-center">
                        <img src="${value || ''}" class="banner-thumb me-3" alt="${row.title ?? ''}">
                    </div>
                `,
            },
            {
                data: 'title',
                render: (value, type, row) => `
                    <div class="fw-bold text-gray-900">${value ?? '—'}</div>
                    <div class="text-muted fs-7">${row.description ?? '—'}</div>
                `,
            },
            // {
            //     data: 'placement_label',
            //     render: (value) => `<span class="badge badge-light fw-semibold text-uppercase">${value}</span>`,
            // },
            {
                data: 'status_label',
                render: (value, type, row) => {
                    const badge = row.status === 'active' ? 'badge-light-success' : 'badge-light-secondary';
                    return `<span class="badge ${badge}">${value}</span>`;
                },
            },
            // {
            //     data: 'schedule',
            //     render: (schedule) => {
            //         if (!schedule) {
            //             return '—';
            //         }

            //         const start = schedule.starts_at ? new Date(schedule.starts_at).toLocaleString() : '—';
            //         const end = schedule.ends_at ? new Date(schedule.ends_at).toLocaleString() : '—';

            //         return `
            //             <div class="text-muted fs-7">
            //                 <div>${config.i18n.startsAt}: ${start}</div>
            //                 <div>${config.i18n.endsAt}: ${end}</div>
            //             </div>
            //         `;
            //     },
            // },
            {
                data: null,
                orderable: false,
                className: 'text-end',
                render: (row) => `
                    <button class="btn btn-sm btn-icon btn-light-primary me-2" data-banner-action="edit" data-id="${row.id}">
                        <i class="ki-duotone ki-pencil fs-2"></i>
                    </button>
                    <button class="btn btn-sm btn-icon btn-light-danger" data-banner-action="delete" data-id="${row.id}">
                        <i class="ki-duotone ki-trash fs-2"></i>
                    </button>
                `,
            },
        ],
    });

    $('#banner-search').on('keyup', function () {
        table.search(this.value).draw();
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

    document.querySelectorAll('[data-banner-action="open-form"]').forEach((btn) => {
        btn.addEventListener('click', () => {
            resetForm();
            modal?.show();
        });
    });

    tableEl.on('click', '[data-banner-action="edit"]', function () {
        const id = this.getAttribute('data-id');
        const data = table.row($(this).closest('tr')).data();

        if (!data) {
            return;
        }

        fillForm(data);
        modal?.show();
    });

    tableEl.on('click', '[data-banner-action="delete"]', function () {
        const id = this.getAttribute('data-id');
        if (!id) return;

        if (!confirm(config.i18n.confirmDelete)) {
            return;
        }

        axios.post(config.routes.destroy.replace('__ID__', id), {
            _method: 'DELETE',
        }, {
            headers: { 'X-CSRF-TOKEN': csrfToken },
        })
            .then(() => {
                showToast(config.messages.deleted);
                table.ajax.reload(null, false);
            })
            .catch(() => showToast('Error', true));
    });

    form?.addEventListener('submit', (event) => {
        event.preventDefault();
        submitForm();
    });

    function resetForm() {
        form?.reset();
        form?.querySelector('input[name="banner_id"]').value = '';
        form?.querySelector('input[name="_method"]').value = 'POST';
        clearErrors();
    }

    function fillForm(banner) {
        resetForm();
        form.querySelector('input[name="banner_id"]').value = banner.id;
        form.querySelector('input[name="_method"]').value = 'PUT';

        setValue('input[name="button_label"]', banner.button?.label ?? '');
        setValue('input[name="button_url"]', banner.button?.url ?? '');
        setValue('select[name="placement"]', banner.placement ?? '');
        setValue('select[name="status"]', banner.status ?? '');
        // setValue('input[name="starts_at"]', banner.schedule?.starts_at ? banner.schedule.starts_at.replace('Z', '') : '');
        // setValue('input[name="ends_at"]', banner.schedule?.ends_at ? banner.schedule.ends_at.replace('Z', '') : '');
        setValue('input[name="sort_order"]', banner.sort_order ?? 0);

        Object.entries(banner.title_translations ?? {}).forEach(([locale, value]) => {
            setValue(`input[name="title[${locale}]"]`, value);
        });

        Object.entries(banner.description_translations ?? {}).forEach(([locale, value]) => {
            const field = form.querySelector(`textarea[name="description[${locale}]"]`);
            if (field) field.value = value ?? '';
        });
    }

    function setValue(selector, value) {
        const field = form.querySelector(selector);
        if (field) field.value = value ?? '';
    }

    function submitForm() {
        const bannerId = form.querySelector('input[name="banner_id"]').value;
        const isEdit = Boolean(bannerId);
        const formData = new FormData(form);
        const url = isEdit ? config.routes.update.replace('__ID__', bannerId) : config.routes.store;

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
        form.querySelectorAll('.is-invalid').forEach((input) => input.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach((feedback) => feedback.remove());
    }

    function handleErrors(error) {
        if (error.response?.data?.errors) {
            const errors = error.response.data.errors;
            Object.entries(errors).forEach(([field, messages]) => {
                const input = form.querySelector(`[name="${field}"]`);
                if (!input) return;
                input.classList.add('is-invalid');
                const span = document.createElement('div');
                span.classList.add('invalid-feedback');
                span.textContent = messages.join(' ');
                input.parentNode.appendChild(span);
            });
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

    localeTabs.forEach((tab) => {
        tab.addEventListener('shown.bs.tab', () => {
            // placeholder for future behaviour
        });
    });
});
