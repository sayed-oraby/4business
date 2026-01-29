import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.OrderShow;
    if (!config) return;

    const changeStatusForm = document.getElementById('changeStatusForm');
    const changePaymentStatusForm = document.getElementById('changePaymentStatusForm');

    if (changeStatusForm) {
        changeStatusForm.addEventListener('submit', (e) => {
            e.preventDefault();
            handleStatusChange(e.target);
        });
    }

    if (changePaymentStatusForm) {
        changePaymentStatusForm.addEventListener('submit', (e) => {
            e.preventDefault();
            handlePaymentStatusChange(e.target);
        });
    }

    function handleStatusChange(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const statusId = form.querySelector('select[name="status_id"]').value;

        if (!statusId) {
            return;
        }

        toggleSubmitButton(submitBtn, true);

        axios.post(config.routes.changeStatus, {
            status_id: statusId,
            comment: null,
        })
            .then(() => {
                showToast('success', config.messages.statusChanged);
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            })
            .catch((error) => {
                showToast('error', error.response?.data?.message || 'Error updating status');
                toggleSubmitButton(submitBtn, false);
            });
    }

    function handlePaymentStatusChange(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const paymentStatus = form.querySelector('select[name="payment_status"]').value;

        toggleSubmitButton(submitBtn, true);

        axios.post(config.routes.changePaymentStatus, {
            payment_status: paymentStatus,
        })
            .then(() => {
                showToast('success', config.messages.paymentStatusChanged);
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            })
            .catch((error) => {
                showToast('error', error.response?.data?.message || 'Error updating payment status');
                toggleSubmitButton(submitBtn, false);
            });
    }

    function toggleSubmitButton(btn, loading) {
        if (!btn) return;
        if (loading) {
            btn.setAttribute('data-kt-indicator', 'on');
            btn.disabled = true;
        } else {
            btn.setAttribute('data-kt-indicator', 'off');
            btn.disabled = false;
        }
    }

    function showToast(type, message) {
        if (window.toastr) {
            toastr[type](message);
            return;
        }
        if (window.Swal) {
            Swal.fire({
                icon: type === 'success' ? 'success' : 'error',
                text: message,
                timer: 3000,
                showConfirmButton: false,
            });
            return;
        }
        alert(message);
    }
});

