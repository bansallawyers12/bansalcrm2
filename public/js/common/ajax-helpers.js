/**
 * AJAX Helper Module
 * 
 * Provides standardized AJAX methods with automatic CSRF token handling
 * 
 * Usage:
 *   AjaxHelper.post(url, data, successCallback, errorCallback)
 *   AjaxHelper.get(url, data, successCallback)
 */

'use strict';

/**
 * AJAX Helper object
 */
const AjaxHelper = {
    /**
     * Perform POST request with CSRF token
     * @param {string} url - Request URL
     * @param {object} data - Data to send
     * @param {function} successCallback - Success callback function
     * @param {function} errorCallback - Error callback function (optional)
     */
    post: function(url, data, successCallback, errorCallback) {
        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': App.getCsrf()
            },
            data: data,
            dataType: 'json',
            success: successCallback || function() {},
            error: errorCallback || function(xhr, status, error) {
                console.error('AJAX POST Error:', error);
                console.error('URL:', url);
                console.error('Response:', xhr.responseText);
            }
        });
    },

    /**
     * Perform GET request
     * @param {string} url - Request URL
     * @param {object} data - Query parameters
     * @param {function} successCallback - Success callback function
     * @param {function} errorCallback - Error callback function (optional)
     */
    get: function(url, data, successCallback, errorCallback) {
        $.ajax({
            url: url,
            type: 'GET',
            data: data,
            dataType: 'json',
            success: successCallback || function() {},
            error: errorCallback || function(xhr, status, error) {
                console.error('AJAX GET Error:', error);
                console.error('URL:', url);
            }
        });
    },

    /**
     * Perform POST request with FormData (for file uploads)
     * @param {string} url - Request URL
     * @param {FormData} formData - FormData object
     * @param {function} successCallback - Success callback function
     * @param {function} errorCallback - Error callback function (optional)
     */
    postFormData: function(url, formData, successCallback, errorCallback) {
        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': App.getCsrf()
            },
            data: formData,
            processData: false,
            contentType: false,
            success: successCallback || function() {},
            error: errorCallback || function(xhr, status, error) {
                console.error('AJAX FormData POST Error:', error);
                console.error('URL:', url);
            }
        });
    }
};

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AjaxHelper;
}

