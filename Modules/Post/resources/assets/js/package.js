"use strict";

var KTPackages = function () {
    var table;
    var dt;
    var modal;
    var form;
    var submitButton;
    var validator;
    var config = window.PackageModule;

    var initDatatable = function () {
        dt = $("#packages-table").DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: false,
            ajax: {
                url: config.routes.index,
                type: 'GET',
            },
            columns: [
                { data: 'title' },
                { data: 'price' },
                { data: 'period_days' },
                { data: 'top_days' },
                { data: 'is_free' },
                { data: 'status' },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, row) {
                        var title = row.title_translations ? (row.title_translations[config.locale] || row.title_translations.en || row.title) : row.title;
                        var badge = '';
                        if (row.is_free) {
                            badge = '<span class="badge badge-light-success ms-2">' + config.labels.free + '</span>';
                        }
                        return `<div class="d-flex align-items-center">
                            <div class="me-3" style="width: 8px; height: 40px; background: ${row.label_color || '#3b82f6'}; border-radius: 4px;"></div>
                            <div>
                                <span class="fw-bold">${title}</span>${badge}
                                ${row.is_featured ? '<i class="ki-duotone ki-star fs-6 text-warning ms-1"><span class="path1"></span><span class="path2"></span></i>' : ''}
                            </div>
                        </div>`;
                    }
                },
                {
                    targets: 1,
                    render: function (data, type, row) {
                        if (row.is_free) {
                            return '<span class="badge badge-light-success">' + config.labels.free + '</span>';
                        }
                        return row.price + ' KWD';
                    }
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        return row.period_days + ' ' + config.labels.days;
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        if (row.top_days > 0) {
                            return `<span class="badge badge-light-primary">${row.top_days} ${config.labels.days_top}</span>`;
                        }
                        return '-';
                    }
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        if (row.is_free) {
                            return `<span class="badge badge-light-success">${row.free_credits_per_user || 0} ${config.labels.credits}</span>`;
                        }
                        return '<span class="badge badge-light-info">' + config.labels.paid + '</span>';
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        var statusClass = row.status ? 'badge-light-success' : 'badge-light-danger';
                        var statusText = row.status_label || (row.status ? 'Active' : 'Inactive');
                        return `<span class="badge ${statusClass}">${statusText}</span>`;
                    }
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-icon btn-active-light-primary w-30px h-30px me-3" data-package-action="edit" data-id="${row.id}">
                                <i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                            <button class="btn btn-icon btn-active-light-danger w-30px h-30px" data-package-action="delete" data-id="${row.id}">
                                <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </button>
                        `;
                    },
                },
            ],
        });

        table = dt.$;

        $('#package-search').on('keyup', function () {
            dt.search(this.value).draw();
        });
    }

    var initFormInteractions = function() {
        // Toggle free package fields
        var isFreeCheckbox = document.getElementById('isFreePackage');
        var freeCreditsSection = document.getElementById('freeCreditsSection');
        var priceSection = document.getElementById('priceSection');

        function toggleFreeFields() {
            var isFree = isFreeCheckbox.checked;
            if (freeCreditsSection) {
                freeCreditsSection.style.display = isFree ? 'block' : 'none';
            }
            if (priceSection) {
                priceSection.style.display = isFree ? 'none' : 'block';
            }
            
            if (isFree) {
                form.querySelector('[name="price"]').value = 0;
            }
        }

        if (isFreeCheckbox) {
            isFreeCheckbox.addEventListener('change', toggleFreeFields);
            // Initial state
            toggleFreeFields();
        }

        // Calculate free days
        var periodDays = document.getElementById('periodDays');
        var topDays = document.getElementById('topDays');
        var freeDays = document.getElementById('freeDays');

        function updateFreeDays() {
            var period = parseInt(periodDays.value) || 0;
            var top = parseInt(topDays.value) || 0;
            freeDays.value = Math.max(0, period - top);
        }

        periodDays.addEventListener('input', updateFreeDays);
        topDays.addEventListener('input', function() {
            var period = parseInt(periodDays.value) || 0;
            var top = parseInt(topDays.value) || 0;
            if (top > period) {
                topDays.value = period;
            }
            updateFreeDays();
        });

        // Color picker sync
        var labelColor = form.querySelector('[name="label_color"]');
        var labelColorText = document.getElementById('labelColorText');
        var cardColor = form.querySelector('[name="card_color"]');
        var cardColorText = document.getElementById('cardColorText');
        var preview = document.getElementById('packagePreview');
        var previewLabel = document.getElementById('previewLabel');

        function updatePreview() {
            preview.style.backgroundColor = cardColor.value;
            preview.style.borderColor = labelColor.value;
            previewLabel.style.backgroundColor = labelColor.value;
        }

        labelColor.addEventListener('input', function() {
            labelColorText.value = this.value;
            updatePreview();
        });

        labelColorText.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                labelColor.value = this.value;
                updatePreview();
            }
        });

        cardColor.addEventListener('input', function() {
            cardColorText.value = this.value;
            updatePreview();
        });

        cardColorText.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                cardColor.value = this.value;
                updatePreview();
            }
        });
    }

    var handleForm = function () {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'title[en]': {
                        validators: {
                            notEmpty: { message: 'English title is required' }
                        }
                    },
                    'title[ar]': {
                        validators: {
                            notEmpty: { message: 'Arabic title is required' }
                        }
                    },
                    'price': {
                        validators: {
                            notEmpty: { message: 'Price is required' },
                            numeric: { message: 'Price must be a number' }
                        }
                    },
                    'period_days': {
                        validators: {
                            notEmpty: { message: 'Period is required' },
                            integer: { message: 'Period must be an integer' }
                        }
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );

        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            if (validator) {
                validator.validate().then(function (status) {
                    if (status == 'Valid') {
                        submitButton.setAttribute('data-kt-indicator', 'on');
                        submitButton.disabled = true;

                        var formData = new FormData(form);
                        var id = formData.get('id');
                        var url = id ? config.routes.update.replace('__ID__', id) : config.routes.store;
                        var method = id ? 'PUT' : 'POST';

                        if (id) {
                            formData.append('_method', 'PUT');
                            method = 'POST';
                        }

                        // Handle checkboxes
                        if (!formData.has('status')) formData.append('status', 0);
                        if (!formData.has('is_featured')) formData.append('is_featured', 0);
                        if (!formData.has('is_free')) formData.append('is_free', 0);

                        axios({
                            method: method,
                            url: url,
                            data: formData,
                            headers: { 'Content-Type': 'multipart/form-data' }
                        }).then(function (response) {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            Swal.fire({
                                text: response.data.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok!",
                                customClass: { confirmButton: "btn btn-primary" }
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    modal.hide();
                                    dt.ajax.reload();
                                }
                            });
                        }).catch(function (error) {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            var errorMsg = error.response?.data?.message || "Sorry, looks like there are some errors detected, please try again.";
                            
                            // Handle validation errors
                            if (error.response?.data?.errors) {
                                var errors = error.response.data.errors;
                                errorMsg = Object.values(errors).flat().join('<br>');
                            }

                            Swal.fire({
                                html: errorMsg,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok!",
                                customClass: { confirmButton: "btn btn-primary" }
                            });
                        });
                    }
                });
            }
        });
    }

    var resetForm = function() {
        form.reset();
        form.querySelector('[name="id"]').value = '';
        form.querySelector('[name="label_color"]').value = '#3b82f6';
        form.querySelector('[name="card_color"]').value = '#eff6ff';
        document.getElementById('labelColorText').value = '#3b82f6';
        document.getElementById('cardColorText').value = '#eff6ff';
        document.getElementById('freeDays').value = '';
        
        // Reset free package fields
        var isFreeCheckbox = document.getElementById('isFreePackage');
        if (isFreeCheckbox) isFreeCheckbox.checked = false;
        
        var freeCreditsSection = document.getElementById('freeCreditsSection');
        if (freeCreditsSection) freeCreditsSection.style.display = 'none';
        
        var priceSection = document.getElementById('priceSection');
        if (priceSection) priceSection.style.display = 'block';
        
        // Update preview
        var preview = document.getElementById('packagePreview');
        var previewLabel = document.getElementById('previewLabel');
        if (preview && previewLabel) {
            preview.style.backgroundColor = '#eff6ff';
            preview.style.borderColor = '#3b82f6';
            previewLabel.style.backgroundColor = '#3b82f6';
        }
    }

    var handleActions = function () {
        $(document).on('click', '[data-package-action="open-form"]', function () {
            resetForm();
            document.getElementById('packageFormModalTitle').innerText = config.messages.create_title || 'Add Package';
            modal.show();
        });

        $(document).on('click', '[data-package-action="edit"]', function () {
            var id = $(this).data('id');
            var row = dt.row($(this).closest('tr')).data();

            resetForm();
            form.querySelector('[name="id"]').value = row.id;
            form.querySelector('[name="title[en]"]').value = row.title_translations?.en || '';
            form.querySelector('[name="title[ar]"]').value = row.title_translations?.ar || '';
            form.querySelector('[name="description[en]"]').value = row.description_translations?.en || '';
            form.querySelector('[name="description[ar]"]').value = row.description_translations?.ar || '';
            form.querySelector('[name="price"]').value = row.price;
            form.querySelector('[name="period_days"]').value = row.period_days;
            form.querySelector('[name="top_days"]').value = row.top_days || 0;
            document.getElementById('freeDays').value = row.free_days || 0;
            
            // Colors
            var labelColor = row.label_color || '#3b82f6';
            var cardColor = row.card_color || '#eff6ff';
            form.querySelector('[name="label_color"]').value = labelColor;
            form.querySelector('[name="card_color"]').value = cardColor;
            document.getElementById('labelColorText').value = labelColor;
            document.getElementById('cardColorText').value = cardColor;
            
            // Update preview
            var preview = document.getElementById('packagePreview');
            var previewLabel = document.getElementById('previewLabel');
            preview.style.backgroundColor = cardColor;
            preview.style.borderColor = labelColor;
            previewLabel.style.backgroundColor = labelColor;

            form.querySelector('[name="status"]').checked = row.status;
            form.querySelector('[name="is_featured"]').checked = row.is_featured;
            
            // Free package
            var isFreeCheckbox = document.getElementById('isFreePackage');
            if (isFreeCheckbox) {
                isFreeCheckbox.checked = row.is_free;
            }
            
            var freeCreditsSection = document.getElementById('freeCreditsSection');
            if (freeCreditsSection) {
                freeCreditsSection.style.display = row.is_free ? 'block' : 'none';
            }
            
            var priceSection = document.getElementById('priceSection');
            if (priceSection) {
                priceSection.style.display = row.is_free ? 'none' : 'block';
            }
            
            if (row.free_credits_per_user) {
                form.querySelector('[name="free_credits_per_user"]').value = row.free_credits_per_user;
            }

            document.getElementById('packageFormModalTitle').innerText = config.messages.edit_title || 'Edit Package';
            modal.show();
        });

        $(document).on('click', '[data-package-action="delete"]', function () {
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
                                confirmButtonText: "Ok!",
                                customClass: { confirmButton: "btn fw-bold btn-primary" }
                            }).then(function () {
                                dt.ajax.reload();
                            });
                        })
                        .catch(function (error) {
                            Swal.fire({
                                text: error.response?.data?.message || "Error deleting package.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok!",
                                customClass: { confirmButton: "btn fw-bold btn-primary" }
                            });
                        });
                }
            });
        });
    }

    return {
        init: function () {
            modal = new bootstrap.Modal(document.querySelector('#packageFormModal'));
            form = document.querySelector('#packageForm');
            submitButton = document.querySelector('#packageFormSubmit');

            initDatatable();
            initFormInteractions();
            handleForm();
            handleActions();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTPackages.init();
});
