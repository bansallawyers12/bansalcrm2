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

/** Repair stale/corrupt data-lucide values from cached HTML or unmapped FA slugs. */
const STALE_LUCIDE_FIXES = {
    'ngle-down': 'chevron-down',
    'ngle-left': 'chevron-left',
    'ngle-right': 'chevron-right',
    'rrow-left': 'arrow-left',
    'rrow-right': 'arrow-right',
    'rrow-down': 'arrow-down',
    'ile': 'file',
    'ile-alt': 'file-text',
    'ile-pdf': 'file-text',
    'ile-image': 'file-image',
    'ile-signature': 'signature',
    'rchive': 'archive',
    'ticket-alt': 'ticket',
    'mobile': 'smartphone',
    'refresh': 'refresh-cw',
    'bag': 'shopping-bag',
};

function normalizeDataLucideAttributes(root) {
    const scope = root || document;
    const map = (typeof window !== 'undefined' && window.CRM_FA_LUCIDE_MAP) || {};

    scope.querySelectorAll('[data-lucide]').forEach(function (el) {
        let name = el.getAttribute('data-lucide');
        if (!name) {
            return;
        }

        if (STALE_LUCIDE_FIXES[name]) {
            name = STALE_LUCIDE_FIXES[name];
        } else if (map[name]) {
            name = map[name];
        }

        el.setAttribute('data-lucide', name);
    });
}

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
    normalizeDataLucideAttributes(root);
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
