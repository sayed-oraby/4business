/**
 * ============================================================
 * DALEL - OTP Verification Page Module
 * ============================================================
 */

const OTPPage = {
  boxes: null,
  verifyBtn: null,
  errorEl: null,
  cardEl: null,
  successEl: null,
  timerEl: null,
  resendLink: null,
  timeLeft: 120,
  timerInterval: null,

  /**
   * Initialize OTP page
   */
  init() {
    this.boxes = document.querySelectorAll('.p-otp__box');
    this.verifyBtn = document.getElementById('verifyBtn');
    this.errorEl = document.getElementById('otpError');
    this.cardEl = document.getElementById('otpCard');
    this.successEl = document.getElementById('otpSuccess');
    this.timerEl = document.getElementById('otpTimer');
    this.resendLink = document.getElementById('resendLink');

    if (this.boxes.length === 0) return;

    this.initBoxes();
    this.initTimer();
    this.initResend();
    this.initVerify();

    // Focus first box
    this.boxes[0]?.focus();

    console.log('🔢 OTP page initialized');
  },

  /**
   * Initialize OTP input boxes
   */
  initBoxes() {
    this.boxes.forEach((box, index) => {
      // Handle input
      box.addEventListener('input', (e) => {
        const value = e.target.value;

        // Only allow numbers
        if (!/^\d*$/.test(value)) {
          e.target.value = '';
          return;
        }

        // Update visual state
        if (value) {
          box.classList.add('p-otp__box--filled');
          // Auto-focus next box
          if (index < this.boxes.length - 1) {
            this.boxes[index + 1].focus();
          }
        } else {
          box.classList.remove('p-otp__box--filled');
        }

        // Clear error state
        this.clearError();
      });

      // Handle backspace
      box.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !box.value && index > 0) {
          this.boxes[index - 1].focus();
        }
      });
    });

    // Handle paste on first box
    this.boxes[0]?.addEventListener('paste', (e) => {
      e.preventDefault();
      const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 4);

      pastedData.split('').forEach((char, i) => {
        if (this.boxes[i]) {
          this.boxes[i].value = char;
          this.boxes[i].classList.add('p-otp__box--filled');
        }
      });

      const focusIndex = Math.min(pastedData.length, this.boxes.length - 1);
      this.boxes[focusIndex]?.focus();
    });
  },

  /**
   * Initialize countdown timer
   */
  initTimer() {
    if (!this.timerEl) return;

    this.timerInterval = setInterval(() => {
      this.timeLeft--;

      const minutes = Math.floor(this.timeLeft / 60);
      const seconds = this.timeLeft % 60;
      this.timerEl.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

      if (this.timeLeft <= 0) {
        clearInterval(this.timerInterval);
        this.resendLink?.classList.remove('p-otp__resend-link--disabled');
        this.timerEl.textContent = '00:00';
      }
    }, 1000);
  },

  /**
   * Initialize resend link
   */
  initResend() {
    if (!this.resendLink) return;

    this.resendLink.addEventListener('click', (e) => {
      e.preventDefault();
      if (this.resendLink.classList.contains('p-otp__resend-link--disabled')) return;

      // Reset timer
      this.timeLeft = 120;
      this.resendLink.classList.add('p-otp__resend-link--disabled');

      // Clear boxes
      this.boxes.forEach(box => {
        box.value = '';
        box.classList.remove('p-otp__box--filled', 'p-otp__box--error');
      });
      this.boxes[0]?.focus();

      // Show feedback
      alert('تم إرسال رمز جديد');
    });
  },

  /**
   * Initialize verify button
   */
  initVerify() {
    if (!this.verifyBtn) return;

    this.verifyBtn.addEventListener('click', () => this.verify());

    // Allow Enter key to submit
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        this.verify();
      }
    });
  },

  /**
   * Verify OTP code
   */
  verify() {
    const code = Array.from(this.boxes).map(b => b.value).join('');

    if (code.length !== 4) {
      this.showError('يرجى إدخال رمز التحقق كاملاً');
      this.boxes.forEach(b => {
        if (!b.value) b.classList.add('p-otp__box--error');
      });
      return;
    }

    // Simulate verification (accept any 4-digit code for demo)
    if (code === '1234' || code.length === 4) {
      // Show success
      if (this.cardEl) this.cardEl.style.display = 'none';
      if (this.successEl) this.successEl.classList.add('p-otp__success--visible');

      // Redirect after delay
      setTimeout(() => {
        window.location.href = 'index.html';
      }, 2000);
    } else {
      this.showError('رمز التحقق غير صحيح، يرجى المحاولة مرة أخرى');
      this.boxes.forEach(b => b.classList.add('p-otp__box--error'));
    }
  },

  /**
   * Show error message
   * @param {string} message
   */
  showError(message) {
    if (this.errorEl) {
      this.errorEl.textContent = message;
      this.errorEl.classList.add('p-otp__error--visible');
    }
  },

  /**
   * Clear error state
   */
  clearError() {
    if (this.errorEl) {
      this.errorEl.classList.remove('p-otp__error--visible');
    }
    this.boxes.forEach(b => b.classList.remove('p-otp__box--error'));
  }
};

// Export for use in other modules
window.DalelOTPPage = OTPPage;

