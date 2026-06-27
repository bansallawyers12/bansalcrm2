/**
 * Admin layout legacy scripts — Vite entry (Phase 2f).
 * Load after app.js, before legacy-init.js. Requires jQuery + vendor-libs.
 */
'use strict';

import '@legacy/common/utilities.js';
import '@legacy/common/crm-icon.js';
import '@/lucide-init.js';
import '@legacy/common/tomselect-init.js';
import '@legacy/common/task-view-tomselect.js';
import '@legacy/common/application-modal-cascade.js';
import '@legacy/common/email-modal-tomselect.js';
import '@legacy/common/recipient-select.js';
import '@legacy/common/action-popover-tomselect.js';
import '@legacy/custom-form-validation.js';
import '@legacy/scripts.js';
import '@legacy/custom.js';
import '@legacy/modern-search.js';
import '@legacy/inactivity-logout.js';
