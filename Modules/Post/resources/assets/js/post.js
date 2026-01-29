"use strict";

var KTPosts = function () {
    var table;
    var dt;
    var config = window.PostModule;
    var filterForm = document.getElementById('post-filter-form');
    var filterApplyBtn = document.querySelector('[data-filter-apply]');
    var filterResetBtn = document.querySelector('[data-filter-reset]');

    var getFilters = function () {
        if (!filterForm) {
            return {};
        }

        return {
            status: filterForm.status?.value || '',
            category_id: filterForm.category_id?.value || '',
            post_type_id: filterForm.post_type_id?.value || '',
            package_id: filterForm.package_id?.value || '',
            is_paid: filterForm.is_paid?.value || '',
            gender: filterForm.gender?.value || '',
            city_id: filterForm.city_id?.value || '',
            date_from: filterForm.date_from?.value || '',
            date_to: filterForm.date_to?.value || '',
        };
    }

    var initDatatable = function () {
        dt = $("#posts-table").DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: config.routes.index,
                type: 'GET',
                data: function (d) {
                    // Add filters to request
                    var filters = getFilters();
                    $.extend(d, filters);
                }
            },
            columns: [
                { data: null },  // title - rendered in columnDefs
                { data: null },  // user.name - rendered in columnDefs  
                { data: null },  // post_type.name - rendered in columnDefs
                { data: null },  // package - rendered in columnDefs
                { data: 'status' },
                { data: 'created_at' },
                { data: null },  // actions
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, row) {
                        var title = row.title[config.locale] || row.title.en || row.title;
                        var coverHtml = '';

                        // Add cover image if available
                        if (row.cover_image_url) {
                            coverHtml = `
                                <div class="symbol symbol-50px symbol-2by3 me-3">
                                    <div class="symbol-label" style="background-image: url('${row.cover_image_url}'); background-size: cover; background-position: center;"></div>
                                </div>
                            `;
                        } else {
                            coverHtml = `
                                <div class="symbol symbol-50px symbol-2by3 me-3">
                                    <div class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-briefcase fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                            `;
                        }

                        return `
                            <div class="d-flex align-items-center">
                                ${coverHtml}
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-gray-900">${title}</span>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    targets: 1,
                    render: function (data, type, row) {
                        return row.user?.name || '-';
                    }
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        if (!row.post_type) return '-';
                        return row.post_type.name[config.locale] || row.post_type.name.en || '-';
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        var packageTitle = '-';
                        if (row.package && row.package.title) {
                            packageTitle = row.package.title[config.locale] || row.package.title.en || row.package.title;
                        }

                        // Determine if package is paid (has price > 0)
                        var isPaidPackage = row.package && parseFloat(row.package.price || 0) > 0;

                        // Payment badge - shows paid/unpaid/free status
                        var paymentBadge = '';
                        if (isPaidPackage) {
                            if (row.is_paid) {
                                paymentBadge = `<span class="badge badge-success ms-2">${config.labels.paid}</span>`;
                            } else {
                                paymentBadge = `<span class="badge badge-danger ms-2">${config.labels.unpaid}</span>`;
                            }
                        } else {
                            paymentBadge = `<span class="badge badge-light-primary ms-2">${config.labels.free}</span>`;
                        }

                        return packageTitle + paymentBadge;
                    }
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        var statusClass = {
                            'pending': 'badge-light-warning',
                            'approved': 'badge-light-success',
                            'rejected': 'badge-light-danger',
                            'expired': 'badge-light-secondary',
                            'awaiting_payment': 'badge-light-info',
                            'payment_failed': 'badge-light-danger',
                            'active': 'badge-light-success',
                            'inactive': 'badge-light-secondary',
                        }[row.status] || 'badge-light-secondary';
                        var statusText = config.statuses[row.status] || row.status;
                        return `<span class="badge ${statusClass}">${statusText}</span>`;
                    }
                },
                {
                    targets: 5,
                    render: function (data) {
                        return moment(data).format('YYYY-MM-DD');
                    }
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <a href="${config.routes.show.replace('__ID__', row.id)}" class="btn btn-icon btn-active-light-primary w-30px h-30px me-3">
                                <i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            </a>
                            <button class="btn btn-icon btn-active-light-danger w-30px h-30px" data-post-action="delete" data-id="${row.id}">
                                <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </button>
                        `;
                    },
                },
            ],
        });

        table = dt.$;

        // Search
        $('#post-search').on('keyup', function () {
            dt.search(this.value).draw();
        });
    }

    var handleFilters = function () {
        // Handle Filter Apply
        filterApplyBtn?.addEventListener('click', () => {
            dt.ajax.reload();
            bootstrap.Offcanvas.getInstance(document.getElementById('postFiltersCanvas'))?.hide();
        });

        // Handle Filter Reset
        filterResetBtn?.addEventListener('click', () => {
            filterForm?.reset();
            dt.ajax.reload();
        });
    }

    var handleActions = function () {
        // Delete
        $(document).on('click', '[data-post-action="delete"]', function () {
            var id = $(this).data('id');

            Swal.fire({
                text: config.confirm.deleteMessage,
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: config.confirm.confirm,
                cancelButtonText: config.confirm.cancel,
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    axios.delete(config.routes.destroy.replace('__ID__', id))
                        .then(function (response) {
                            Swal.fire({
                                text: response.data.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function () {
                                dt.ajax.reload();
                            });
                        })
                        .catch(function (error) {
                            Swal.fire({
                                text: error.response?.data?.message || "Error deleting post.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        });
                }
            });
        });
    }

    return {
        init: function () {
            initDatatable();
            handleFilters();
            handleActions();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTPosts.init();
});
