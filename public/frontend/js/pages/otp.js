/**
 * ============================================================
 * DALEL - OTP Verification Module
 * ============================================================
 */

const OTPPage = {
  timerInterval: null,

  /**
   * Initialize OTP page
   */
  init() {
    this.initForm();
    this.initOtpInputs();
    this.initTimer();
    this.initResend();
    console.log('📱 OTP page initialized');
  },

  /**
   * Initialize OTP form
   */
  initForm() {
    const otpForm = document.getElementById('otpForm');
    const errorContainer = document.getElementById('ajax-error-container');

    if (otpForm) {
      otpForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Clear previous errors
        if (errorContainer) {
          errorContainer.style.display = 'none';
          errorContainer.innerHTML = '';
        }

        const submitBtn = otpForm.querySelector('button[type="submit"]');
        const originalBtnHtml = submitBtn.innerHTML;

        // Loading state
        submitBtn.disabled = true;
        const loadingText = (window.Dalel && window.Dalel.config && window.Dalel.config.translations)
          ? window.Dalel.config.translations.loading
          : 'Loading...';
        submitBtn.innerHTML = loadingText;

        const formData = new FormData(otpForm);

        try {
          const response = await fetch(otpForm.action, {
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
          console.error('OTP error:', error);
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
   * Handle OTP input behavior
   */
  initOtpInputs() {
    const otpBoxes = document.querySelectorAll('.p-otp__box');

    otpBoxes.forEach((box, index) => {
      // Auto-focus next on input
      box.addEventListener('input', function (e) {
        // Only allow numbers
        this.value = this.value.replace(/[^0-9]/g, '');

        if (this.value.length === 1) {
          this.classList.add('p-otp__box--filled');
          if (index < otpBoxes.length - 1) {
            otpBoxes[index + 1].focus();
          }
        } else {
          this.classList.remove('p-otp__box--filled');
        }
      });

      // Handle backspace
      box.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
          otpBoxes[index - 1].focus();
          otpBoxes[index - 1].classList.remove('p-otp__box--filled');
        }
      });

      // Handle paste
      box.addEventListener('paste', function (e) {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        const digits = paste.replace(/\D/g, '').split('');
        digits.forEach((digit, i) => {
          if (otpBoxes[i]) {
            otpBoxes[i].value = digit;
            otpBoxes[i].classList.add('p-otp__box--filled');
          }
        });
        if (digits.length >= 4) {
          otpBoxes[3].focus();
        }
      });
    });
  },

  /**
   * Handle countdown timer
   */
  initTimer() {
    if (this.timerInterval) clearInterval(this.timerInterval);

    let countdown = 60;
    const countdownEl = document.getElementById('countdown');
    const timerWrapper = document.getElementById('timerWrapper');
    const resendLink = document.getElementById('resendLink');

    if (!countdownEl) return;

    // Reset UI
    if (timerWrapper) timerWrapper.style.display = 'flex';
    if (resendLink) resendLink.classList.add('p-otp__resend-link--disabled');
    countdownEl.textContent = countdown;

    this.timerInterval = setInterval(() => {
      countdown--;
      if (countdownEl) countdownEl.textContent = countdown;

      if (countdown <= 0) {
        clearInterval(this.timerInterval);
        if (timerWrapper) timerWrapper.style.display = 'none';
        if (resendLink) resendLink.classList.remove('p-otp__resend-link--disabled');
      }
    }, 1000);
  },

  /**
   * Handle OTP resend
   */
  initResend() {
    const resendLink = document.getElementById('resendLink');
    if (resendLink) {
      resendLink.addEventListener('click', async (e) => {
        e.preventDefault();
        if (resendLink.classList.contains('p-otp__resend-link--disabled')) return;

        try {
          const response = await fetch(resendLink.href, {
            method: 'GET',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            }
          });

          const data = await response.json();

          if (response.ok && data.status === 'success') {
            const successTitle = (window.Dalel && window.Dalel.config && window.Dalel.config.translations)
              ? window.Dalel.config.translations.success
              : 'Success';

            Swal.fire({
              title: successTitle,
              text: data.message,
              icon: 'success',
              timer: 2000
            });

            // Restart timer
            this.initTimer();
          } else if (data.redirect) {
            window.location.href = data.redirect;
          } else {
            Swal.fire({
              title: 'Error',
              text: data.message || 'Error occurred',
              icon: 'error'
            });
          }
        } catch (error) {
          console.error('Resend error:', error);
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
          if (Array.isArray(data.errors[key])) {
            data.errors[key].forEach(message => {
              errorHtml += `<p>${message}</p>`;
            });
          } else {
            errorHtml += `<p>${data.errors[key]}</p>`;
          }
        });

        container.innerHTML = errorHtml;
      }

      Swal.fire({
        title: errorTitle,
        text: data.message || 'Please check the OTP code.',
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
  document.addEventListener('DOMContentLoaded', () => OTPPage.init());
} else {
  OTPPage.init();
}
