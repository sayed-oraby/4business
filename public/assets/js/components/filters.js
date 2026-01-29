/**
 * ============================================================
 * DALEL - Filters Component
 * Filter dropdowns, chips, and tabs functionality
 * ============================================================
 */

const Filters = {
  dropdowns: [],

  /**
   * Initialize filters component
   */
  init() {
    this.initDropdowns();
    this.initChips();
    this.initTabs();
  },

  /**
   * Initialize filter dropdowns
   */
  initDropdowns() {
    this.dropdowns = document.querySelectorAll('.c-dropdown');

    this.dropdowns.forEach(dropdown => {
      const trigger = dropdown.querySelector('.c-chip');
      const items = dropdown.querySelectorAll('.c-dropdown__item');

      if (trigger) {
        trigger.addEventListener('click', (e) => {
          e.stopPropagation();
          const isActive = dropdown.classList.contains('c-dropdown--active');
          this.closeAllDropdowns();
          if (!isActive) {
            dropdown.classList.add('c-dropdown--active');
          }
        });
      }

      items.forEach(item => {
        item.addEventListener('click', () => {
          // Update active state
          items.forEach(i => i.classList.remove('c-dropdown__item--active'));
          item.classList.add('c-dropdown__item--active');

          // Update trigger text
          if (trigger) {
            const chipText = trigger.childNodes[0];
            if (chipText && chipText.nodeType === Node.TEXT_NODE) {
              chipText.textContent = item.textContent + ' ';
            }
          }

          dropdown.classList.remove('c-dropdown--active');
        });
      });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.c-dropdown')) {
        this.closeAllDropdowns();
      }
    });

    // Close on ESC key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.closeAllDropdowns();
      }
    });
  },

  /**
   * Close all dropdowns
   */
  closeAllDropdowns() {
    this.dropdowns.forEach(dropdown => {
      dropdown.classList.remove('c-dropdown--active');
    });
  },

  /**
   * Initialize mobile filter chips
   */
  initChips() {
    const mobileChips = document.querySelectorAll('.c-filters-scroll .c-chip');

    mobileChips.forEach(chip => {
      chip.addEventListener('click', function() {
        this.classList.toggle('c-chip--active');
      });
    });
  },

  /**
   * Initialize hero tabs
   */
  initTabs() {
    const heroTabs = document.querySelectorAll('.p-hero__tab');

    heroTabs.forEach(tab => {
      tab.addEventListener('click', function() {
        heroTabs.forEach(t => t.classList.remove('p-hero__tab--active'));
        this.classList.add('p-hero__tab--active');

        const tabType = this.getAttribute('data-tab');
        console.log('Selected tab:', tabType);
        
        // Store selection
        if (window.DalelStorage) {
          window.DalelStorage.set('selectedTab', tabType);
        }
      });
    });
  }
};

// Export for use in other modules
window.DalelFilters = Filters;

