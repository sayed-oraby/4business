/**
 * ============================================================
 * DALEL - دليل العقار - Main Application
 * Global initialization and page routing
 * ============================================================
 */

(function() {
  'use strict';

  /**
   * Initialize global components
   */
  function initGlobalComponents() {
    // Initialize drawer
    if (window.DalelDrawer) {
      window.DalelDrawer.init();
    }

    // Initialize filters (dropdowns)
    if (window.DalelFilters) {
      window.DalelFilters.init();
    }
  }

  /**
   * Initialize page-specific modules based on body class or page
   */
  function initPageModules() {
    const body = document.body;
    const path = window.location.pathname;

    // Home page
    if (body.classList.contains('page-home') || path.endsWith('index.html') || path === '/' || path.endsWith('/')) {
      if (window.DalelHomePage) {
        window.DalelHomePage.init();
      }
    }

    // Listings page
    if (body.classList.contains('page-listings') || path.endsWith('listings.html')) {
      if (window.DalelListingsPage) {
        window.DalelListingsPage.init();
      }
    }

    // Login page
    if (body.classList.contains('page-login') || path.endsWith('login.html')) {
      if (window.DalelLoginPage) {
        window.DalelLoginPage.init();
      }
    }

    // OTP page
    if (body.classList.contains('page-otp') || path.endsWith('otp.html')) {
      if (window.DalelOTPPage) {
        window.DalelOTPPage.init();
      }
    }

    // New listing page
    if (body.classList.contains('page-new-listing') || path.endsWith('new-listing.html')) {
      if (window.DalelNewListingPage) {
        window.DalelNewListingPage.init();
      }
    }
  }

  /**
   * Initialize smooth scroll for anchor links
   */
  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href === '#') return;

        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });
  }

  /**
   * Initialize navbar scroll effect
   */
  function initNavbarScroll() {
    let lastScroll = 0;
    const navbar = document.querySelector('.c-navbar');

    if (!navbar) return;

    window.addEventListener('scroll', function() {
      const currentScroll = window.pageYOffset;

      if (currentScroll <= 0) {
        navbar.classList.remove('c-navbar--hidden');
        return;
      }

      if (currentScroll > lastScroll && currentScroll > 100) {
        // Scrolling down
        navbar.classList.add('c-navbar--hidden');
      } else {
        // Scrolling up
        navbar.classList.remove('c-navbar--hidden');
      }

      lastScroll = currentScroll;
    }, { passive: true });
  }

  /**
   * Main initialization
   */
  function init() {
    initGlobalComponents();
    initPageModules();
    initSmoothScroll();
    initNavbarScroll();

    console.log('🏠 Dalel Web App Initialized');
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();

