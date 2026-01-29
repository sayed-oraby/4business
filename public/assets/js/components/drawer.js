/**
 * ============================================================
 * DALEL - Drawer Component
 * Mobile navigation drawer functionality
 * ============================================================
 */

const Drawer = {
  drawer: null,
  overlay: null,
  openBtn: null,
  closeBtn: null,

  /**
   * Initialize drawer component
   */
  init() {
    this.drawer = document.getElementById('drawer');
    this.overlay = document.getElementById('drawerOverlay');
    this.openBtn = document.getElementById('openDrawer');
    this.closeBtn = document.getElementById('closeDrawer');

    if (!this.drawer || !this.overlay) return;

    this.bindEvents();
  },

  /**
   * Bind event listeners
   */
  bindEvents() {
    if (this.openBtn) {
      this.openBtn.addEventListener('click', () => this.open());
    }

    if (this.closeBtn) {
      this.closeBtn.addEventListener('click', () => this.close());
    }

    if (this.overlay) {
      this.overlay.addEventListener('click', () => this.close());
    }

    // Close on ESC key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.close();
      }
    });
  },

  /**
   * Open drawer
   */
  open() {
    if (!this.drawer || !this.overlay) return;
    
    this.drawer.classList.add('c-drawer--active');
    this.overlay.classList.add('c-drawer-overlay--active');
    document.body.style.overflow = 'hidden';
  },

  /**
   * Close drawer
   */
  close() {
    if (!this.drawer || !this.overlay) return;
    
    this.drawer.classList.remove('c-drawer--active');
    this.overlay.classList.remove('c-drawer-overlay--active');
    document.body.style.overflow = '';
  },

  /**
   * Toggle drawer
   */
  toggle() {
    if (this.drawer?.classList.contains('c-drawer--active')) {
      this.close();
    } else {
      this.open();
    }
  }
};

// Export for use in other modules
window.DalelDrawer = Drawer;

