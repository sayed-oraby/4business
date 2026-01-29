"use strict";

var KTPostTypes = function () {
    var table;
    var dt;
    var modal;
    var form;
    var submitButton;
    var validator;
    var imageInput;
    var config = window.PostTypeModule;
    var defaultImage = '/metronic/media/svg/files/folder-document.svg';

    var initDatatable = function () {
        dt = $("#types-table").DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: false, // Client-side for small data
            ajax: {
                url: config.routes.index,
                type: 'GET',
            },
            columns: [
                { data: 'name' },
                { data: 'slug' },
                { data: 'status' },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, row) {
                        var name = row.name_translations ? (row.name_translations[config.locale] || row.name_translations.en || row.name) : row.name;
                        var imageUrl = row.image_url || defaultImage;
                        return `
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-50px me-3">
                                    <img src="${imageUrl}" alt="${name}" class="rounded-2" />
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">${name}</span>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    targets: 2,
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
                            <button class="btn btn-icon btn-active-light-primary w-30px h-30px me-3" data-type-action="edit" data-id="${row.id}">
                                <i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                            <button class="btn btn-icon btn-active-light-danger w-30px h-30px" data-type-action="delete" data-id="${row.id}">
                                <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </button>
                        `;
                    },
                },
            ],
        });

        table = dt.$;

        // Search
        $('#type-search').on('keyup', function () {
            dt.search(this.value).draw();
        });
    }

    var handleForm = function () {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'name[en]': {
                        validators: {
                            notEmpty: {
                                message: 'English name is required'
                            }
                        }
                    },
                    'name[ar]': {
                        validators: {
                            notEmpty: {
                                message: 'Arabic name is required'
                            }
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

                        if (!formData.has('status')) {
                            formData.append('status', 0);
                        }

                        axios({
                            method: method,
                            url: url,
                            data: formData,
                        }).then(function (response) {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            Swal.fire({
                                text: response.data.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    modal.hide();
                                    dt.ajax.reload();
                                }
                            });
                        }).catch(function (error) {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            Swal.fire({
                                text: error.response?.data?.message || "Sorry, looks like there are some errors detected, please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        });
                    }
                });
            }
        });
    }

    var setImagePreview = function(imageUrl) {
        var preview = document.getElementById('typeImagePreview');
        if (preview) {
            preview.style.backgroundImage = 'url(' + (imageUrl || defaultImage) + ')';
        }
    }

    var handleActions = function () {
        $(document).on('click', '[data-type-action="open-form"]', function () {
            form.reset();
            form.querySelector('[name="id"]').value = '';
            setImagePreview(defaultImage);
            document.getElementById('typeFormModalTitle').innerText = config.messages.create_title || 'Add Post Type';
            modal.show();
        });

        $(document).on('click', '[data-type-action="edit"]', function () {
            var id = $(this).data('id');
            var row = dt.row($(this).closest('tr')).data();

            form.reset();
            form.querySelector('[name="id"]').value = row.id;
            form.querySelector('[name="name[en]"]').value = row.name_translations ? row.name_translations.en : (row.name.en || '');
            form.querySelector('[name="name[ar]"]').value = row.name_translations ? row.name_translations.ar : (row.name.ar || '');
            form.querySelector('[name="slug"]').value = row.slug;
            form.querySelector('[name="status"]').checked = row.status;
            
            // Set image preview
            setImagePreview(row.image_url || defaultImage);

            document.getElementById('typeFormModalTitle').innerText = config.messages.edit_title || 'Edit Post Type';
            modal.show();
        });

        $(document).on('click', '[data-type-action="delete"]', function () {
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
                                text: error.response?.data?.message || "Error deleting type.",
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
            modal = new bootstrap.Modal(document.querySelector('#typeFormModal'));
            form = document.querySelector('#typeForm');
            submitButton = document.querySelector('#typeFormSubmit');

            initDatatable();
            handleForm();
            handleActions();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTPostTypes.init();
});
