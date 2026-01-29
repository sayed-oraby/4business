/**
 * ============================================================
 * DALEL - Home Page Module
 * ============================================================
 */

const HomePage = {
  /**
   * Initialize home page
   */
  init() {
    // Render latest listings
    if (window.DalelCards && document.getElementById('latestListings')) {
      window.DalelCards.render('latestListings', 6, false);
    }

    // Initialize hero tabs
    if (window.DalelFilters) {
      window.DalelFilters.initTabs();
    }

    console.log('🏠 Home page initialized');
  }
};

// Export for use in other modules
window.DalelHomePage = HomePage;

