/**
 * Temporary Debug Script for Search Bar
 * This helps identify why the search is not working
 */

console.log('=== SEARCH DEBUG SCRIPT LOADED (top level) ===');
console.log('site_url at top level:', typeof site_url !== 'undefined' ? site_url : 'NOT DEFINED');
console.log('jQuery at top level:', typeof $ !== 'undefined' ? 'YES' : 'NO');

(function() {
    'use strict';
    
    console.log('=== SEARCH DEBUG IIFE ===');
    
    // Check immediately
    console.log('site_url:', typeof site_url !== 'undefined' ? site_url : 'NOT DEFINED');
    console.log('window.location:', window.location.href);
    console.log('jQuery available:', typeof $ !== 'undefined');
    
    // Wait for DOM to be ready
    if (typeof $ !== 'undefined') {
        $(document).ready(function() {
            console.log('=== DOM Ready ===');
            console.log('jQuery version:', $.fn.jquery);
            console.log('Select2 available:', typeof $.fn.select2 !== 'undefined');
            console.log('site_url in DOM ready:', typeof site_url !== 'undefined' ? site_url : 'NOT DEFINED');
            
            // Check if search element exists
            const $searchElement = $('.js-data-example-ajaxccsearch');
            console.log('Search element found:', $searchElement.length);
            
            if ($searchElement.length > 0) {
                console.log('Search element HTML:', $searchElement[0].outerHTML);
                console.log('Has Select2:', $searchElement.hasClass('select2-hidden-accessible'));
                
                // Try to see if Select2 is initialized
                if ($searchElement.hasClass('select2-hidden-accessible')) {
                    console.log('Select2 is initialized');
                } else {
                    console.log('Select2 is NOT initialized - will wait and check again');
                    
                    // Check again after a delay
                    setTimeout(function() {
                        console.log('=== Checking Select2 again after 3 seconds ===');
                        console.log('Has Select2 now:', $searchElement.hasClass('select2-hidden-accessible'));
                    }, 3000);
                }
            }
            
            // Test manual AJAX call to search endpoint
            setTimeout(function() {
                console.log('=== Testing search endpoint ===');
                const baseUrl = typeof site_url !== 'undefined' ? site_url : window.location.origin;
                const testUrl = baseUrl + '/clients/get-allclients?q=test';
                console.log('Base URL:', baseUrl);
                console.log('Full Test URL:', testUrl);
                
                $.ajax({
                    url: testUrl,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(data) {
                        console.log('✓ Search endpoint SUCCESS!');
                        console.log('Response data:', data);
                        console.log('Number of items:', data.items ? data.items.length : 'no items');
                    },
                    error: function(xhr, status, error) {
                        console.error('✗ Search endpoint ERROR!');
                        console.error('Status:', status);
                        console.error('Error:', error);
                        console.error('Status code:', xhr.status);
                        console.error('Response text:', xhr.responseText);
                        console.error('Response:', xhr);
                    }
                });
            }, 2000);
        });
    } else {
        console.error('jQuery is not available!');
    }
})();

