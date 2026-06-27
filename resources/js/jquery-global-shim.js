/**
 * Vite alias target for `import … from 'jquery'`.
 * Uses the synchronous layout script (Phase 2a) — never bundle a second jQuery copy.
 */
const jq = typeof window !== 'undefined' ? (window.jQuery || window.$) : undefined;

if (!jq) {
    throw new Error('[jquery-global-shim] jQuery must load in <head> before vendor-libs.js');
}

export default jq;
export { jq as jQuery };
