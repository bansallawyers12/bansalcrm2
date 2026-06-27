/**
 * Hydrate data-lucide placeholders and column-sort icons after page load / AJAX.
 */
'use strict';

import { createIcons } from 'lucide';

const SORT_ICON_MAP = {
    'sort-default': 'arrow-up-down',
    'sort-alpha': 'arrow-up-down',
    'sort-alpha-asc': 'arrow-down-a',
    'sort-alpha-desc': 'arrow-up-a',
    'sort-amount': 'arrow-up-down',
    'sort-amount-asc': 'arrow-down-wide-narrow',
    'sort-amount-desc': 'arrow-up-wide-narrow',
    'sort-numeric': 'arrow-up-down',
    'sort-numeric-asc': 'arrow-down-1-0',
    'sort-numeric-desc': 'arrow-up-1-0',
};

function resolveSortLucideName(className) {
    const parts = className.split(/\s+/).filter(Boolean);
    const sortClass = parts.find(function (part) {
        return part.indexOf('sort-') === 0;
    });

    if (!sortClass) {
        return 'arrow-up-down';
    }

    return SORT_ICON_MAP[sortClass]
        || SORT_ICON_MAP[sortClass.replace(/-asc$/, '').replace(/-desc$/, '')]
        || 'arrow-up-down';
}

function hydrateSortIcons(root) {
    const scope = root || document;
    scope.querySelectorAll('th i[class*="sort-"]').forEach(function (el) {
        if (el.getAttribute('data-lucide')) {
            return;
        }
        el.classList.add('crm-icon');
        el.setAttribute('data-lucide', resolveSortLucideName(el.className));
    });
}

export function refreshCrmIcons(root) {
    hydrateSortIcons(root);
    createIcons({
        attrs: {
            'aria-hidden': 'true',
        },
        root: root || document,
    });
}

if (typeof window !== 'undefined') {
    window.refreshCrmIcons = refreshCrmIcons;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            refreshCrmIcons();
        });
    } else {
        refreshCrmIcons();
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.jQuery === 'undefined') {
            return;
        }
        window.jQuery(document).ajaxComplete(function (_event, _xhr, settings) {
            if (!settings || (settings.dataType && settings.dataType !== 'html')) {
                return;
            }
            refreshCrmIcons();
        });
    });
}
