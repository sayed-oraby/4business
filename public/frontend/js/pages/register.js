/**
 * ============================================================
 * DALEL - Register Page Module
 * ============================================================
 */

const RegisterPage = {
    /**
     * Initialize register page
     */
    init() {
        this.initForm();
        console.log('📝 Register page initialized');
    },

    /**
     * Initialize register form
     */
    initForm() {
        const registerForm = document.getElementById('registerForm');
        const errorContainer = document.getElementById('ajax-error-container');

        if (registerForm) {
            registerForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                // Clear previous errors
                if (errorContainer) {
                    errorContainer.style.display = 'none';
                    errorContainer.innerHTML = '';
                }

                const submitBtn = registerForm.querySelector('button[type="submit"]');
                const originalBtnHtml = submitBtn.innerHTML;

                // Loading state
                submitBtn.disabled = true;
                const loadingText = (window.Dalel && window.Dalel.config && window.Dalel.config.translations)
                    ? window.Dalel.config.translations.loading
                    : 'Loading...';
                submitBtn.innerHTML = loadingText;

                const formData = new FormData(registerForm);

                try {
                    const response = await fetch(registerForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.status === 'success') {
                        // Success SweetAlert
                        const successTitle = (window.Dalel && window.Dalel.config && window.Dalel.config.translations)
                            ? window.Dalel.config.translations.success
                            : 'Success';

                        Swal.fire({
                            title: successTitle,
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    } else {
                        // Validation or other errors
                        this.handleErrors(data, errorContainer);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    }
                } catch (error) {
                    console.error('Register error:', error);
                    const errorTitle = (window.Dalel && window.Dalel.config && window.Dalel.config.translations)
                        ? window.Dalel.config.translations.error
                        : 'Error';

                    Swal.fire({
                        title: errorTitle,
                        text: 'Something went wrong. Please try again.',
                        icon: 'error'
                    });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            });
        }
    },

    /**
     * Handle error display
     */
    handleErrors(data, container) {
        const errorTitle = (window.Dalel && window.Dalel.config && window.Dalel.config.translations)
            ? window.Dalel.config.translations.error
            : 'Error';

        if (data.errors) {
            if (container) {
                container.style.display = 'block';
                let errorHtml = '';

                Object.keys(data.errors).forEach(key => {
                    data.errors[key].forEach(message => {
                        errorHtml += `<p>${message}</p>`;
                    });
                });

                container.innerHTML = errorHtml;
            }

            Swal.fire({
                title: errorTitle,
                text: data.message || 'Please check the form for errors.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else if (data.message) {
            Swal.fire({
                title: errorTitle,
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }
};

// Initialize
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => RegisterPage.init());
} else {
    RegisterPage.init();
}
