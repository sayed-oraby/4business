import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.ShippingLocations;
    if (!config) return;

    const stateTableEl = $('#shipping-states-table');
    const cityTableEl = $('#shipping-cities-table');

    const countryFilter = document.getElementById('location-country-filter');
    const stateFilter = document.getElementById('location-state-filter');
    const stateSearch = document.getElementById('location-state-search');
    const citySearch = document.getElementById('location-city-search');
    const openStateBtn = document.querySelector('[data-location-action="open-state-form"]');
    const openCityBtn = document.querySelector('[data-location-action="open-city-form"]');

    const stateModalEl = document.getElementById('shippingStateModal');
    const stateModal = stateModalEl ? new bootstrap.Modal(stateModalEl) : null;
    const stateForm = document.getElementById('shippingStateForm');
    const stateSubmit = document.querySelector('[data-shipping-state-action="submit"]');
    const stateErrors = stateForm?.querySelector('[data-shipping-state-errors]');

    const cityModalEl = document.getElementById('shippingCityModal');
    const cityModal = cityModalEl ? new bootstrap.Modal(cityModalEl) : null;
    const cityForm = document.getElementById('shippingCityForm');
    const citySubmit = document.querySelector('[data-shipping-city-action="submit"]');
    const cityErrors = cityForm?.querySelector('[data-shipping-city-errors]');
    const cityStateSelect = cityForm?.querySelector('[data-city-state-select]');

    let states = [];
    let cities = [];
    let stateTable;
    let cityTable;
    let currentStateId = null;

    initTables();
    bindEvents();
    fetchStates();

    function initTables() {
        stateTable = stateTableEl.DataTable({
            order: [],
            pageLength: 10,
            columns: [
                { data: 'code' },
                { data: 'name_en' },
                { data: 'name_ar' },
                {
                    data: 'country',
                    render: (value) => value ? `${value.name_en} (${value.iso2})` : '',
                },
                {
                    data: 'cities_count',
                    render: (value) => value ?? 0,
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: (row) => renderStateActions(row),
                },
            ],
        });

        cityTable = cityTableEl.DataTable({
            order: [],
            pageLength: 10,
            columns: [
                { data: 'code' },
                { data: 'name_en' },
                { data: 'name_ar' },
                {
                    data: 'state',
                    render: (value) => value ? `${value.name_en} (${value.code})` : '',
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: (row) => renderCityActions(row),
                },
            ],
        });
    }

    function bindEvents() {
        countryFilter?.addEventListener('change', fetchStates);
        stateSearch?.addEventListener('keyup', debounce(fetchStates, 300));
        citySearch?.addEventListener('keyup', debounce(fetchCities, 300));

        openStateBtn?.addEventListener('click', () => {
            resetStateForm();
            stateModal?.show();
        });

        openCityBtn?.addEventListener('click', () => {
            if (!currentStateId) return;
            resetCityForm();
            cityModal?.show();
        });

        stateSubmit?.addEventListener('click', submitStateForm);
        citySubmit?.addEventListener('click', submitCityForm);

        stateTableEl.on('click', '[data-action="edit-state"]', function () {
            const data = stateTable.row($(this).closest('tr')).data();
            if (!data) return;
            fillStateForm(data);
            stateModal?.show();
        });

        stateTableEl.on('click', '[data-action="delete-state"]', function () {
            const data = stateTable.row($(this).closest('tr')).data();
            if (!data) return;
            confirmDelete().then((ok) => {
                if (!ok) return;
                deleteState(data.id);
            });
        });

        stateTableEl.on('click', '[data-action="select-state"]', function () {
            const data = stateTable.row($(this).closest('tr')).data();
            if (!data) return;
            currentStateId = data.id;
            stateFilter.value = data.id;
            fetchCities();
        });

        cityTableEl.on('click', '[data-action="edit-city"]', function () {
            const data = cityTable.row($(this).closest('tr')).data();
            if (!data) return;
            fillCityForm(data);
            cityModal?.show();
        });

        cityTableEl.on('click', '[data-action="delete-city"]', function () {
            const data = cityTable.row($(this).closest('tr')).data();
            if (!data) return;
            confirmDelete().then((ok) => {
                if (!ok) return;
                deleteCity(data.id);
            });
        });
    }

    function fetchStates() {
        const params = {
            country_id: countryFilter?.value || '',
            search: stateSearch?.value || '',
        };

        axios.get(config.routes.states, { params })
            .then(({ data }) => {
                states = data?.data?.states ?? [];
                stateTable.clear().rows.add(states).draw();
                populateStateFilter();
            });
    }

    function fetchCities() {
        if (!stateFilter?.value) {
            cityTable.clear().draw();
            openCityBtn?.setAttribute('disabled', 'disabled');
            return;
        }

        const params = {
            state_id: stateFilter.value,
            search: citySearch?.value || '',
        };

        axios.get(config.routes.cities, { params })
            .then(({ data }) => {
                cities = data?.data?.cities ?? [];
                cityTable.clear().rows.add(cities).draw();
                openCityBtn?.removeAttribute('disabled');
            });
    }

    function submitStateForm() {
        if (!stateForm) return;
        clearErrors(stateErrors);
        const formData = new FormData(stateForm);
        const payload = Object.fromEntries(formData.entries());
        const stateId = payload.state_id;
        const isEdit = Boolean(stateId);
        const url = isEdit ? config.routes.statesUpdate.replace('__ID__', stateId) : config.routes.statesStore;
        const method = isEdit ? 'put' : 'post';

        axios({ method, url, data: payload })
            .then(() => {
                stateModal?.hide();
                resetStateForm();
                fetchStates();
            })
            .catch((error) => handleErrors(error, stateErrors));
    }

    function deleteState(id) {
        const url = config.routes.statesDestroy.replace('__ID__', id);
        axios.delete(url).then(() => fetchStates());
    }

    function resetStateForm() {
        if (!stateForm) return;
        stateForm.reset();
        stateForm.querySelector('input[name="state_id"]').value = '';
        stateForm.querySelector('input[name="_method"]').value = 'POST';
        const title = stateModalEl?.querySelector('[data-shipping-state-form-title]');
        if (title) title.textContent = config.labels.stateFormCreate;
        clearErrors(stateErrors);
    }

    function fillStateForm(data) {
        if (!stateForm) return;
        stateForm.shipping_country_id.value = data.shipping_country_id ?? '';
        stateForm.code.value = data.code ?? '';
        stateForm.name_en.value = data.name_en ?? '';
        stateForm.name_ar.value = data.name_ar ?? '';
        stateForm.lat.value = data.lat ?? '';
        stateForm.lng.value = data.lng ?? '';
        stateForm.querySelector('input[name="state_id"]').value = data.id;
        stateForm.querySelector('input[name="_method"]').value = 'PUT';
        const title = stateModalEl?.querySelector('[data-shipping-state-form-title]');
        if (title) title.textContent = config.labels.stateFormEdit;
    }

    function submitCityForm() {
        if (!cityForm) return;
        clearErrors(cityErrors);
        const formData = new FormData(cityForm);
        const payload = Object.fromEntries(formData.entries());
        const cityId = payload.city_id;
        const isEdit = Boolean(cityId);
        const url = isEdit ? config.routes.citiesUpdate.replace('__ID__', cityId) : config.routes.citiesStore;
        const method = isEdit ? 'put' : 'post';

        axios({ method, url, data: payload })
            .then(() => {
                cityModal?.hide();
                resetCityForm();
                fetchCities();
            })
            .catch((error) => handleErrors(error, cityErrors));
    }

    function deleteCity(id) {
        const url = config.routes.citiesDestroy.replace('__ID__', id);
        axios.delete(url).then(() => fetchCities());
    }

    function resetCityForm() {
        if (!cityForm) return;
        cityForm.reset();
        cityForm.querySelector('input[name="city_id"]').value = '';
        cityForm.querySelector('input[name="_method"]').value = 'POST';
        if (cityStateSelect) {
            cityStateSelect.value = stateFilter?.value || '';
        }
        const title = cityModalEl?.querySelector('[data-shipping-city-form-title]');
        if (title) title.textContent = config.labels.cityFormCreate;
        clearErrors(cityErrors);
    }

    function fillCityForm(data) {
        if (!cityForm) return;
        cityForm.shipping_state_id.value = data.shipping_state_id ?? '';
        cityForm.code.value = data.code ?? '';
        cityForm.name_en.value = data.name_en ?? '';
        cityForm.name_ar.value = data.name_ar ?? '';
        cityForm.lat.value = data.lat ?? '';
        cityForm.lng.value = data.lng ?? '';
        cityForm.querySelector('input[name="city_id"]').value = data.id;
        cityForm.querySelector('input[name="_method"]').value = 'PUT';
        const title = cityModalEl?.querySelector('[data-shipping-city-form-title]');
        if (title) title.textContent = config.labels.cityFormEdit;
    }

    function populateStateFilter() {
        if (!stateFilter || !cityStateSelect) return;
        const options = [`<option value="">${config.filters?.select_state ?? 'Select state'}</option>`];
        states.forEach((state) => {
            options.push(`<option value="${state.id}">${state.name_en} (${state.code})</option>`);
        });
        stateFilter.innerHTML = options.join('');
        cityStateSelect.innerHTML = options.join('');

        stateFilter.disabled = states.length === 0;
        cityStateSelect.disabled = states.length === 0;
    }

    function icon(name) {
        return `<i class="ki-outline ${name} fs-2"></i>`;
    }

    function renderStateActions(row) {
        const actions = [];
        actions.push(`
            <button class="btn btn-sm btn-icon btn-light-secondary" data-action="select-state" title="Select" data-id="${row.id}">
                ${icon('ki-arrow-right')}
            </button>
        `);
        if (config.can?.update) {
            actions.push(`
                <button class="btn btn-sm btn-icon btn-light-primary" data-action="edit-state" title="Edit" data-id="${row.id}">
                    ${icon('ki-pencil')}
                </button>
            `);
        }
        if (config.can?.delete) {
            actions.push(`
                <button class="btn btn-sm btn-icon btn-light-danger" data-action="delete-state" title="Delete" data-id="${row.id}">
                    ${icon('ki-trash')}
                </button>
            `);
        }
        return `<div class="d-flex justify-content-center gap-2">${actions.join('')}</div>`;
    }

    function renderCityActions(row) {
        const actions = [];
        if (config.can?.update) {
            actions.push(`
                <button class="btn btn-sm btn-icon btn-light-primary" data-action="edit-city" title="Edit" data-id="${row.id}">
                    ${icon('ki-pencil')}
                </button>
            `);
        }
        if (config.can?.delete) {
            actions.push(`
                <button class="btn btn-sm btn-icon btn-light-danger" data-action="delete-city" title="Delete" data-id="${row.id}">
                    ${icon('ki-trash')}
                </button>
            `);
        }
        return `<div class="d-flex justify-content-center gap-2">${actions.join('')}</div>`;
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
