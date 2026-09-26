/**
 * Partner detail page — Vite entry (Phase 2g).
 * Requires window.AppConfig / PageConfig from detail.blade.php inline script first.
 */
'use strict';

import '@legacy/common/config.js';
import {
    applyActivitiesFilters,
    applyActivitiesResponse,
    applyNotesResponse,
    deleteactivitylog,
    getallactivities,
    getallnotes,
} from '@legacy/common/activity-handlers.js';

if (typeof window !== 'undefined') {
    window.getallactivities = getallactivities;
    window.getallnotes = getallnotes;
    window.deleteactivitylog = deleteactivitylog;
    window.applyActivitiesResponse = applyActivitiesResponse;
    window.applyNotesResponse = applyNotesResponse;
    window.applyActivitiesFilters = applyActivitiesFilters;
}

import '@legacy/pages/admin/partner-detail/archive-handlers.js';
import '@legacy/pages/admin/partner-detail/notes-handlers.js';
import '@legacy/pages/admin/partner-detail/notes-contact-handlers.js';

import '@legacy/common/document-handlers.js';
import '@legacy/pages/admin/partner-detail/bulk-upload.js';
import '@legacy/pages/admin/client-detail/document-rename.js';
import '@legacy/pages/admin/client-detail/document-context-menu.js';
import '@legacy/pages/admin/client-detail/document-actions.js';
import '@legacy/pages/admin/client-detail/preview-file-scroll.js';
import './client-detail/deep-link-highlight.js';

import '@legacy/pages/admin/partner-detail/application-tab.js';
import '@legacy/pages/admin/partner-detail/application-handlers.js';
import '@legacy/pages/admin/partner-detail/service-handlers.js';
import '@legacy/pages/admin/partner-detail/datatable-handlers.js';
import '@legacy/pages/admin/partner-detail/payment-field-handlers.js';
import '@legacy/pages/admin/partner-detail/promotion-handlers.js';
import '@legacy/pages/admin/partner-detail/status-handlers.js';
import '@legacy/pages/admin/partner-detail/invoice-handlers.js';
import '@legacy/pages/admin/partner-detail/mail-upload.js';

import '@legacy/pages/admin/partner-detail.js';
