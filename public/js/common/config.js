/**
 * Application Configuration Module
 * 
 * This file provides access to global and page-specific configuration
 * that is set by Blade templates.
 * 
 * Usage:
 *   App.getUrl('deleteAction')
 *   App.getCsrf()
 *   App.getAsset('imgDocuments')
 */

'use strict';

// Ensure AppConfig and PageConfig exist (set by Blade templates)
window.AppConfig = window.AppConfig || {};
window.PageConfig = window.PageConfig || {};

/**
 * Application helper object
 */
const App = {
    /**
     * Get URL by key from AppConfig.urls
     * @param {string} key - URL key name
     * @returns {string} URL or empty string if not found
     */
    getUrl: function(key) {
        return AppConfig.urls && AppConfig.urls[key] ? AppConfig.urls[key] : '';
    },

    /**
     * Get CSRF token
     * @returns {string} CSRF token
     */
    getCsrf: function() {
        return AppConfig.csrf || '';
    },

    /**
     * Get asset path by key from AppConfig.assets
     * @param {string} key - Asset key name
     * @returns {string} Asset path or empty string if not found
     */
    getAsset: function(key) {
        return AppConfig.assets && AppConfig.assets[key] ? AppConfig.assets[key] : '';
    },

    /**
     * Get page-specific config value
     * @param {string} key - Config key name
     * @returns {*} Config value or null if not found
     */
    getPageConfig: function(key) {
        return PageConfig[key] !== undefined ? PageConfig[key] : null;
    }
};

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = App;
}

