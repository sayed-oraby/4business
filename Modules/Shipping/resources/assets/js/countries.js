import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.ShippingCountries;
    if (!config) {
        return;
    }

    const tableEl = $('#shipping-countries-table');
    const modalEl = document.getElementById('shippingCountryModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const form = document.getElementById('shippingCountryForm');
    const submitBtn = document.querySelector('[data-shipping-country-action="submit"]');
    const errorAlert = form?.querySelector('[data-shipping-country-errors]');
    const searchInput = document.getElementById('shipping-country-search');
    const filterSelect = document.getElementById('shipping-country-shipping-filter');
    const packageSelect = $('[data-shipping-country-package]');

    const ratesModalEl = document.getElementById('shippingCountryRatesModal');
    const ratesModal = ratesModalEl ? new bootstrap.Modal(ratesModalEl) : null;
    const ratesList = ratesModalEl?.querySelector('[data-shipping-country-rates-list]');
    const ratesEmpty = ratesModalEl?.querySelector('[data-shipping-country-rates-empty]');
    const ratesCountryLabel = ratesModalEl?.querySelector('[data-shipping-country-rates-country]');
    const ratesForm = document.getElementById('shippingCountryRateForm');
    const ratesSubmitBtn = document.querySelector('[data-shipping-country-rate-action="submit"]');
    const ratesErrorAlert = ratesForm?.querySelector('[data-shipping-country-rate-errors]');
    const stateSelect = ratesForm?.querySelector('[data-shipping-rate-state]');
    const citySelect = ratesForm?.querySelector('[data-shipping-rate-city]');

    let table;
    let filters = {
        search: '',
        shipping_enabled: '',
    };

    let currentCountryId = null;
    let currentCountryName = '';
    let statesCache = [];

    let packageCountries = [];

    initTable();
    fetchCountries();
    bindEvents();
    initPackageSelect();

    function initTable() {
        table = tableEl.DataTable({
            order: [],
            pageLength: 10,
            columns: [
                {
                    data: 'iso2',
                    render: (value, type, row) => {
                        let flag = '';
                        if (row.flag_url) {
                            flag = `<img src="${row.flag_url}" alt="${value ?? ''}" class="shipping-country-flag me-3">`;
                        } else if (row.flag) {
                            flag = `<span class="fs-2 me-3">${row.flag}</span>`;
                        }

                        return `
                            <div class="d-flex align-items-center">
                                ${flag}
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">${value ?? ''}</span>
                                    <span class="text-muted fs-8">${row.iso3 ?? ''}</span>
                                </div>
                            </div>
                        `;
                    },
                },
                {
                    data: 'name_en',
                    render: (value, type, row) => `
                        <div class="d-flex flex-column">
                            <span class="fw-semibold">${value ?? ''}</span>
                            <span class="text-muted fs-8">${row.phone_code ?? ''}</span>
                        </div>
                    `,
                },
                { data: 'name_ar' },
                {
                    data: 'phone_code',
                    render: (value) => value ?? '—',
                },
                {
                    data: 'is_active',
                    render: (value) => badge(value),
                },
                {
                    data: 'is_shipping_enabled',
                    render: (value) => badge(value, true),
                },
                { data: 'sort_order' },
                {
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: (row) => renderActions(row),
                },
            ],
        });
    }

    function fetchCountries() {
        axios.get(config.routes.data, { params: filters })
            .then(({ data }) => {
                const list = data?.data?.countries ?? [];
                table.clear().rows.add(list).draw();
            });
    }

    function bindEvents() {
        submitBtn?.addEventListener('click', submitCountryForm);

        document.querySelector('[data-shipping-country-action="open-form"]')?.addEventListener('click', () => {
            resetCountryForm();
            modal?.show();
        });

        searchInput?.addEventListener('keyup', (event) => {
            filters.search = event.target.value;
            debounceFetch();
        });

        filterSelect?.addEventListener('change', (event) => {
            filters.shipping_enabled = event.target.value;
            fetchCountries();
        });

        tableEl.on('click', '[data-action="edit"]', function () {
            const data = table.row($(this).closest('tr')).data();
            if (data) {
                fillCountryForm(data);
                modal?.show();
            }
        });

        tableEl.on('click', '[data-action="delete"]', function () {
            const data = table.row($(this).closest('tr')).data();
            if (data) {
                confirmDelete().then((accepted) => {
                    if (!accepted) return;
                    removeCountry(data.id);
                });
            }
        });

        tableEl.on('click', '[data-action="import"]', function () {
            const data = table.row($(this).closest('tr')).data();
            if (data) {
                importLocations(data.id);
            }
        });

        tableEl.on('click', '[data-action="rates"]', function () {
            const data = table.row($(this).closest('tr')).data();
            if (data) {
                currentCountryId = data.id;
                currentCountryName = data.name_en;
                if (ratesCountryLabel) {
                    ratesCountryLabel.textContent = `${data.name_en} (${data.iso2})`;
                }
                resetRateForm();
                loadStatesForCountry().finally(() => {
                    fetchRates();
                    ratesModal?.show();
                });
            }
        });

        ratesSubmitBtn?.addEventListener('click', submitRateForm);
        stateSelect?.addEventListener('change', () => {
            populateCitySelect(stateSelect.value, '');
        });

        ratesList?.addEventListener('click', (event) => {
            const btn = event.target.closest('button[data-rate-action]');
            if (!btn) return;
            const rateId = btn.dataset.rateId;
            if (btn.dataset.rateAction === 'edit') {
                const payload = JSON.parse(btn.closest('tr').dataset.ratePayload || '{}');
                fillRateForm(payload);
            } else if (btn.dataset.rateAction === 'delete') {
                confirmDelete().then((ok) => {
                    if (!ok) return;
                    deleteRate(rateId);
                });
            }
        });
    }

    function initPackageSelect() {
        if (!packageSelect.length) return;
        const selectParent = $('#shippingCountryModal');

        const initializeSelect = (options = []) => {
            if (packageSelect.data('select2')) {
                packageSelect.select2('destroy');
            }
            packageSelect.empty();
            options.forEach(option => {
                const newOption = new Option(option.text, option.id, false, false);
                packageSelect.append(newOption);
            });
            packageSelect.select2({
                dropdownParent: selectParent,
                placeholder: packageSelect.data('placeholder') || '',
                allowClear: true,
            });
        };

        initializeSelect();

        axios.get(config.routes.package)
            .then(({ data }) => {
                packageCountries = data?.data?.countries ?? [];
                const options = packageCountries.map(country => ({
                    id: country.iso2,
                    text: `${country.flag ?? ''} ${country.name_en} (${country.iso2})`.trim(),
                }));
                initializeSelect(options);
            })
            .catch(() => initializeSelect());

        packageSelect.on('change', function () {
            const iso2 = this.value;
            const found = packageCountries.find(country => country.iso2 === iso2);
            if (found) {
                form.iso2.value = found.iso2 ?? '';
                form.iso3.value = found.iso3 ?? '';
                form.name_en.value = found.name_en ?? '';
                form.name_ar.value = found.name_ar ?? '';
                form.phone_code.value = found.phone_code ?? '';
                form.flag_svg.value = found.flag_url ?? found.flag ?? '';
            }
        });
    }

    let searchTimeout;
    function debounceFetch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchCountries(), 300);
    }

    function submitCountryForm() {
        if (!form) return;
        clearErrors(errorAlert);

        const payload = {
            iso2: form.iso2.value.trim().toUpperCase(),
            iso3: form.iso3.value.trim().toUpperCase(),
            phone_code: form.phone_code.value.trim(),
            name_en: form.name_en.value.trim(),
            name_ar: form.name_ar.value.trim(),
            flag_svg: form.flag_svg.value.trim(),
            sort_order: Number(form.sort_order.value) || 0,
            is_active: form.is_active.checked ? 1 : 0,
            is_shipping_enabled: form.is_shipping_enabled.checked ? 1 : 0,
        };

        const countryId = form.querySelector('input[name="country_id"]').value;
        const isEdit = Boolean(countryId);
        const url = isEdit
            ? config.routes.update.replace('__ID__', countryId)
            : config.routes.store;
        const method = isEdit ? 'put' : 'post';

        toggleButton(submitBtn, true);

        axios({ method, url, data: payload })
            .then(() => {
                modal?.hide();
                resetCountryForm();
                fetchCountries();
                toast(isEdit ? config.labels.edit : config.labels.create);
            })
            .catch((error) => handleErrors(error, errorAlert))
            .finally(() => toggleButton(submitBtn, false));
    }

    function removeCountry(id) {
        const url = config.routes.destroy.replace('__ID__', id);
        axios.delete(url)
            .then(() => {
                fetchCountries();
                toast(config.labels.delete);
            })
            .catch(() => toast('Error', true));
    }

    function resetCountryForm() {
        if (!form) return;
        form.reset();
        form.querySelector('input[name="country_id"]').value = '';
        form.querySelector('input[name="_method"]').value = 'POST';
        form.is_active.checked = true;
        form.is_shipping_enabled.checked = true;
        clearErrors(errorAlert);
        const title = modalEl?.querySelector('[data-shipping-country-form-title]');
        if (title) {
            title.textContent = config.labels.formTitleCreate;
        }
        if (packageSelect.length) {
            packageSelect.val(null).trigger('change.select2');
        }
    }

    function fillCountryForm(data) {
        if (!form) return;
        form.iso2.value = data.iso2 ?? '';
        form.iso3.value = data.iso3 ?? '';
        form.phone_code.value = data.phone_code ?? '';
        form.name_en.value = data.name_en ?? '';
        form.name_ar.value = data.name_ar ?? '';
        form.flag_svg.value = data.flag ?? '';
        form.sort_order.value = data.sort_order ?? 0;
        form.is_active.checked = Boolean(data.is_active);
        form.is_shipping_enabled.checked = Boolean(data.is_shipping_enabled);
        form.querySelector('input[name="country_id"]').value = data.id;
        form.querySelector('input[name="_method"]').value = 'PUT';
        const title = modalEl?.querySelector('[data-shipping-country-form-title]');
        if (title) {
            title.textContent = config.labels.formTitleEdit;
        }
        if (packageSelect.length) {
            packageSelect.val(data.iso2 ?? null).trigger('change.select2');
        }
    }

    function fetchRates() {
        if (!currentCountryId) return;
        const url = config.routes.rates.index.replace('__ID__', currentCountryId);
        axios.get(url).then(({ data }) => {
            const rates = data?.data?.rates ?? [];
            renderRates(rates);
        });
    }

    function renderRates(rates) {
        if (!ratesList) return;
        ratesList.innerHTML = '';
        ratesEmpty?.classList.toggle('d-none', rates.length > 0);

        rates.forEach((rate) => {
            const tr = document.createElement('tr');
            tr.dataset.ratePayload = JSON.stringify(rate);
            tr.innerHTML = `
                <td><span class="fw-bold">${rate.calculation_type}</span></td>
                <td>${rate.base_price}</td>
                <td>${rate.price_per_kg ?? '—'}</td>
                <td>${rate.free_shipping_over ?? '—'}</td>
                <td>${rate.currency}</td>
                <td>${formatRateScope(rate)}</td>
                <td>${badge(rate.is_active)}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-sm btn-icon btn-light-primary" data-rate-action="edit" data-rate-id="${rate.id}" title="Edit">
                            ${icon('ki-pencil')}
                        </button>
                        <button class="btn btn-sm btn-icon btn-light-danger" data-rate-action="delete" data-rate-id="${rate.id}" title="Delete">
                            ${icon('ki-trash')}
                        </button>
                    </div>
                </td>
            `;
            ratesList.appendChild(tr);
        });
    }

    function submitRateForm() {
        if (!ratesForm || !currentCountryId) return;
        clearErrors(ratesErrorAlert);

        const payload = {
            calculation_type: ratesForm.calculation_type.value,
            base_price: ratesForm.base_price.value || 0,
            price_per_kg: ratesForm.price_per_kg.value || null,
            free_shipping_over: ratesForm.free_shipping_over.value || null,
            currency: ratesForm.currency.value,
            delivery_estimate_en: ratesForm.delivery_estimate_en.value,
            delivery_estimate_ar: ratesForm.delivery_estimate_ar.value,
            shipping_state_id: ratesForm.shipping_state_id.value || null,
            shipping_city_id: ratesForm.shipping_city_id.value || null,
            is_active: ratesForm.is_active.checked ? 1 : 0,
        };

        const rateId = ratesForm.querySelector('input[name="rate_id"]').value;
        const isEdit = Boolean(rateId);
        const url = isEdit
            ? config.routes.rates.update.replace('__ID__', rateId)
            : config.routes.rates.store.replace('__ID__', currentCountryId);
        const method = isEdit ? 'put' : 'post';

        toggleButton(ratesSubmitBtn, true);

        axios({ method, url, data: payload })
            .then(() => {
                resetRateForm();
                fetchRates();
                toast(config.rateLabels?.saved ?? 'Saved');
            })
            .catch((error) => handleErrors(error, ratesErrorAlert))
            .finally(() => toggleButton(ratesSubmitBtn, false));
    }

    function resetRateForm() {
        if (!ratesForm) return;
        ratesForm.reset();
        ratesForm.currency.value = 'KWD';
        ratesForm.querySelector('input[name="rate_id"]').value = '';
        ratesForm.querySelector('input[name="_method"]').value = 'POST';
        ratesForm.is_active.checked = true;
        clearErrors(ratesErrorAlert);
    }

    function fillRateForm(data) {
        if (!ratesForm) return;
        ratesForm.calculation_type.value = data.calculation_type ?? 'flat';
        ratesForm.base_price.value = data.base_price ?? 0;
        ratesForm.price_per_kg.value = data.price_per_kg ?? '';
        ratesForm.free_shipping_over.value = data.free_shipping_over ?? '';
        ratesForm.currency.value = data.currency ?? 'KWD';
        ratesForm.delivery_estimate_en.value = data.delivery_estimate_en ?? '';
        ratesForm.delivery_estimate_ar.value = data.delivery_estimate_ar ?? '';
        ratesForm.is_active.checked = Boolean(data.is_active);
        ratesForm.querySelector('input[name="rate_id"]').value = data.id;
        ratesForm.querySelector('input[name="_method"]').value = 'PUT';
    }

    function deleteRate(rateId) {
        const url = config.routes.rates.destroy.replace('__ID__', rateId);
        axios.delete(url)
            .then(() => {
                fetchRates();
                toast(config.rateLabels?.deleted ?? 'Deleted');
            })
            .catch(() => toast('Error', true));
    }

    function icon(name) {
        return `<i class="ki-outline ${name} fs-2"></i>`;
    }

    function renderActions(row) {
        const actions = [];
        if (config.can?.update) {
            actions.push(`
                <button class="btn btn-sm btn-icon btn-light-info" data-action="import" title="${config.labels.import}" data-id="${row.id}">
                    ${icon('ki-delivery')}
                </button>
            `);
            actions.push(`
                <button class="btn btn-sm btn-icon btn-light-primary" data-action="edit" title="${config.labels.edit}" data-id="${row.id}">
                    ${icon('ki-pencil')}
                </button>
            `);
            actions.push(`
                <button class="btn btn-sm btn-icon btn-light-info" data-action="rates" title="${config.labels.rates}" data-id="${row.id}">
                    ${icon('ki-delivery')}
                </button>
            `);
        }
        if (config.can?.delete) {
            actions.push(`
                <button class="btn btn-sm btn-icon btn-light-danger" data-action="delete" title="${config.labels.delete}" data-id="${row.id}">
                    ${icon('ki-trash')}
                </button>
            `);
        }

        return `<div class="d-flex justify-content-center gap-2">${actions.join('')}</div>`;
    }

    function badge(value, shipping = false) {
        const activeLabel = shipping ? config.labels.shippingEnabled ?? 'Enabled' : config.labels.active ?? 'Active';
        const inactiveLabel = shipping ? config.labels.shippingDisabled ?? 'Disabled' : config.labels.inactive ?? 'Inactive';
        const cls = value ? 'badge-light-success' : 'badge-light-danger';
        return `<span class="badge ${cls}">${value ? activeLabel : inactiveLabel}</span>`;
    }

    function confirmDelete() {
        if (window.Swal) {
            return Swal.fire({
                text: config.labels.confirmDelete ?? 'Are you sure?',
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

        return Promise.resolve(confirm(config.labels.confirmDelete ?? 'Delete?'));
    }

    function importLocations(id) {
        const url = config.routes.import.replace('__ID__', id);
        axios.post(url)
            .then(({ data }) => {
                toast(config.labels.import);
                if (currentCountryId === id) {
                    loadStatesForCountry();
                }
            })
            .catch(() => toast('Error', true));
    }

    function loadStatesForCountry() {
        if (!stateSelect || !currentCountryId) {
            return Promise.resolve();
        }

        stateSelect.innerHTML = `<option value="">${config.rateLabels.scopeCountry}</option>`;
        stateSelect.disabled = true;
        citySelect?.setAttribute('disabled', 'disabled');
        if (citySelect) {
            citySelect.innerHTML = `<option value="">${config.rateLabels.scopeState}</option>`;
        }

        return axios.get(config.routes.states.replace('__ID__', currentCountryId))
            .then(({ data }) => {
                statesCache = data?.data?.states ?? [];
                populateStateSelect();
            })
            .catch(() => {
                statesCache = [];
            })
            .finally(() => {
                stateSelect.disabled = false;
            });
    }

    function populateStateSelect(selectedId = '', selectedCityId = '') {
        if (!stateSelect) return;
        stateSelect.innerHTML = `<option value="">${config.rateLabels.scopeCountry}</option>`;
        statesCache.forEach((state) => {
            const option = document.createElement('option');
            option.value = state.id;
            option.textContent = `${state.name_en} (${state.code})`;
            stateSelect.appendChild(option);
        });
        stateSelect.value = selectedId ? String(selectedId) : '';
        populateCitySelect(stateSelect.value, selectedCityId);
    }

    function populateCitySelect(stateId, selectedCityId = '') {
        if (!citySelect) return;
        if (!stateId) {
            citySelect.innerHTML = `<option value="">${config.rateLabels.scopeCountry}</option>`;
            citySelect.value = '';
            citySelect.disabled = true;
            return;
        }

        const state = statesCache.find((st) => String(st.id) === String(stateId));
        const cities = state?.cities ?? [];
        citySelect.innerHTML = `<option value="">${config.rateLabels.scopeState}</option>`;
        cities.forEach((city) => {
            const option = document.createElement('option');
            option.value = city.id;
            option.textContent = `${city.name_en}${city.code ? ` (${city.code})` : ''}`;
            citySelect.appendChild(option);
        });
        citySelect.disabled = cities.length === 0;
        citySelect.value = selectedCityId ? String(selectedCityId) : '';
    }

    function formatRateScope(rate) {
        if (rate.city) {
            return `${config.rateLabels.scopeCity}: ${rate.city.name_en}`;
        }
        if (rate.state) {
            return `${config.rateLabels.scopeState}: ${rate.state.name_en}`;
        }
        return config.rateLabels.scopeCountry;
    }

    function handleErrors(error, alertBox) {
        if (!alertBox) return;
        const messages = error?.response?.data?.errors || {};
        if (Object.keys(messages).length === 0) {
            alertBox.classList.add('d-none');
            alertBox.innerHTML = '';
            return;
        }

        const list = document.createElement('ul');
        Object.entries(messages).forEach(([field, errs]) => {
            errs.forEach((message) => {
                const li = document.createElement('li');
                li.textContent = message;
                list.appendChild(li);
            });
        });

        alertBox.innerHTML = '';
        alertBox.appendChild(list);
        alertBox.classList.remove('d-none');
    }

    function clearErrors(alertBox) {
        if (!alertBox) return;
        alertBox.classList.add('d-none');
        alertBox.innerHTML = '';
    }

    function toggleButton(button, isLoading) {
        if (!button) return;
        button.disabled = isLoading;
        button.dataset.ktIndicator = isLoading ? 'on' : 'off';
    }

    function toast(message, isError = false) {
        if (window.toastr) {
            isError ? toastr.error(message) : toastr.success(message);
        } else {
            isError ? alert(message) : console.log(message);
        }
    }
});
