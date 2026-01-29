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
      // Form submits normally to Laravel backend
      console.log('📝 Login form ready');
    }
  },

  /**
   * Handle form submission
   */
  handleSubmit() {
    // No longer needed - using standard form submission
  }
};

// Export for use in other modules
window.DalelLoginPage = LoginPage;

