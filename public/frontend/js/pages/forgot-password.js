/**
 * ============================================================
 * DALEL - Forgot Password AJAX Module
 * ============================================================
 */

const ForgotPassword = {
    init() {
        this.initRequestForm();
        this.initOtpForm();
        this.initResetForm();
    },

    /**
     * Helper to show errors
     */
    showErrors(data, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.style.display = 'block';
        let html = '';
        if (data.errors) {
            Object.keys(data.errors).forEach(key => {
                data.errors[key].forEach(msg => {
                    html += `<p>${msg}</p>`;
                });
            });
        } else if (data.message) {
            html = `<p>${data.message}</p>`;
        }
        container.innerHTML = html;

        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: data.message || 'يرجى التأكد من البيانات المدخلة',
            confirmButtonText: 'حسناً'
        });
    },

    /**
     * 1. Request OTP Form
     */
    initRequestForm() {
        const form = document.querySelector('.page-forgot-password .p-login__form');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const originalHtml = btn.innerHTML;

            // Clear errors
            const errorContainer = document.querySelector('.p-login__alert--error');
            if (errorContainer) errorContainer.style.display = 'none';

            btn.disabled = true;
            btn.innerHTML = 'جاري الإرسال...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                });

                const data = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الإرسال',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    this.showErrors(data, 'ajax-error-container');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error(error);
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    },

    /**
     * 2. Verify OTP Form
     */
    initOtpForm() {
        const form = document.getElementById('otpForm');
        if (!form || !document.querySelector('.page-verify-otp')) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = 'جاري التحقق...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                });

                const data = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التحقق',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: data.message || 'الرمز غير صحيح'
                    });
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error(error);
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });

        // Resend logic
        const resendForm = document.getElementById('resendForm');
        if (resendForm) {
            resendForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const resendBtn = document.getElementById('resendBtn');

                try {
                    const response = await fetch(resendForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: new FormData(resendForm)
                    });

                    const data = await response.json();

                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم إعادة الإرسال',
                            text: data.message,
                            timer: 2000
                        });
                        // Reset timer if needed or just handle UI
                        location.reload(); // Simplest way to reset the whole state for now
                    }
                } catch (error) {
                    console.error(error);
                }
            });
        }
    },

    /**
     * 3. Reset Password Form
     */
    initResetForm() {
        const form = document.getElementById('resetPasswordForm');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;

            if (password !== confirm) {
                Swal.fire({ icon: 'error', title: 'خطأ', text: 'كلمة المرور وتأكيد كلمة المرور غير متطابقين' });
                return;
            }

            const btn = form.querySelector('button[type="submit"]');
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = 'جاري الحفظ...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                });

                const data = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم بنجاح',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    this.showErrors(data, 'ajax-error-container');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error(error);
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', () => ForgotPassword.init());
