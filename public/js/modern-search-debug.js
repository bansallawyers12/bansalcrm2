/**
 * Dev-only debug helper for header search (Tom Select).
 */
(function () {
    'use strict';

    function checkSearch() {
        if (typeof window.jQuery === 'undefined') {
            console.log('[Search Debug] jQuery not loaded');
            return;
        }
        var $ = window.jQuery;
        console.log('[Search Debug] Tom Select available:', typeof TomSelect !== 'undefined');
        console.log('[Search Debug] initTomSelect available:', typeof initTomSelect === 'function');

        var $searchElement = $('.search-element select, .search-element .js-data-example-ajaxccsearch');
        if (!$searchElement.length) {
            console.log('[Search Debug] No search element found');
            return;
        }

        console.log('[Search Debug] Search element count:', $searchElement.length);
        $searchElement.each(function (i, el) {
            console.log('[Search Debug] Element', i, 'tomselect:', !!el.tomselect, 'classes:', el.className);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkSearch);
    } else {
        checkSearch();
    }
})();
