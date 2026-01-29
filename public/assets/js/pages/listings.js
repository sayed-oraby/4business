/**
 * ============================================================
 * DALEL - Listings Page Module
 * ============================================================
 */

const ListingsPage = {
  currentCount: 9,

  /**
   * Initialize listings page
   */
  init() {
    this.renderListings();
    this.initLoadMore();
    this.updateCount();

    console.log('📋 Listings page initialized');
  },

  /**
   * Render listings
   */
  renderListings() {
    if (window.DalelCards && document.getElementById('listingsGrid')) {
      window.DalelCards.render('listingsGrid', this.currentCount, true);
    }
  },

  /**
   * Initialize load more button
   */
  initLoadMore() {
    const loadMoreBtn = document.getElementById('loadMore');

    if (loadMoreBtn && window.DalelCards) {
      loadMoreBtn.addEventListener('click', () => {
        this.currentCount = window.DalelCards.loadMore(
          'listingsGrid',
          this.currentCount,
          true
        );

        // Hide button if max reached
        if (this.currentCount >= 18) {
          loadMoreBtn.style.display = 'none';
        }
      });
    }
  },

  /**
   * Update listings count display
   */
  updateCount() {
    const countEl = document.getElementById('listingsCount');
    if (countEl && window.DalelCards) {
      countEl.textContent = `(${window.DalelCards.listingsData.length} إعلان)`;
    }
  }
};

// Export for use in other modules
window.DalelListingsPage = ListingsPage;

