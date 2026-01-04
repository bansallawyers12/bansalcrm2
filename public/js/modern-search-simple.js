/**
 * Simplified Modern Search - Direct approach without complex async loading
 */

console.log('[Search] Script loaded at:', new Date().toISOString());

// Run immediately when jQuery and Select2 are available
(function checkAndInit() {
    console.log('[Search] Checking dependencies...');
    console.log('[Search] jQuery available:', typeof $ !== 'undefined');
    console.log('[Search] Select2 available:', typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined');
    
    if (typeof $ === 'undefined') {
        console.log('[Search] jQuery not ready, waiting...');
        setTimeout(checkAndInit, 100);
        return;
    }
    
    if (typeof $.fn.select2 === 'undefined') {
        console.log('[Search] Select2 not ready, retrying in 100ms...');
        setTimeout(checkAndInit, 100);
        return;
    }
    
    console.log('[Search] ✓ jQuery and Select2 are ready!');
    
    // If DOM is already ready, init immediately, otherwise wait
    if (document.readyState === 'loading') {
        console.log('[Search] DOM still loading, waiting for DOMContentLoaded...');
        $(document).ready(function() {
            console.log('[Search] DOM ready, initializing now...');
            initSearch();
        });
    } else {
        console.log('[Search] DOM already ready, initializing now...');
        initSearch();
    }
})();

function initSearch() {
    console.log('[Search] === initSearch() called ===');
    
    const $searchElement = $('.js-data-example-ajaxccsearch');
    
    console.log('[Search] Looking for element with class: .js-data-example-ajaxccsearch');
    console.log('[Search] Elements found:', $searchElement.length);
    
    if (!$searchElement.length) {
        console.error('[Search] ✗ Search element not found in DOM!');
        console.log('[Search] Available search-related elements:', $('.search-element').length);
        return;
    }
    
    console.log('[Search] ✓ Search element found!');
    console.log('[Search] Element HTML:', $searchElement[0].outerHTML.substring(0, 200));
    
    if ($searchElement.hasClass('select2-hidden-accessible')) {
        console.log('[Search] Already initialized, skipping');
        return;
    }
    
    console.log('[Search] Calling select2()...');
    
    try {
        $searchElement.select2({
            placeholder: 'Search clients, leads, partners...',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: function() {
                    const baseUrl = typeof site_url !== 'undefined' ? site_url : window.location.origin;
                    const url = baseUrl + '/clients/get-allclients';
                    console.log('[Search] Ajax URL:', url);
                    return url;
                },
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    console.log('[Search] Searching for:', params.term);
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    console.log('[Search] Got results:', data);
                    if (!data.items || data.items.length === 0) {
                        return { results: [] };
                    }
                    return { results: data.items };
                },
                error: function(xhr, status, error) {
                    console.error('[Search] Ajax error:', {
                        status: status,
                        error: error,
                        xhr: xhr,
                        responseText: xhr.responseText
                    });
                }
            },
            templateResult: function(item) {
                if (item.loading) return item.text;
                
                const name = item.name || item.text || 'Unknown';
                const email = item.email || '';
                const clientId = item.client_id ? '#' + item.client_id : '';
                
                return $('<div>' + clientId + ' ' + name + '<br><small>' + email + '</small></div>');
            },
            templateSelection: function(item) {
                return item.name || item.text || 'Search...';
            }
        });
        
        console.log('[Search] ✓✓✓ Select2 initialized successfully! ✓✓✓');
        console.log('[Search] Search box should now be visible and functional');
        
        // Verify initialization
        setTimeout(function() {
            if ($searchElement.hasClass('select2-hidden-accessible')) {
                console.log('[Search] ✓ Verification: Select2 is active');
                console.log('[Search] ✓ Search box is ready for use');
            } else {
                console.error('[Search] ✗ Verification failed: Select2 not active');
            }
        }, 500);
        
        // Handle selection
        $searchElement.on('select2:select', function(e) {
            const data = e.params.data;
            console.log('[Search] Selected:', data);
            
            if (data.id) {
                const parts = data.id.split('/');
                const id = parts[0];
                const type = parts[1];
                const baseUrl = typeof site_url !== 'undefined' ? site_url : window.location.origin;
                
                let url = '';
                if (type === 'Client') {
                    url = baseUrl + '/clients/detail/' + id;
                } else if (type === 'Lead') {
                    url = baseUrl + '/leads/detail/' + id;
                }
                
                console.log('[Search] Navigating to:', url);
                if (url) {
                    window.location.href = url;
                }
            }
        });
        
        // Clear on close
        $searchElement.on('select2:close', function() {
            setTimeout(() => {
                $(this).val(null).trigger('change');
            }, 100);
        });
        
    } catch (err) {
        console.error('[Search] Error initializing:', err);
    }
}

