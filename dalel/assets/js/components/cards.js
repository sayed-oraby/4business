/**
 * ============================================================
 * DALEL - Cards Component
 * Listing card rendering and interactions
 * ============================================================
 */

const Cards = {
  /**
   * Dummy listings data
   */
  listingsData: [
    {
      id: 1,
      title: 'شقة للإيجار في حولي',
      description: 'شقة مميزة 3 غرف وصالة ومطبخ و2 حمام في موقع ممتاز قريب من الخدمات',
      price: '350 د.ك',
      time: '2 ساعة',
      featured: true,
      type: 'rent',
      category: 'apartment',
      area: 'hawalli'
    },
    {
      id: 2,
      title: 'بيت للبيع في صباح السالم',
      description: 'بيت للبيع بمنطقة صباح السالم التجارية بمساحة 400 متر - 5 غرف 4 حمامات',
      price: '150,000 د.ك',
      time: '4 ساعة',
      featured: true,
      type: 'sale',
      category: 'house',
      area: 'mubarak'
    },
    {
      id: 3,
      title: 'شقة للإيجار في السالمية',
      description: 'شقة فاخرة 2 غرفة نوم - إطلالة بحرية - موقف خاص - أمن 24 ساعة',
      price: '450 د.ك',
      time: '6 ساعة',
      featured: false,
      type: 'rent',
      category: 'apartment',
      area: 'hawalli'
    },
    {
      id: 4,
      title: 'أرض للبيع في الجهراء',
      description: 'أرض سكنية مساحة 500 متر - زاوية - موقع مميز - قريبة من الخدمات',
      price: '85,000 د.ك',
      time: '8 ساعة',
      featured: false,
      type: 'sale',
      category: 'land',
      area: 'jahra'
    },
    {
      id: 5,
      title: 'شقة للإيجار في الفروانية',
      description: 'شقة واسعة 3 غرف - مدخل خاص - موقف سيارة - قريبة من المدارس',
      price: '280 د.ك',
      time: '10 ساعة',
      featured: true,
      type: 'rent',
      category: 'apartment',
      area: 'farwaniya'
    },
    {
      id: 6,
      title: 'عمارة للبيع في حولي',
      description: 'عمارة استثمارية - 12 شقة - دخل ممتاز - موقع تجاري',
      price: '800,000 د.ك',
      time: '12 ساعة',
      featured: false,
      type: 'sale',
      category: 'building',
      area: 'hawalli'
    },
    {
      id: 7,
      title: 'شاليه للبيع في الخيران',
      description: 'شاليه فاخر - مسبح خاص - 4 غرف نوم - إطلالة على البحر',
      price: '120,000 د.ك',
      time: '1 يوم',
      featured: true,
      type: 'sale',
      category: 'chalet',
      area: 'ahmadi'
    },
    {
      id: 8,
      title: 'شقة للبدل في المنقف',
      description: 'شقة للبدل - 2 غرفة - صالة كبيرة - مطلوب بدل في حولي أو السالمية',
      price: 'للبدل',
      time: '1 يوم',
      featured: false,
      type: 'exchange',
      category: 'apartment',
      area: 'ahmadi'
    },
    {
      id: 9,
      title: 'بيت للإيجار في العاصمة',
      description: 'بيت حكومي للإيجار - 4 غرف - حديقة - موقف سيارتين',
      price: '1,200 د.ك',
      time: '2 يوم',
      featured: false,
      type: 'rent',
      category: 'house',
      area: 'capital'
    }
  ],

  /**
   * Create a listing card HTML
   * @param {Object} listing - Listing data object
   * @param {boolean} horizontal - Whether to use horizontal layout
   * @returns {string} HTML string
   */
  createCard(listing, horizontal = false) {
    const featuredClass = listing.featured ? 'c-card--featured' : '';
    const horizontalClass = horizontal ? 'c-card--horizontal' : '';

    // Get correct image path based on page location
    const imgPath = window.location.pathname.includes('/pages/') ? '../assets/img/ad.png' : 'assets/img/ad.png';

    return `
      <article class="c-card ${featuredClass} ${horizontalClass}" data-id="${listing.id}">
        <div class="c-card__image">
          <img src="${imgPath}" alt="${listing.title}" class="c-card__img">
          ${listing.featured ? '<span class="c-card__tag">مميز</span>' : ''}
        </div>
        <div class="c-card__content">
          <h3 class="c-card__title">${listing.title}</h3>
          <p class="c-card__desc">${listing.description}</p>
          <div class="c-card__footer">
            <span class="c-card__price">${listing.price}</span>
            <span class="c-card__meta">
              <svg class="c-card__meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
              </svg>
              ${listing.time}
            </span>
          </div>
        </div>
      </article>
    `;
  },

  /**
   * Render listings to a container
   * @param {string} containerId - Container element ID
   * @param {number} count - Number of listings to show
   * @param {boolean} horizontal - Whether to use horizontal layout
   */
  render(containerId, count = 6, horizontal = false) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const listingsToShow = this.listingsData.slice(0, count);
    container.innerHTML = listingsToShow.map(listing =>
      this.createCard(listing, horizontal)
    ).join('');

    // Add click handlers
    this.bindCardClicks(container);
  },

  /**
   * Bind click handlers to cards
   * @param {HTMLElement} container
   */
  bindCardClicks(container) {
    container.querySelectorAll('.c-card').forEach(card => {
      card.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        console.log('Clicked listing:', id);
        // Navigate to detail page
        // window.location.href = `ad.html?id=${id}`;
      });
    });
  },

  /**
   * Load more listings
   * @param {string} containerId
   * @param {number} currentCount
   * @param {boolean} horizontal
   * @returns {number} New count
   */
  loadMore(containerId, currentCount, horizontal = false) {
    const container = document.getElementById(containerId);
    if (!container) return currentCount;

    const newCount = currentCount + 6;
    const moreListings = this.listingsData.concat(this.listingsData).slice(0, newCount);

    container.innerHTML = moreListings.map(listing =>
      this.createCard(listing, horizontal)
    ).join('');

    this.bindCardClicks(container);
    return newCount;
  }
};

// Export for use in other modules
window.DalelCards = Cards;

