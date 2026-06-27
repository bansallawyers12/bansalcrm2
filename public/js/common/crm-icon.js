/**
 * Client-side icon helper — mirrors App\Helpers\IconHelper for dynamic HTML.
 * Lucide: replace crmIcon() body and implement refreshCrmIcons() with createIcons().
 */
(function (global) {
    'use strict';

    var STYLE_PREFIX = {
        solid: 'fas',
        fas: 'fas',
        regular: 'far',
        far: 'far',
        brands: 'fab',
        fab: 'fab',
    };

    var MODIFIERS = {
        'fa-spin': true,
        'fa-pulse': true,
        'fa-fw': true,
        'fa-xs': true,
        'fa-sm': true,
        'fa-lg': true,
        'fa-1x': true,
        'fa-2x': true,
        'fa-3x': true,
    };

    function normalizeName(name) {
        if (!name) {
            return 'fa-tag';
        }
        return name.indexOf('fa-') === 0 ? name : 'fa-' + name;
    }

    function stylePrefix(style) {
        return STYLE_PREFIX[style] || 'fas';
    }

    function looksLikeClassString(value) {
        return /\bfa[srb]?\s+fa-|\bfa-solid\s+fa-|\bfa-regular\s+fa-|\bfa-brands\s+fa-/.test(value);
    }

    function escapeAttr(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;');
    }

    function buildClasses(name, style, options) {
        options = options || {};
        var classes;

        if (looksLikeClassString(name)) {
            classes = name.trim().split(/\s+/).filter(Boolean);
        } else {
            classes = [stylePrefix(style), normalizeName(name)];
        }

        if (options.spin) {
            classes.push('fa-spin', 'icon-spin');
        }

        if (options.size) {
            classes.push(options.size.indexOf('fa-') === 0 ? options.size : 'fa-' + options.size);
        }

        if (options.class) {
            options.class.split(/\s+/).forEach(function (part) {
                if (part) {
                    classes.push(part);
                }
            });
        }

        return classes.filter(function (value, index, list) {
            return list.indexOf(value) === index;
        });
    }

    function crmIcon(name, style, options) {
        if (typeof style === 'object' && style !== null) {
            options = style;
            style = 'solid';
        }

        style = style || 'solid';
        options = options || {};

        var classes = buildClasses(name, style, options);
        var attrs = options.attrs || {};
        var html = '<i class="' + escapeAttr(classes.join(' ')) + '" aria-hidden="true"';

        Object.keys(attrs).forEach(function (key) {
            if (key === 'class' || attrs[key] == null || attrs[key] === '') {
                return;
            }
            html += ' ' + key + '="' + escapeAttr(attrs[key]) + '"';
        });

        return html + '></i>';
    }

    function crmIconFromClass(classString, options) {
        return crmIcon(classString, 'solid', options);
    }

    function crmIconStored(stored, options) {
        options = options || {};
        stored = (stored || '').trim();

        if (!stored) {
            return crmIcon('tag', 'solid', options);
        }

        return crmIcon(stored, 'solid', options);
    }

    function crmIconSpinner(label) {
        return crmIcon('spinner', 'solid', { spin: true, class: 'icon-spin' }) + (label ? ' ' + label : '');
    }

    /** Placeholder for Lucide createIcons() after AJAX DOM updates. */
    function refreshCrmIcons(root) {
        void root;
    }

    global.crmIcon = crmIcon;
    global.crmIconFromClass = crmIconFromClass;
    global.crmIconStored = crmIconStored;
    global.crmIconSpinner = crmIconSpinner;
    global.refreshCrmIcons = refreshCrmIcons;
})(window);
