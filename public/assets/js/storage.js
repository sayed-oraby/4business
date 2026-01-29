/**
 * ============================================================
 * DALEL - Storage Module
 * LocalStorage helpers for persisting data
 * ============================================================
 */

const Storage = {
  /**
   * Get item from localStorage
   * @param {string} key 
   * @param {*} defaultValue 
   * @returns {*}
   */
  get(key, defaultValue = null) {
    try {
      const item = localStorage.getItem(`dalel_${key}`);
      return item ? JSON.parse(item) : defaultValue;
    } catch (e) {
      console.warn('Storage.get error:', e);
      return defaultValue;
    }
  },

  /**
   * Set item in localStorage
   * @param {string} key 
   * @param {*} value 
   */
  set(key, value) {
    try {
      localStorage.setItem(`dalel_${key}`, JSON.stringify(value));
    } catch (e) {
      console.warn('Storage.set error:', e);
    }
  },

  /**
   * Remove item from localStorage
   * @param {string} key 
   */
  remove(key) {
    try {
      localStorage.removeItem(`dalel_${key}`);
    } catch (e) {
      console.warn('Storage.remove error:', e);
    }
  },

  /**
   * Clear all dalel_ prefixed items
   */
  clear() {
    try {
      Object.keys(localStorage)
        .filter(key => key.startsWith('dalel_'))
        .forEach(key => localStorage.removeItem(key));
    } catch (e) {
      console.warn('Storage.clear error:', e);
    }
  }
};

// Export for use in other modules
window.DalelStorage = Storage;

