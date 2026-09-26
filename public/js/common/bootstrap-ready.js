'use strict';

let resolveBootstrapReady = null;
let bootstrapReadyResolved = false;

if (typeof window !== 'undefined' && typeof window.bootstrapReady === 'undefined') {
    window.bootstrapReady = new Promise(function (resolve) {
        resolveBootstrapReady = resolve;
    });
}

/**
 * True when Bootstrap 5 and the jQuery popover bridge from app.js are available.
 */
export function bootstrapIsReady() {
    return typeof window !== 'undefined'
        && typeof window.bootstrap !== 'undefined'
        && window.bootstrap.Dropdown
        && typeof window.$ !== 'undefined'
        && typeof window.$.fn.popover === 'function';
}

/**
 * Called at the end of resources/js/bootstrap.js after globals and bridges are set.
 */
export function markBootstrapReady() {
    if (bootstrapReadyResolved || !bootstrapIsReady()) {
        return;
    }

    bootstrapReadyResolved = true;

    if (typeof resolveBootstrapReady === 'function') {
        resolveBootstrapReady();
        resolveBootstrapReady = null;
    }
}

/**
 * Wait for Bootstrap + jQuery bridges (used by legacy page bundles that may load before app.js).
 *
 * @param {number} [timeoutMs]
 * @returns {Promise<void>}
 */
export function waitForBootstrap(timeoutMs) {
    timeoutMs = timeoutMs || 10000;

    if (bootstrapIsReady()) {
        return Promise.resolve();
    }

    var bootstrapPromise = (typeof window !== 'undefined' && window.bootstrapReady)
        ? window.bootstrapReady
        : Promise.resolve();

    return bootstrapPromise.then(function () {
        if (bootstrapIsReady()) {
            return;
        }

        return new Promise(function (resolve, reject) {
            var deadline = Date.now() + timeoutMs;

            (function tick() {
                if (bootstrapIsReady()) {
                    resolve();
                    return;
                }

                if (Date.now() >= deadline) {
                    reject(new Error('Bootstrap not available after timeout'));
                    return;
                }

                setTimeout(tick, 50);
            })();
        });
    });
}

if (typeof window !== 'undefined') {
    window.waitForBootstrap = waitForBootstrap;
    window.markBootstrapReady = markBootstrapReady;
}

export {};
