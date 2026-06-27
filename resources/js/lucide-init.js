/**
 * Hydrate data-lucide placeholders and column-sort icons after page load / AJAX.
 */
'use strict';

import { createIcons, icons } from 'lucide';

const SORT_ICON_MAP = {
    'sort-default': 'arrow-up-down',
    'sort-alpha': 'arrow-up-down',
    'sort-alpha-asc': 'arrow-down-a-z',
    'sort-alpha-desc': 'arrow-up-a-z',
    'sort-amount': 'arrow-up-down',
    'sort-amount-asc': 'arrow-down-wide-narrow',
    'sort-amount-desc': 'arrow-up-wide-narrow',
    'sort-numeric': 'arrow-up-down',
    'sort-numeric-asc': 'arrow-down-1-0',
    'sort-numeric-desc': 'arrow-up-1-0',
};

let refreshTimer = null;

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
    scope.querySelectorAll('th i[class*="sort-"], th i.sort-default').forEach(function (el) {
        if (el.getAttribute('data-lucide')) {
            return;
        }
        el.classList.add('crm-icon');
        el.setAttribute('data-lucide', resolveSortLucideName(el.className));
    });
}

function nodeNeedsIconHydration(node) {
    if (!node || node.nodeType !== 1) {
        return false;
    }

    if (node.matches && (node.matches('[data-lucide]') || node.matches('i[class*="sort-"]'))) {
        return true;
    }

    return !!(node.querySelector && node.querySelector('[data-lucide], th i[class*="sort-"]'));
}

export function refreshCrmIcons(root) {
    hydrateSortIcons(root);
    createIcons({
        icons,
        attrs: {
            'aria-hidden': 'true',
        },
        root: root || document,
    });
}

function scheduleRefreshCrmIcons(root) {
    clearTimeout(refreshTimer);
    refreshTimer = setTimeout(function () {
        refreshCrmIcons(root);
    }, 16);
}

function setupDynamicIconObserver() {
    if (typeof MutationObserver === 'undefined' || !document.body) {
        return;
    }

    const observer = new MutationObserver(function (mutations) {
        for (let i = 0; i < mutations.length; i++) {
            const mutation = mutations[i];
            if (mutation.type !== 'childList') {
                continue;
            }

            for (let j = 0; j < mutation.addedNodes.length; j++) {
                if (nodeNeedsIconHydration(mutation.addedNodes[j])) {
                    scheduleRefreshCrmIcons();
                    return;
                }
            }
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });
}

if (typeof window !== 'undefined') {
    window.refreshCrmIcons = refreshCrmIcons;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            refreshCrmIcons();
            setupDynamicIconObserver();
        });
    } else {
        refreshCrmIcons();
        setupDynamicIconObserver();
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.jQuery === 'undefined') {
            return;
        }
        window.jQuery(document).ajaxComplete(function (_event, _xhr, settings) {
            if (!settings || (settings.dataType && settings.dataType !== 'html')) {
                return;
            }
            scheduleRefreshCrmIcons();
        });
    });
}
