/**
 * ============================================================
 * DALEL - New Listing Page Module
 * ============================================================
 */

const NewListingPage = {
  textarea: null,
  charCount: null,
  featuredToggle: null,
  featuredCheckbox: null,
  listingForm: null,
  formCard: null,
  successState: null,
  isFeatured: false,

  /**
   * Initialize new listing page
   */
  init() {
    this.textarea = document.getElementById('descTextarea');
    this.charCount = document.getElementById('charCount');
    this.featuredToggle = document.getElementById('featuredToggle');
    this.featuredCheckbox = document.getElementById('featuredCheckbox');
    this.listingForm = document.getElementById('listingForm');
    this.formCard = document.getElementById('formCard');
    this.successState = document.getElementById('successState');

    this.initCharCounter();
    this.initFeaturedToggle();
    this.initForm();

    console.log('➕ New listing page initialized');
  },

  /**
   * Initialize character counter
   */
  initCharCounter() {
    if (!this.textarea || !this.charCount) return;

    this.textarea.addEventListener('input', () => {
      this.charCount.textContent = this.textarea.value.length;
    });
  },

  /**
   * Initialize featured toggle
   */
  initFeaturedToggle() {
    if (!this.featuredToggle || !this.featuredCheckbox) return;

    this.featuredToggle.addEventListener('click', () => {
      this.isFeatured = !this.isFeatured;
      this.featuredCheckbox.classList.toggle('p-new-listing__featured-checkbox--checked', this.isFeatured);
    });
  },

  /**
   * Initialize form submission
   */
  initForm() {
    if (!this.listingForm) return;

    this.listingForm.addEventListener('submit', (e) => {
      e.preventDefault();
      this.handleSubmit();
    });
  },

  /**
   * Handle form submission
   */
  handleSubmit() {
    // Hide form content
    const header = document.querySelector('.p-new-listing__card-header');
    if (header) header.style.display = 'none';
    if (this.listingForm) this.listingForm.style.display = 'none';

    // Show success
    if (this.successState) {
      this.successState.classList.add('p-new-listing__success--visible');
    }
  }
};

// Export for use in other modules
window.DalelNewListingPage = NewListingPage;

