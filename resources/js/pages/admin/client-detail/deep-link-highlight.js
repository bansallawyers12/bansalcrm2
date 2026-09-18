/**
 * Scroll to and briefly highlight #activity_{id}, #note_id_{id}, or #app_stage_log_{id}
 * from the URL hash. Safe no-op when hash is absent or the target never appears.
 * Scrolls the target to the top of the viewport (block: start).
 */

const HASH_RE = /^#(activity_\d+|note_id_\d+|app_stage_log_\d+)$/;
const HIGHLIGHT_CLASS = 'deep-link-highlight';
const STYLE_ID = 'deep-link-highlight-style';
const MAX_ATTEMPTS = 40;
const RETRY_MS = 250;

let started = false;

function ensureStyles() {
    if (document.getElementById(STYLE_ID)) {
        return;
    }
    const style = document.createElement('style');
    style.id = STYLE_ID;
    style.textContent =
        `[id^="activity_"],[id^="note_id_"],[id^="app_stage_log_"]{scroll-margin-top:5rem;}` +
        `.${HIGHLIGHT_CLASS}{` +
        'outline:2px solid #f59e0b;outline-offset:3px;border-radius:6px;' +
        'transition:outline-color .4s ease,background-color .4s ease;' +
        'background-color:rgba(245,158,11,.12)!important;' +
        '}' +
        '@keyframes deep-link-flash{0%{background-color:rgba(245,158,11,.28)}100%{background-color:transparent}}' +
        `.${HIGHLIGHT_CLASS}{animation:deep-link-flash 2.4s ease-out;}`;
    document.head.appendChild(style);
}

function targetIdFromHash() {
    const hash = window.location.hash || '';
    if (!HASH_RE.test(hash)) {
        return null;
    }
    return hash.slice(1);
}

/**
 * Expand Bootstrap collapse ancestors so AJAX-loaded stage rows become visible.
 */
function revealAncestors(el) {
    let node = el.parentElement;
    while (node && node !== document.body) {
        if (node.classList && node.classList.contains('collapse') && !node.classList.contains('show')) {
            const Collapse = window.bootstrap && window.bootstrap.Collapse;
            if (Collapse) {
                Collapse.getOrCreateInstance(node, { toggle: false }).show();
            } else {
                node.classList.add('show');
                node.style.height = '';
                node.style.display = '';
            }
            const toggle = document.querySelector(
                `[data-bs-target="#${node.id}"], [href="#${node.id}"]`
            );
            if (toggle) {
                toggle.classList.remove('collapsed');
                toggle.setAttribute('aria-expanded', 'true');
                const header = toggle.closest('.accordion-header');
                if (header) {
                    header.classList.remove('collapsed');
                }
            }
        }
        node = node.parentElement;
    }
}

function highlight(el) {
    ensureStyles();
    revealAncestors(el);
    el.classList.add(HIGHLIGHT_CLASS);
    // Defer scroll until collapse expand has started painting.
    window.requestAnimationFrame(() => {
        if (typeof el.scrollIntoView === 'function') {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
    window.setTimeout(() => {
        el.classList.remove(HIGHLIGHT_CLASS);
    }, 2800);
}

function tryHighlight(attempt) {
    const id = targetIdFromHash();
    if (!id) {
        return;
    }
    const el = document.getElementById(id);
    if (el) {
        highlight(el);
        return;
    }
    if (attempt >= MAX_ATTEMPTS) {
        return;
    }
    window.setTimeout(() => {
        tryHighlight(attempt + 1);
    }, RETRY_MS);
}

export function initActivityDeepLinkHighlight() {
    if (started) {
        return;
    }
    started = true;

    const start = () => {
        if (!targetIdFromHash()) {
            return;
        }
        tryHighlight(0);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }

    window.addEventListener('hashchange', () => {
        tryHighlight(0);
    });
}

initActivityDeepLinkHighlight();
