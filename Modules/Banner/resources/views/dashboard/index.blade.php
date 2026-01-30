@extends('layouts.dashboard.master')

@section('title', __('banner::banner.title'))
@section('page-title', __('banner::banner.title'))

@push('styles')
<style>
    .banner-hero {
        background: linear-gradient(135deg, #eff4ff 0%, #ffffff 100%);
        border-radius: 1.5rem;
    }

    .banner-table-card .table thead tr {
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .04em;
    }

    .banner-table-card .table thead th,
    .banner-table-card .table tbody td {
        text-align: center !important;
        vertical-align: middle;
    }

    .banner-thumb {
        width: 120px;
        height: 60px;
        object-fit: cover;
        border-radius: 0.65rem;
    }
</style>
@endpush

@php($availableLocales = available_locales())

@section('content')
    <div class="card border-0 shadow-sm mb-10 banner-hero">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-7">
                <div>
                    <span class="badge badge-light-primary mb-3">{{ __('dashboard.content') }}</span>
                    <h2 class="fw-bold text-gray-900 mb-2">{{ __('banner::banner.title') }}</h2>
                    <p class="text-gray-600 mb-0">{{ __('banner::banner.description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-flush shadow-sm mb-10 banner-table-card">
        <div class="card-header align-items-center border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-1">{{ __('banner::banner.title') }}</h3>
            </div>
            <div class="card-toolbar gap-3">
                <div class="position-relative my-1">
                    <span class="svg-icon svg-icon-2 position-absolute top-50 translate-middle-y ms-4">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control form-control-solid ps-12" id="banner-search" placeholder="{{ __('banner::banner.search_placeholder') }}">
                </div>
                {{-- <button class="btn btn-light btn-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#bannerFiltersCanvas">
                    <i class="ki-duotone ki-filter fs-2"></i>{{ __('dashboard.filter_button') }}
                </button> --}}
                @can('banners.create')
                    <button class="btn btn-primary btn-flex align-items-center gap-2" data-banner-action="open-form">
                        <i class="ki-duotone ki-plus fs-2"></i>{{ __('banner::banner.actions.create') }}
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4" id="banner-table">
                    <thead class="bg-transparent text-gray-500 fw-semibold">
                        <tr class="text-center align-middle">
                            <th class="w-60px text-center">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="select-all-banners">
                                </div>
                            </th>
                            <th class="w-150px text-center">{{ __('banner::banner.table.image') }}</th>
                            <th class="text-center">{{ __('banner::banner.table.title') }}</th>
                            {{-- <th class="text-center">{{ __('banner::banner.table.placement') }}</th> --}}
                            <th class="text-center">{{ __('banner::banner.table.status') }}</th>
                            {{-- <th class="text-center">{{ __('banner::banner.table.schedule') }}</th> --}}
                            <th class="text-center pe-4 min-w-150px">{{ __('banner::banner.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        @can('banners.delete')
            <div class="card-footer d-flex justify-content-between flex-wrap gap-3">
                <div class="text-muted">{{ __('banner::banner.actions.bulk_delete') }}</div>
                <button class="btn btn-light-danger" id="bulk-delete-btn" disabled>
                    <i class="ki-duotone ki-trash fs-2 me-2"></i>{{ __('banner::banner.actions.bulk_delete') }}
                </button>
            </div>
        @endcan
    </div>

    {{-- <div class="offcanvas offcanvas-end" tabindex="-1" id="bannerFiltersCanvas">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-bold">{{ __('dashboard.filters_panel.title') }}</h5>
            <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="offcanvas">
                <i class="ki-duotone ki-cross fs-2"></i>
            </button>
        </div>
        <div class="offcanvas-body pt-5">
            <form id="banner-filter-form" class="d-flex flex-column gap-5">
                <div>
                    <label class="form-label">{{ __('banner::banner.filters.placement') }}</label>
                    <select class="form-select form-select-solid" name="placement" data-banner-filter="placement">
                        <option value="">{{ __('banner::banner.filters.placement') }}</option>
                        @foreach($placements as $key => $placement)
                            <option value="{{ $key }}">{{ __($placement['label']) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('banner::banner.filters.status') }}</label>
                    <select class="form-select form-select-solid" name="status" data-banner-filter="status">
                        <option value="">{{ __('banner::banner.filters.status') }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ __('banner::banner.statuses.' . $status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('banner::banner.filters.state.active') }}</label>
                    <select class="form-select form-select-solid" name="state" data-banner-filter="state">
                        <option value="active">{{ __('banner::banner.filters.state.active') }}</option>
                        <option value="archived">{{ __('banner::banner.filters.state.archived') }}</option>
                        <option value="all">{{ __('banner::banner.filters.state.all') }}</option>
                    </select>
                </div>
                <div class="d-flex gap-3">
                    <button class="btn btn-light flex-grow-1" type="button" data-filter-reset>
                        <i class="ki-duotone ki-arrows-circle fs-2 me-2"></i>{{ __('dashboard.filters_panel.reset') }}
                    </button>
                    <button class="btn btn-primary flex-grow-1" type="button" data-filter-apply>
                        <i class="ki-duotone ki-check fs-2 me-2"></i>{{ __('dashboard.filters_panel.apply') }}
                    </button>
                </div>
            </form>
        </div>
    </div> --}}

    @include('banner::dashboard.partials.form', ['availableLocales' => $availableLocales])
    @include('banner::dashboard.partials.view-modal')
@endsection

@push('scripts')
    <script>
        window.BannerModule = {
            routes: {
                data: "{{ route('dashboard.banners.data') }}",
                store: "{{ route('dashboard.banners.store') }}",
                update: "{{ route('dashboard.banners.update', ['banner' => '__ID__']) }}",
                destroy: "{{ route('dashboard.banners.destroy', ['banner' => '__ID__']) }}",
                bulkDestroy: "{{ route('dashboard.banners.bulk-destroy') }}",
            },
            can: {
                update: @json(auth('admin')->user()?->can('banners.update')),
                delete: @json(auth('admin')->user()?->can('banners.delete')),
            },
            placements: @json(collect($placements)->mapWithKeys(fn($placement, $key) => [$key => __($placement['label'])])),
            statuses: @json($statuses),
            statusLabels: @json(collect($statuses)->mapWithKeys(fn($status) => [$status => __('banner::banner.statuses.' . $status)])),
            messages: {
                created: "{{ __('banner::banner.messages.created') }}",
                updated: "{{ __('banner::banner.messages.updated') }}",
                deleted: "{{ __('banner::banner.messages.deleted') }}",
                bulkDeleted: "{{ __('banner::banner.messages.bulk_deleted') }}",
            },
            states: {
                active: "{{ __('banner::banner.states.active') }}",
                archived: "{{ __('banner::banner.states.archived') }}",
            },
            i18n: {
                view: "{{ __('banner::banner.actions.view') }}",
                edit: "{{ __('banner::banner.actions.edit') }}",
                delete: "{{ __('banner::banner.actions.delete') }}",
            },
            confirm: {
                deleteTitle: "{{ __('banner::banner.actions.delete') }}",
                deleteMessage: "{{ __('banner::banner.actions.confirm_delete') }}",
                bulkTitle: "{{ __('banner::banner.actions.bulk_delete') }}",
                bulkMessage: "{{ __('banner::banner.actions.bulk_delete_confirm') }}",
                confirm: "{{ __('banner::banner.actions.confirm') }}",
                cancel: "{{ __('banner::banner.actions.cancel') }}",
            },
            view: {
                title: "{{ __('banner::banner.view.title') }}",
                fields: @json(__('banner::banner.view.fields')),
                startsAt: "{{ __('banner::banner.view.starts_at') }}",
                endsAt: "{{ __('banner::banner.view.ends_at') }}",
            },
            locale: "{{ app()->getLocale() }}",
        };
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var config = window.BannerModule || {};
            var tableEl = $('#banner-table');

            if (!tableEl.length) {
                return;
            }

            var locale = config.locale || document.documentElement.lang || 'en';
            var form = document.getElementById('bannerForm');
            var modalEl = document.getElementById('bannerFormModal');
            var modal = modalEl ? new bootstrap.Modal(modalEl) : null;
            var viewModalEl = document.getElementById('bannerViewModal');
            var viewModal = viewModalEl ? new bootstrap.Modal(viewModalEl) : null;
            var bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            var selectAllCheckbox = document.getElementById('select-all-banners');
            var csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;

            var viewFields = viewModalEl ? {
                image: viewModalEl.querySelector('[data-banner-view="image"]'),
                title: viewModalEl.querySelector('[data-banner-view="title"]'),
                description: viewModalEl.querySelector('[data-banner-view="description"]'),
                placement: viewModalEl.querySelector('[data-banner-view="placement"]'),
                status: viewModalEl.querySelector('[data-banner-view="status"]'),
                state: viewModalEl.querySelector('[data-banner-view="state"]'),
                button_label: viewModalEl.querySelector('[data-banner-view="button_label"]'),
                button_url: viewModalEl.querySelector('[data-banner-view="button_url"]'),
                created_at: viewModalEl.querySelector('[data-banner-view="created_at"]'),
                updated_at: viewModalEl.querySelector('[data-banner-view="updated_at"]'),
            } : {};

            var filters = {
                placement: '',
                status: '',
                state: 'active',
            };

            var selectedIds = new Set();

            var table = tableEl.DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: function(data, callback) {
                    var params = {
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

                    axios.get(config.routes.data, { params: params })
                        .then(function(response) { callback(response.data); })
                        .catch(function() { callback({ data: [], recordsTotal: 0, recordsFiltered: 0, draw: data.draw }); });
                },
                columnDefs: [
                    { targets: 0, orderable: false, className: 'text-center align-middle' },
                    { targets: -1, orderable: false, className: 'text-center align-middle' },
                ],
                columns: [
                    {
                        data: 'id',
                        render: function(id) {
                            return '<div class="form-check form-check-sm form-check-custom"><input class="form-check-input banner-select" type="checkbox" value="' + id + '"></div>';
                        },
                    },
                    {
                        data: 'image_url',
                        render: function(value, type, row) {
                            return '<div class="d-flex align-items-center"><img src="' + (value || '') + '" class="banner-thumb me-3" alt="' + (row.title || '') + '"></div>';
                        },
                    },
                    {
                        data: 'title',
                        render: function(value, type, row) {
                            var title = localizedText(row.title_translations, row.title);
                            var description = localizedText(row.description_translations, row.description);
                            return '<div class="d-flex flex-column"><span class="fw-bold text-gray-900">' + (title || '—') + '</span><span class="text-muted fs-7">' + (description || '—') + '</span></div>';
                        },
                    },
                    {
                        data: 'status_label',
                        render: function(value, type, row) {
                            if (row.is_deleted) {
                                return '<span class="badge badge-light-danger">' + (config.states?.archived || value) + '</span>';
                            }
                            var badgeClass = row.status === 'active' ? 'badge-light-success' : 'badge-light-secondary';
                            return '<span class="badge ' + badgeClass + '">' + value + '</span>';
                        },
                    },
                    {
                        data: null,
                        render: function(row) {
                            return '<div class="d-flex justify-content-center gap-2">' +
                                '<button class="btn btn-sm btn-icon btn-light-primary" data-banner-action="edit" title="' + config.i18n.edit + '" data-id="' + row.id + '">' + icon('ki-pencil') + '</button>' +
                                '<button class="btn btn-sm btn-icon btn-light-danger" data-banner-action="delete" title="' + config.i18n.delete + '" data-id="' + row.id + '">' + icon('ki-trash') + '</button>' +
                                '</div>';
                        },
                    },
                ],
                drawCallback: function() {
                    tableEl.find('.banner-select').each(function() {
                        this.checked = selectedIds.has(this.value);
                    });
                    syncSelectionState();
                },
            });

            var searchInput = document.getElementById('banner-search');
            if (searchInput) {
                searchInput.addEventListener('keyup', function(event) {
                    table.search(event.target.value).draw();
                });
            }

            document.querySelectorAll('[data-banner-filter="placement"]').forEach(function(el) {
                el.addEventListener('change', function(event) {
                    filters.placement = event.target.value;
                    table.ajax.reload();
                });
            });

            document.querySelectorAll('[data-banner-filter="status"]').forEach(function(el) {
                el.addEventListener('change', function(event) {
                    filters.status = event.target.value;
                    table.ajax.reload();
                });
            });

            document.querySelectorAll('[data-banner-filter="state"]').forEach(function(el) {
                el.addEventListener('change', function(event) {
                    filters.state = event.target.value || 'active';
                    table.ajax.reload();
                });
            });

            document.querySelectorAll('[data-banner-action="open-form"]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    resetForm();
                    if (modal) modal.show();
                });
            });

            tableEl.on('click', '[data-banner-action="view"]', function() {
                var data = table.row($(this).closest('tr')).data();
                if (!data) return;
                populateViewModal(data);
                if (viewModal) viewModal.show();
            });

            tableEl.on('click', '[data-banner-action="edit"]', function() {
                var data = table.row($(this).closest('tr')).data();
                if (!data) return;
                fillForm(data);
                if (modal) modal.show();
            });

            tableEl.on('click', '[data-banner-action="delete"]', function() {
                var id = this.getAttribute('data-id');
                if (!id) return;

                confirmAction('single').then(function(confirmed) {
                    if (!confirmed) return;

                    axios.post(config.routes.destroy.replace('__ID__', id), {
                        _method: 'DELETE',
                    }, {
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                    })
                        .then(function() {
                            showToast(config.messages.deleted);
                            selectedIds.delete(id);
                            table.ajax.reload(null, false);
                            syncSelectionState();
                        })
                        .catch(function() { showToast('Error', true); });
                });
            });

            tableEl.on('change', '.banner-select', function() {
                var id = String(this.value);
                if (!id) return;

                if (this.checked) {
                    selectedIds.add(id);
                } else {
                    selectedIds.delete(id);
                }

                syncSelectionState();
            });

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    var checked = selectAllCheckbox.checked;
                    tableEl.find('.banner-select').each(function() {
                        this.checked = checked;
                        var id = String(this.value);
                        if (!id) return;

                        if (checked) {
                            selectedIds.add(id);
                        } else {
                            selectedIds.delete(id);
                        }
                    });
                    syncSelectionState();
                });
            }

            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function() {
                    if (selectedIds.size === 0) return;

                    confirmAction('bulk', selectedIds.size).then(function(confirmed) {
                        if (!confirmed) return;

                        axios.post(config.routes.bulkDestroy, {
                            ids: Array.from(selectedIds),
                            _method: 'DELETE',
                        }, {
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                        })
                            .then(function() {
                                showToast(config.messages.bulkDeleted);
                                selectedIds.clear();
                                table.ajax.reload();
                                syncSelectionState();
                            })
                            .catch(function() { showToast('Error', true); });
                    });
                });
            }

            if (form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    submitForm();
                });
            }

            function resetForm() {
                if (!form) return;
                form.reset();
                var idInput = form.querySelector('input[name="banner_id"]');
                if (idInput) idInput.value = '';
                var methodInput = form.querySelector('input[name="_method"]');
                if (methodInput) methodInput.value = 'POST';

                var imagePreviewContainer = form.querySelector('#banner-image-preview-container');
                var imagePreview = form.querySelector('#banner-image-preview');
                if (imagePreviewContainer) imagePreviewContainer.classList.add('d-none');
                if (imagePreview) imagePreview.src = '';

                clearErrors();
            }

            function fillForm(banner) {
                if (!form) return;
                resetForm();
                var idInput = form.querySelector('input[name="banner_id"]');
                var methodInput = form.querySelector('input[name="_method"]');
                if (idInput) idInput.value = banner.id;
                if (methodInput) methodInput.value = 'PUT';

                setValue('input[name="button_label"]', banner.button?.label || '');
                setValue('input[name="button_url"]', banner.button?.url || '');
                setValue('select[name="placement"]', banner.placement || '');
                setValue('select[name="status"]', banner.status || '');
                setValue('input[name="sort_order"]', banner.sort_order || 0);

                var titleTranslations = banner.title_translations || {};
                Object.keys(titleTranslations).forEach(function(loc) {
                    setValue('input[name="title[' + loc + ']"]', titleTranslations[loc]);
                });

                var descriptionTranslations = banner.description_translations || {};
                Object.keys(descriptionTranslations).forEach(function(loc) {
                    var field = form.querySelector('textarea[name="description[' + loc + ']"]');
                    if (field) field.value = descriptionTranslations[loc] || '';
                });

                var imagePreviewContainer = form.querySelector('#banner-image-preview-container');
                var imagePreview = form.querySelector('#banner-image-preview');
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
                var field = form.querySelector(selector);
                if (field) field.value = value || '';
            }

            function submitForm() {
                var bannerId = form.querySelector('input[name="banner_id"]').value;
                var isEdit = Boolean(bannerId);
                var formData = new FormData(form);
                var url = isEdit ? config.routes.update.replace('__ID__', bannerId) : config.routes.store;
                var submitBtn = form.querySelector('[data-banner-action="submit-form"]');

                if (!isEdit) {
                    formData.set('_method', 'POST');
                }

                clearErrors();

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
                    .then(function() {
                        if (modal) modal.hide();
                        table.ajax.reload(null, false);
                        showToast(isEdit ? config.messages.updated : config.messages.created);
                    })
                    .catch(function(error) { handleErrors(error); })
                    .finally(function() {
                        if (submitBtn) {
                            submitBtn.removeAttribute('data-kt-indicator');
                            submitBtn.disabled = false;
                        }
                    });
            }

            function clearErrors() {
                if (!form) return;
                form.querySelectorAll('.is-invalid').forEach(function(input) { input.classList.remove('is-invalid'); });
                form.querySelectorAll('.invalid-feedback').forEach(function(feedback) { feedback.remove(); });
                form.querySelectorAll('[data-error-for]').forEach(function(el) {
                    el.textContent = '';
                    el.style.display = 'none';
                });
                var errorSummary = form.querySelector('[data-banner-errors]');
                if (errorSummary) {
                    errorSummary.classList.add('d-none');
                }
            }

            function handleErrors(error) {
                if (!form) return;

                var summaryMessages = new Set();
                var errorSummary = form.querySelector('[data-banner-errors]');

                if (error.response?.status === 422 && error.response?.data?.errors) {
                    var errors = error.response.data.errors;
                    Object.keys(errors).forEach(function(field) {
                        var messages = errors[field];
                        var firstMessage = Array.isArray(messages) ? messages[0] : messages;
                        summaryMessages.add(firstMessage);

                        var inputName = normalizeFieldName(field);
                        var input = form.querySelector('[name="' + inputName + '"]');
                        var errorElement = form.querySelector('[data-error-for="' + field + '"]');
                        if (!errorElement) {
                            errorElement = form.querySelector('[data-error-for="' + inputName + '"]');
                        }

                        if (input) {
                            input.classList.add('is-invalid');
                        }

                        if (errorElement) {
                            errorElement.textContent = firstMessage;
                            errorElement.style.display = 'block';
                        } else if (input) {
                            var feedback = input.parentNode.querySelector('.invalid-feedback');
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
                        var messagesArray = Array.from(summaryMessages);
                        errorSummary.innerHTML = '<ul class="mb-0">' + messagesArray.map(function(msg) { return '<li>' + msg + '</li>'; }).join('') + '</ul>';
                    }
                } else {
                    showToast('Error', true);
                }
            }

            function showToast(message, isError) {
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
                    viewFields.image.alt = data.title || '';
                }

                setViewText('title', localizedText(data.title_translations, data.title));
                setViewText('description', localizedText(data.description_translations, data.description));
                setViewText('placement', data.placement_label || data.placement);
                setViewText('status', data.status_label || data.status);
                setViewText('state', data.state_label || (data.is_deleted ? config.states?.archived : config.states?.active));
                setViewText('button_label', data.button?.label);
                if (viewFields.button_url) {
                    viewFields.button_url.textContent = data.button?.url || '—';
                    viewFields.button_url.href = data.button?.url || '#';
                }
                setViewText('created_at', formatDate(data.created_at));
                setViewText('updated_at', formatDate(data.updated_at));
            }

            function setViewText(field, value) {
                var element = viewFields[field];
                if (!element) return;
                element.textContent = value || '—';
            }

            function formatDate(value) {
                if (!value) {
                    return '—';
                }
                var date = new Date(value);
                if (isNaN(date.getTime())) {
                    return value;
                }
                return date.toLocaleString();
            }

            function confirmAction(type, count) {
                count = count || 1;
                if (!window.Swal) {
                    return Promise.resolve(confirm(config.i18n.confirmDelete));
                }

                var translations = config.confirm || {};
                var title = type === 'bulk' ? translations.bulkTitle : translations.deleteTitle;
                var message = type === 'bulk' ? translations.bulkMessage : translations.deleteMessage;

                if (type === 'bulk' && message) {
                    message = message.replace(':count', count);
                }

                return Swal.fire({
                    title: title,
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
                }).then(function(result) { return result.isConfirmed; });
            }

            function syncSelectionState() {
                var checkboxes = tableEl.find('.banner-select');
                var checked = tableEl.find('.banner-select:checked');

                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = checked.length > 0 && checked.length === checkboxes.length;
                    selectAllCheckbox.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
                }

                if (bulkDeleteBtn) {
                    bulkDeleteBtn.disabled = selectedIds.size === 0;
                }
            }

            function localizedText(translations, fallback) {
                translations = translations || {};
                if (translations && typeof translations === 'object') {
                    if (translations[locale]) {
                        return translations[locale];
                    }

                    var fallbackLocale = document.documentElement.dataset.fallbackLocale || 'en';
                    if (translations[fallbackLocale]) {
                        return translations[fallbackLocale];
                    }

                    var values = Object.values(translations);
                    var first = values.find(function(v) { return Boolean(v); });
                    if (first) {
                        return first;
                    }
                }

                return fallback || null;
            }

            function icon(name) {
                return '<i class="ki-outline ' + name + ' fs-2"></i>';
            }
        });
    </script>
@endpush

