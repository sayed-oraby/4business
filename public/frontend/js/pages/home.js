/**
 * ============================================================
 * DALEL - Home Page Module
 * ============================================================
 */

(function() {
    'use strict';

    // Selected locations state
    const selectedLocations = {
        states: new Set(),
        cities: new Set()
    };

    /**
     * Initialize post type tabs
     */
    function initPostTypeTabs() {
        const tabs = document.querySelectorAll('.p-hero__tab');
        const hiddenInput = document.getElementById('selectedPostType');

        if (!tabs.length || !hiddenInput) return;

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('p-hero__tab--active'));
                this.classList.add('p-hero__tab--active');
                hiddenInput.value = this.dataset.tab;
            });
        });
    }

    /**
     * Initialize category dropdown
     */
    function initCategoryDropdown() {
        const dropdown = document.getElementById('categoryDropdown');
        const btn = document.getElementById('categoryBtn');
        const input = document.getElementById('categoryInput');
        const items = dropdown?.querySelectorAll('.p-hero__dropdown-item');

        if (!dropdown || !btn) return;

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const wasActive = dropdown.classList.contains('p-hero__dropdown--active');
            closeAllDropdowns();
            if (!wasActive) {
                dropdown.classList.add('p-hero__dropdown--active');
                showBackdrop();
            }
        });

        // Prevent dropdown menu from closing when clicking inside
        const menu = dropdown.querySelector('.p-hero__dropdown-menu');
        if (menu) {
            menu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        items?.forEach(item => {
            // Handle both click and touch events
            function handleItemSelect(e) {
                e.preventDefault();
                e.stopPropagation();

                const value = item.dataset.value;
                const name = item.dataset.name;

                // Update hidden input
                if (input) input.value = value;

                // Update button text
                const textEl = btn.querySelector('.p-hero__dropdown-text');
                if (textEl) textEl.textContent = name;

                // Update active state
                items.forEach(i => i.classList.remove('p-hero__dropdown-item--active'));
                item.classList.add('p-hero__dropdown-item--active');

                // Close dropdown
                closeAllDropdowns();
            }

            item.addEventListener('click', handleItemSelect);
            item.addEventListener('touchend', handleItemSelect);
        });
    }

    /**
     * Initialize location multi-select dropdown
     */
    function initLocationDropdown() {
        const dropdown = document.getElementById('locationDropdown');
        const btn = document.getElementById('locationBtn');
        const locationItems = document.querySelectorAll('#locationList .p-hero__location-item');

        if (!dropdown || !btn) return;

        // Toggle dropdown
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const wasActive = dropdown.classList.contains('p-hero__dropdown--active');
            closeAllDropdowns();
            if (!wasActive) {
                dropdown.classList.add('p-hero__dropdown--active');
                showBackdrop();
            }
        });

        // Prevent dropdown from closing when clicking inside
        dropdown.querySelector('.p-hero__dropdown-menu')?.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Location item click handler
        locationItems.forEach(item => {
            item.addEventListener('click', function(e) {
                const checkbox = this.querySelector('.p-hero__location-checkbox');
                if (e.target !== checkbox) {
                    checkbox.checked = !checkbox.checked;
                }

                const type = this.dataset.type;
                const id = this.dataset.id;
                const isChecked = checkbox.checked;

                if (type === 'state') {
                    if (isChecked) {
                        selectedLocations.states.add(id);
                    } else {
                        selectedLocations.states.delete(id);
                    }

                    // Select/deselect all cities of this state
                    const cityCbs = document.querySelectorAll(`.p-hero__location-item--city[data-state="${id}"] .p-hero__location-checkbox`);
                    cityCbs.forEach(cityCb => {
                        cityCb.checked = isChecked;
                        const cityId = cityCb.closest('.p-hero__location-item').dataset.id;
                        if (isChecked) {
                            selectedLocations.cities.add(cityId);
                        } else {
                            selectedLocations.cities.delete(cityId);
                        }
                    });
                } else if (type === 'city') {
                    const stateId = this.dataset.state;
                    if (isChecked) {
                        selectedLocations.cities.add(id);
                    } else {
                        selectedLocations.cities.delete(id);
                    }
                    updateStateCheckbox(stateId);
                }

                this.classList.toggle('is-active', isChecked);
                updateSelectedTags();
                updateHiddenInputs();
            });
        });
    }

    /**
     * Update state checkbox based on its cities
     */
    function updateStateCheckbox(stateId) {
        const stateItem = document.querySelector(`.p-hero__location-item--state[data-id="${stateId}"]`);
        const stateCb = stateItem?.querySelector('.p-hero__location-checkbox');
        const cityCbs = document.querySelectorAll(`.p-hero__location-item--city[data-state="${stateId}"] .p-hero__location-checkbox`);

        if (!stateCb || !cityCbs.length) return;

        const checkedCities = Array.from(cityCbs).filter(cb => cb.checked);

        if (checkedCities.length === cityCbs.length) {
            stateCb.checked = true;
            stateCb.indeterminate = false;
            selectedLocations.states.add(stateId);
            stateItem.classList.add('is-active');
        } else if (checkedCities.length > 0) {
            stateCb.checked = false;
            stateCb.indeterminate = true;
            selectedLocations.states.delete(stateId);
            stateItem.classList.remove('is-active');
        } else {
            stateCb.checked = false;
            stateCb.indeterminate = false;
            selectedLocations.states.delete(stateId);
            stateItem.classList.remove('is-active');
        }
    }

    /**
     * Update select all checkbox state - not used anymore
     */
    function updateSelectAllState() {
        // No longer needed with simplified UI
    }

    /**
     * Update selected tags display
     */
    function updateSelectedTags() {
        const tagsRow = document.getElementById('tagsRow');
        const locationLabel = document.getElementById('locationLabel');
        if (!tagsRow) return;

        tagsRow.innerHTML = '';

        // Count total selections (states as full, only cities not in selected states)
        let totalCount = selectedLocations.states.size;
        selectedLocations.cities.forEach(cityId => {
            const cityEl = document.querySelector(`.p-hero__location-item--city[data-id="${cityId}"]`);
            const stateId = cityEl?.dataset.state;
            if (!selectedLocations.states.has(stateId)) {
                totalCount++;
            }
        });

        // Update location button label
        if (locationLabel) {
            if (totalCount === 0) {
                locationLabel.textContent = 'اختر المنطقة';
            } else {
                locationLabel.textContent = `${totalCount} منطقة`;
            }
        }

        if (totalCount === 0) return;

        // Add clear all button first
        const clearBtn = document.createElement('button');
        clearBtn.type = 'button';
        clearBtn.className = 'p-hero__clear-tags';
        clearBtn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
            مسح الكل
        `;
        clearBtn.addEventListener('click', clearAllLocations);
        tagsRow.appendChild(clearBtn);

        // Show state tags
        selectedLocations.states.forEach(stateId => {
            const stateEl = document.querySelector(`.p-hero__location-item--state[data-id="${stateId}"]`);
            const stateName = stateEl?.querySelector('.p-hero__location-name')?.textContent?.trim();
            if (stateName) {
                tagsRow.appendChild(createTag(stateName, 'state', stateId));
            }
        });

        // Show city tags (only for cities whose state is not fully selected)
        selectedLocations.cities.forEach(cityId => {
            const cityEl = document.querySelector(`.p-hero__location-item--city[data-id="${cityId}"]`);
            const stateId = cityEl?.dataset.state;

            // Skip if parent state is selected
            if (selectedLocations.states.has(stateId)) return;

            const cityName = cityEl?.querySelector('.p-hero__location-name')?.textContent?.trim();
            if (cityName) {
                tagsRow.appendChild(createTag(cityName, 'city', cityId));
            }
        });
    }

    /**
     * Create a tag element
     */
    function createTag(name, type, id) {
        const tag = document.createElement('span');
        tag.className = 'p-hero__tag';
        // Trim name if too long
        const displayName = name.length > 12 ? name.substring(0, 10) + '..' : name;
        tag.title = name; // Show full name on hover
        tag.innerHTML = `
            ${displayName}
            <span class="p-hero__tag-remove" data-type="${type}" data-id="${id}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </span>
        `;

        tag.querySelector('.p-hero__tag-remove').addEventListener('click', function(e) {
            e.stopPropagation();
            removeSelection(type, id);
        });

        return tag;
    }


    /**
     * Remove a selection
     */
    function removeSelection(type, id) {
        if (type === 'state') {
            selectedLocations.states.delete(id);
            const stateItem = document.querySelector(`.p-hero__location-item--state[data-id="${id}"]`);
            const stateCb = stateItem?.querySelector('.p-hero__location-checkbox');
            if (stateCb) {
                stateCb.checked = false;
                stateCb.indeterminate = false;
            }
            stateItem?.classList.remove('is-active');

            // Deselect all cities of this state
            const cityItems = document.querySelectorAll(`.p-hero__location-item--city[data-state="${id}"]`);
            cityItems.forEach(item => {
                const cb = item.querySelector('.p-hero__location-checkbox');
                if (cb) cb.checked = false;
                item.classList.remove('is-active');
                selectedLocations.cities.delete(item.dataset.id);
            });
        } else if (type === 'city') {
            selectedLocations.cities.delete(id);
            const cityItem = document.querySelector(`.p-hero__location-item--city[data-id="${id}"]`);
            const cityCb = cityItem?.querySelector('.p-hero__location-checkbox');
            if (cityCb) {
                cityCb.checked = false;
                cityItem.classList.remove('is-active');
                updateStateCheckbox(cityItem.dataset.state);
            }
        }

        updateSelectedTags();
        updateHiddenInputs();
    }

    // Expose removeSelection globally for onclick handler in tags
    window.removeSelection = removeSelection;

    /**
     * Update hidden form inputs for selected locations
     */
    function updateHiddenInputs() {
        const container = document.getElementById('locationInputs');
        if (!container) return;

        container.innerHTML = '';

        // Add state inputs
        selectedLocations.states.forEach(stateId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'locations[]';
            input.value = `state_${stateId}`;
            container.appendChild(input);
        });

        // Add city inputs (only for cities whose state is not selected)
        selectedLocations.cities.forEach(cityId => {
            const cityEl = document.querySelector(`.p-hero__location-item--city[data-id="${cityId}"]`);
            const stateId = cityEl?.dataset.state;

            // Skip if parent state is selected
            if (selectedLocations.states.has(stateId)) return;

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'locations[]';
            input.value = `city_${cityId}`;
            container.appendChild(input);
        });
    }

    /**
     * Close all dropdowns
     */
    function closeAllDropdowns() {
        document.querySelectorAll('.p-hero__dropdown').forEach(d => {
            d.classList.remove('p-hero__dropdown--active');
        });
    }

    /**
     * Show backdrop - disabled, using normal dropdown
     */
    function showBackdrop() {
        // Disabled - using normal dropdown instead
    }

    /**
     * Filter dropdown list
     */
    window.filterList = function(input, listId) {
        const filter = input.value.toLowerCase();
        const list = document.getElementById(listId);
        const items = list?.querySelectorAll('.p-hero__dropdown-item');

        items?.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(filter) ? '' : 'none';
        });
    };

    /**
     * Filter locations list
     */
    window.filterLocations = function(input) {
        const filter = input.value.toLowerCase();
        const items = document.querySelectorAll('#locationList .p-hero__location-item');

        items?.forEach(item => {
            const name = item.dataset.name?.toLowerCase() || item.textContent.toLowerCase();
            const type = item.dataset.type;

            if (type === 'state') {
                // For states, also check if any of its cities match
                const stateId = item.dataset.id;
                const cityItems = document.querySelectorAll(`.p-hero__location-item--city[data-state="${stateId}"]`);
                const hasMatchingCity = Array.from(cityItems).some(city =>
                    (city.dataset.name?.toLowerCase() || '').includes(filter)
                );
                item.style.display = (name.includes(filter) || hasMatchingCity) ? '' : 'none';
            } else {
                // For cities, check if state is visible too
                const stateId = item.dataset.state;
                const stateItem = document.querySelector(`.p-hero__location-item--state[data-id="${stateId}"]`);
                const stateVisible = stateItem?.style.display !== 'none';
                item.style.display = (name.includes(filter) || (stateVisible && filter === '')) ? '' : 'none';
            }
        });
    };


    /**
     * Clear all selected locations
     */
    function clearAllLocations() {
        selectedLocations.states.clear();
        selectedLocations.cities.clear();

        // Uncheck all checkboxes
        document.querySelectorAll('#locationList .p-hero__location-checkbox').forEach(cb => {
            cb.checked = false;
            cb.indeterminate = false;
        });

        // Remove active class
        document.querySelectorAll('#locationList .p-hero__location-item').forEach(item => {
            item.classList.remove('is-active');
        });

        updateSelectedTags();
        updateHiddenInputs();
    }

    // Expose functions globally for onclick handlers
    window.clearAllLocations = clearAllLocations;
    window.closeAllDropdowns = closeAllDropdowns;

    /**
     * Initialize
     */
    function init() {
        initPostTypeTabs();
        initCategoryDropdown();
        initLocationDropdown();

        // Clear all locations button
        const clearAllBtn = document.getElementById('clearAllLocations');
        clearAllBtn?.addEventListener('click', function(e) {
            e.stopPropagation();
            clearAllLocations();
        });

        // Close dropdowns on backdrop click
        const backdrop = document.getElementById('heroBackdrop');
        backdrop?.addEventListener('click', closeAllDropdowns);

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            // Also check for dropdown-menu (which is position: fixed on mobile)
            if (!e.target.closest('.p-hero__dropdown') &&
                !e.target.closest('.p-hero__dropdown-menu') &&
                !e.target.closest('.p-hero__backdrop')) {
                closeAllDropdowns();
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
