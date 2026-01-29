import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.OrderDashboard;
    if (!config) return;

    const tableEl = $('#orders-table');
    const searchInput = document.getElementById('orders-search');
    const statusFilter = document.getElementById('orders-status-filter');
    const paymentFilter = document.getElementById('orders-payment-filter');
    const filterApplyBtn = document.querySelector('[data-filter-apply]');
    const filterResetBtn = document.querySelector('[data-filter-reset]');

    let table;
    let filters = {
        search: '',
        status: '',
        payment_status: '',
    };

    initTable();
    bindEvents();
    fetchOrders();

    function initTable() {
        table = tableEl.DataTable({
            order: [],
            pageLength: 10,
            columns: [
                { data: 'id' },
                {
                    data: 'user_id',
                    render: (value, type, row) => row.user_id ? `#${row.user_id}` : (row.guest_uuid ?? 'Guest'),
                },
                {
                    data: 'grand_total',
                    render: (value, type, row) => `${row.grand_total} ${row.currency}`,
                },
                {
                    data: 'status',
                    render: (value, type, row) => {
                        const color = row.status_color ?? '#f6f8fb';
                        // Use status from API which should already be localized
                        const label = row.status ?? '—';
                        return `<span class="badge rounded-1" style="background:${color};color:#111;">${label}</span>`;
                    },
                },
                {
                    data: null,
                    render: (data, type, row) => {
                        // Use payment_status_label (translated) if available, otherwise fallback to payment_status
                        const label = row.payment_status_label || row.payment_status || '—';
                        return `<span class="badge badge-light-info">${label}</span>`;
                    },
                },
                { data: 'created_at' },
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
        searchInput?.addEventListener('keyup', debounce(() => {
            filters.search = searchInput.value;
            fetchOrders();
        }, 300));

        statusFilter?.addEventListener('change', () => {
            filters.status = statusFilter.value;
        });

        paymentFilter?.addEventListener('change', () => {
            filters.payment_status = paymentFilter.value;
        });

        filterApplyBtn?.addEventListener('click', () => {
            filters.search = searchInput?.value || '';
            filters.status = statusFilter?.value || '';
            filters.payment_status = paymentFilter?.value || '';
            fetchOrders();
            bootstrap.Offcanvas.getInstance(document.getElementById('orderFiltersCanvas'))?.hide();
        });

        filterResetBtn?.addEventListener('click', () => {
            filters.search = '';
            filters.status = '';
            filters.payment_status = '';
            if (searchInput) searchInput.value = '';
            if (statusFilter) statusFilter.value = '';
            if (paymentFilter) paymentFilter.value = '';
            fetchOrders();
            bootstrap.Offcanvas.getInstance(document.getElementById('orderFiltersCanvas'))?.hide();
        });
    }

    function fetchOrders() {
        axios.get(config.routes.data, { params: filters })
            .then(({ data }) => {
                const collection = data?.data?.orders?.data ?? [];
                table.clear().rows.add(collection).draw();
            });
    }

    function icon(name) {
        return `<i class="ki-outline ${name} fs-2"></i>`;
    }

    function renderActions(row) {
        const actions = [];
        const invoice = row.payments?.[0]?.invoice_url;
        const statuses = config.labels?.statuses ?? [];

        // View button
        actions.push(`
            <a class="btn btn-sm btn-icon btn-light-primary" href="${config.routes?.show?.replace('__ID__', row.id) || `/dashboard/orders/${row.id}`}" title="${config.labels?.view || 'View'}">
                ${icon('ki-eye')}
            </a>
        `);

        if (invoice) {
            actions.push(`
                <a class="btn btn-sm btn-icon btn-light-info" href="${invoice}" target="_blank" rel="noopener" title="${config.labels?.viewInvoice || 'View Invoice'}">
                    ${icon('ki-link')}
                </a>
            `);
        }

        if (config.can?.update) {
            const options = statuses.map((s) => `
                <option value="${s.id}" ${row.order_status_id === s.id ? 'selected' : ''}>${s.title}</option>
            `).join('');

            actions.push(`
                <select class="form-select form-select-sm w-auto order-status-select" data-order-id="${row.id}" title="${config.labels.changeStatus}">
                    ${options}
                </select>
            `);
        }

        return actions.length
            ? `<div class="d-flex justify-content-center gap-2">${actions.join('')}</div>`
            : '—';
    }

    function debounce(fn, delay) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn(...args), delay);
        };
    }

    tableEl.on('change', '.order-status-select', function () {
        const orderId = this.dataset.orderId;
        const statusId = this.value;

        if (!orderId || !statusId) return;

        axios.post(config.routes.changeStatus.replace('__ID__', orderId), {
            status_id: statusId,
        }).then(() => {
            fetchOrders();
            notify('success', config.messages?.statusChanged ?? 'Status updated');
        }).catch((error) => {
            console.error(error);
            notify('error', error.response?.data?.message ?? config.messages?.statusChangeFailed ?? 'Unable to update status');
        });
    });

    function notify(type, message) {
        if (window.toastr) {
            toastr[type](message);
            return;
        }

        alert(message);
    }
});
