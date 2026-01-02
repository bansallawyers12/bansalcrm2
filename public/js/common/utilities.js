/**
 * Utilities Module
 * 
 * Common utility functions used across pages:
 * - parseTime: Parse time string to number
 * - convertHours: Convert hours to time string
 * - errorMessage: Format error messages
 * 
 * Usage:
 *   parseTime('09:30')
 *   convertHours(9.5)
 *   errorMessage('Error text')
 */

'use strict';

/**
 * Parse time string to minutes (HH:MM format)
 * @param {string} s - Time string in HH:MM format (e.g., "09:30")
 * @returns {number} Total minutes (e.g., 570 for 09:30)
 */
function parseTime(s) {
    if (!s) return 0;
    var c = s.split(':');
    return parseInt(c[0], 10) * 60 + parseInt(c[1], 10);
}

/**
 * Convert minutes to HH:MM format
 * @param {number} mins - Total minutes (e.g., 570)
 * @returns {string} Time string in HH:MM format (e.g., "09:30")
 */
function convertHours(mins) {
    if (mins === null || mins === undefined) return '00:00';
    var hour = Math.floor(mins / 60);
    var minutes = mins % 60;
    var converted = pad(hour, 2) + ':' + pad(minutes, 2);
    return converted;
}

/**
 * Pad string with leading zeros
 * @param {string|number} str - String or number to pad
 * @param {number} max - Maximum length
 * @returns {string} Padded string
 */
function pad(str, max) {
    str = str.toString();
    return str.length < max ? pad("0" + str, max) : str;
}

/**
 * Validate email address format
 * @param {string} inputText - Email address to validate
 * @returns {boolean} True if valid, false otherwise
 */
function ValidateEmail(inputText) {
    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if (inputText.match(mailformat)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Format error message for display
 * @param {string} message - Error message text
 * @returns {string} Formatted HTML error message
 */
function errorMessage(message) {
    if (!message) {
        return '<div class="alert alert-danger">An error occurred.</div>';
    }
    
    return '<div class="alert alert-danger">' + 
           message.replace(/</g, '&lt;').replace(/>/g, '&gt;') + 
           '</div>';
}

/**
 * Show loading indicator
 */
function showLoader() {
    if ($('#popuploader').length) {
        $('#popuploader').show();
    }
}

/**
 * Hide loading indicator
 */
function hideLoader() {
    if ($('#popuploader').length) {
        $('#popuploader').hide();
    }
}

/**
 * Format date for display
 * @param {string|Date} date - Date to format
 * @param {string} format - Format string (optional)
 * @returns {string} Formatted date
 */
function formatDate(date, format) {
    if (!date) return '';
    
    var d = date instanceof Date ? date : new Date(date);
    if (isNaN(d.getTime())) return '';
    
    format = format || 'd/m/Y';
    
    var day = d.getDate();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();
    
    return format
        .replace('d', day < 10 ? '0' + day : day)
        .replace('m', month < 10 ? '0' + month : month)
        .replace('Y', year);
}

// Export functions for use in other modules
if (typeof window !== 'undefined') {
    window.parseTime = parseTime;
    window.convertHours = convertHours;
    window.pad = pad;
    window.ValidateEmail = ValidateEmail;
    window.errorMessage = errorMessage;
    window.showLoader = showLoader;
    window.hideLoader = hideLoader;
    window.formatDate = formatDate;
}

