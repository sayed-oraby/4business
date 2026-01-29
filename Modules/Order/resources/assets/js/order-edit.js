import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.OrderEdit;
    if (!config) return;

    const form = document.getElementById('orderEditForm');
    const submitBtn = form?.querySelector('button[type="submit"]');

    form?.addEventListener('submit', (e) => {
        e.preventDefault();
        handleSubmit(e.target);
    });

    function handleSubmit(form) {
        const formData = new FormData(form);
        const data = {};

        // Parse notes and meta as JSON if they look like JSON
        ['notes', 'meta'].forEach(field => {
            const value = formData.get(field);
            if (value && value.trim().startsWith('{')) {
                try {
                    data[field] = JSON.parse(value);
                } catch {
                    data[field] = value;
                }
            } else {
                data[field] = value || null;
            }
        });

        toggleSubmitButton(submitBtn, true);

        axios.put(config.routes.update, data)
            .then(() => {
                showToast('success', config.messages.updated);
                setTimeout(() => {
                    window.location.href = config.routes.show;
                }, 1000);
            })
            .catch((error) => {
                showToast('error', error.response?.data?.message || 'Error updating order');
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

