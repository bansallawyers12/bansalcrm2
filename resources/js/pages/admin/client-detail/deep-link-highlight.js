/**
 * Scroll to and briefly highlight #activity_{id} or #note_id_{id} from the URL hash.
 * Safe no-op when hash is absent or the target never appears.
 */

const HASH_RE = /^#(activity_\d+|note_id_\d+)$/;
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

function highlight(el) {
    ensureStyles();
    el.classList.add(HIGHLIGHT_CLASS);
    if (typeof el.scrollIntoView === 'function') {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
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
