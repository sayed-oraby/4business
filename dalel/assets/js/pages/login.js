/**
 * ============================================================
 * DALEL - Login Page Module
 * ============================================================
 */

const LoginPage = {
  /**
   * Initialize login page
   */
  init() {
    this.initForm();
    console.log('🔐 Login page initialized');
  },

  /**
   * Initialize login form
   */
  initForm() {
    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
      loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        this.handleSubmit();
      });
    }
  },

  /**
   * Handle form submission
   */
  handleSubmit() {
    // Simulate login - redirect to OTP verification
    window.location.href = 'otp.html';
  }
};

// Export for use in other modules
window.DalelLoginPage = LoginPage;

