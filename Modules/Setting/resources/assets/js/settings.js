document.addEventListener('DOMContentLoaded', () => {
    const mediaInputs = document.querySelectorAll('[data-media-input]');
    mediaInputs.forEach((input) => {
        input.addEventListener('change', () => {
            const targetSelector = input.getAttribute('data-preview-target');
            const target = document.querySelector(targetSelector);
            const removeInput = document.querySelector(`[data-remove-input="${input.name}"]`);

            if (removeInput) {
                removeInput.value = '0';
            }

            if (input.files && input.files[0] && target) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    target.src = event.target?.result ?? target.dataset.placeholder;
                };
                reader.readAsDataURL(input.files[0]);
            } else if (target) {
                target.src = target.dataset.placeholder;
            }
        });
    });

    document.querySelectorAll('[data-media-remove]').forEach((button) => {
        button.addEventListener('click', () => {
            const field = button.getAttribute('data-media-remove');
            const input = document.querySelector(`input[name="${field}"]`);
            const preview = document.querySelector(button.getAttribute('data-preview-target'));
            const removeInput = document.querySelector(`[data-remove-input="${field}"]`);

            if (input) {
                input.value = '';
            }

            if (removeInput) {
                removeInput.value = '1';
            }

            if (preview && preview.dataset.placeholder) {
                preview.src = preview.dataset.placeholder;
            }
        });
    });

    const localeCheckboxes = document.querySelectorAll('[data-locale-checkbox]');
    const localePanes = document.querySelectorAll('[data-locale-pane]');
    const localeTabs = document.querySelectorAll('[data-locale-tab]');

    const syncLocaleVisibility = () => {
        const activeLocales = Array.from(localeCheckboxes)
            .filter((checkbox) => checkbox.checked || checkbox.disabled)
            .map((checkbox) => checkbox.value);

        localePanes.forEach((pane) => {
            const code = pane.getAttribute('data-locale-pane');
            if (activeLocales.includes(code)) {
                pane.classList.remove('d-none');
            } else {
                pane.classList.add('d-none');
            }
        });

        localeTabs.forEach((tab) => {
            const code = tab.getAttribute('data-locale-tab');
            if (activeLocales.includes(code)) {
                tab.removeAttribute('disabled');
            } else {
                tab.setAttribute('disabled', 'disabled');
            }
        });
    };

    localeCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            const englishCheckbox = document.querySelector('[data-locale-checkbox][value="en"]');
            if (englishCheckbox && !englishCheckbox.checked) {
                englishCheckbox.checked = true;
            }
            syncLocaleVisibility();
        });
    });

    syncLocaleVisibility();
});
