/**
 * Modern Search Implementation
 * Features: Debouncing, Keyboard Shortcuts, Category Grouping, Highlighting
 */

(function() {
    'use strict';

    // Debounce function
    function debounce(func, delay = 300) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Initialize search with modern features
    function initModernSearch() {
        const searchSelector = '.js-data-example-ajaxccsearch';
        const $searchElement = $(searchSelector);

        if (!$searchElement.length) {
            return;
        }

        // Initialize Select2 with modern config
        $searchElement.select2({
            closeOnSelect: true,
            placeholder: 'Search clients, leads, partners... (Ctrl+K)',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: (typeof site_url !== 'undefined' ? site_url : '') + '/admin/clients/get-allclients',
                dataType: 'json',
                delay: 300, // Debounce built into Select2
                processResults: function(data) {
                    // Group results by category
                    const grouped = groupResultsByCategory(data.items);
                    return {
                        results: grouped
                    };
                },
                cache: true,
                error: function(xhr, status, error) {
                    console.error('Search error:', error);
                    return {
                        results: []
                    };
                }
            },
            templateResult: formatSearchResult,
            templateSelection: formatSearchSelection,
            escapeMarkup: function(markup) {
                return markup; // Allow HTML
            }
        });

        // Keyboard shortcut: Ctrl+K or Cmd+K
        $(document).on('keydown', function(e) {
            // Ctrl+K (Windows/Linux) or Cmd+K (Mac)
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                $searchElement.select2('open');
                setTimeout(() => {
                    $('.select2-search__field').focus();
                }, 100);
            }

            // ESC to close
            if (e.key === 'Escape') {
                $searchElement.select2('close');
            }
        });

        // Handle selection and navigation
        $searchElement.on('select2:select', function(e) {
            const data = e.params.data;
            navigateToResult(data);
        });

        // Clear selection on close
        $searchElement.on('select2:close', function() {
            setTimeout(() => {
                $(this).val(null).trigger('change');
            }, 100);
        });
    }

    // Group results by category
    function groupResultsByCategory(items) {
        if (!items || items.length === 0) {
            return [];
        }

        const categories = {
            clients: { text: 'CLIENTS', children: [] },
            leads: { text: 'LEADS', children: [] },
            partners: { text: 'PARTNERS', children: [] },
            products: { text: 'PRODUCTS', children: [] },
            applications: { text: 'APPLICATIONS', children: [] }
        };

        items.forEach(item => {
            const category = item.category || 'clients';
            if (categories[category]) {
                categories[category].children.push(item);
            }
        });

        // Only return categories that have results
        const result = [];
        Object.keys(categories).forEach(key => {
            if (categories[key].children.length > 0) {
                result.push(categories[key]);
            }
        });

        return result.length > 0 ? result : items;
    }

    // Format search result item
    function formatSearchResult(repo) {
        if (repo.loading) {
            return repo.text;
        }

        // If it's a category header
        if (repo.children) {
            return $('<strong class="select2-category-header">' + repo.text + '</strong>');
        }

        // Show client ID as blue badge before name
        const clientId = repo.client_id ? `<span style="color: #007bff; font-weight: 600; margin-right: 8px;">#${repo.client_id}</span>` : '';
        
        // Build additional details array for subtitle
        const details = [];
        
        if (repo.email) {
            details.push(repo.email);
        }
        
        if (repo.phone) {
            details.push(`Phone: ${repo.phone}`);
        }
        
        // Join details with separator
        const detailsText = details.join(' • ');

        const $container = $(`
            <div class="select2-result-repository modern-search-result">
                <div class="modern-search-result-content">
                    <div class="modern-search-result-main">
                        <div class="modern-search-result-title">${clientId}${repo.name || repo.text}</div>
                        <div class="modern-search-result-subtitle">${detailsText}</div>
                    </div>
                </div>
            </div>
        `);

        return $container;
    }

    // Format selected item
    function formatSearchSelection(repo) {
        return repo.name || repo.text || 'Search...';
    }

    // Navigate to selected result
    function navigateToResult(data) {
        if (!data.id) {
            return;
        }

        const siteUrl = (typeof site_url !== 'undefined' ? site_url : '');
        const parts = data.id.split('/');
        const type = parts[1];  //alert('type='+type);
        const id = parts[0];  //alert('id='+id);

        let url = '';

        switch (type) {
            case 'Client':
                 // Both clients and leads (old and new) route to client detail page
                 url = siteUrl + '/admin/clients/detail/' + id;
                 break;
            case 'Lead':
                // Both clients and leads (old and new) route to client detail page
                url = siteUrl + '/admin/leads/detail/' + id;
                break;
            case 'Partner':
                url = siteUrl + '/admin/partners/detail/' + id;
                break;
            case 'Product':
                url = siteUrl + '/admin/products/detail/' + id;
                break;
            case 'Application':
                url = siteUrl + '/admin/applications/detail/' + id;
                break;
            default:
                console.warn('Unknown result type:', type);
                return;
        }

        if (url) {
            window.location.href = url;
        }
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        initModernSearch();
    });

    // Re-initialize on Turbolinks/AJAX page loads if needed
    $(document).on('turbolinks:load', function() {
        initModernSearch();
    });

})();

