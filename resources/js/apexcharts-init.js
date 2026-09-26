/**
 * ApexCharts — loaded on pages that need charts (e.g. audit logs).
 * Dynamic import keeps the library out of the initial page bundle.
 */
'use strict';

let apexChartsPromise = null;

export function whenApexChartsReady() {
    if (!apexChartsPromise) {
        apexChartsPromise = import('apexcharts').then(function (module) {
            window.ApexCharts = module.default;

            return module.default;
        });
    }

    return apexChartsPromise;
}

if (typeof window !== 'undefined') {
    window.whenApexChartsReady = whenApexChartsReady;
    whenApexChartsReady();
}
