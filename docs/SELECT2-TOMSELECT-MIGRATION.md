# Select2 → Tom Select Migration

Phase 0 foundation: Tom Select loads alongside Select2. No existing `.select2()` calls removed.

## Files

| File | Purpose |
|------|---------|
| `package.json` | `tom-select` + `select2` npm deps (CDN used in layouts; npm for future Vite bundling) |
| `resources/views/layouts/admin.blade.php` | Tom Select CDN + bridge CSS + `tomselect-init.js` |
| `resources/views/layouts/adminconsole.blade.php` | Same as admin layout |
| `public/js/common/tomselect-init.js` | `initTomSelect`, `destroyTomSelect`, detection/wrapper helpers |
| `public/css/tomselect-bridge.css` | Layout/z-index rules mirroring `.select2-*` selectors |
| `public/js/scripts.js` | Global init excludes `.tomselect` and `.tomselect-migrated` |
| `public/js/common/ui-components.js` | `UIComponents.initSelect2` excludes migrated elements |

## Conventions (Phase 0+)

- Migrated `<select>` elements: add class **`tomselect`**, remove **`select2`** when fully migrated (Phase 1+).
- `initTomSelect()` auto-adds `tomselect`, `tomselect-migrated`, and `data-enhanced="tomselect"`.
- Global Select2 init in `scripts.js` skips `.tomselect` and `.tomselect-migrated`.

## Helper API (`tomselect-init.js`)

| Function | Purpose |
|----------|---------|
| `initTomSelect(el, options)` | Create Tom Select instance (accepts Select2-style options) |
| `initTomSelectAll(selector, options)` | Batch init for all elements matching a selector |
| `destroyTomSelect(el)` | Destroy instance and remove migration classes |
| `isTomSelect(el)` | True if element has `element.tomselect` |
| `isSelect2(el)` | True if Select2-enhanced (jQuery data or `select2-hidden-accessible`) |
| `getEnhancementWrapper(el)` | Returns `.ts-wrapper` or `.select2-container` sibling |
| `placeValidationError(el, errorHtml)` | Inserts validation error after enhancement wrapper |
| `destroyEnhancedSelect(el)` | Destroy Tom Select or Select2 on element |
| `reinitTomSelect(el, options)` | Destroy then init Tom Select |
| `initModalTomSelects(modalEl, options)` | Init all `select.tomselect` in modal on `shown.bs.modal` |
| `setEnhancedSelectValue(el, value)` | Set value on Tom Select or native select |
| `getEnhancedSelectValue(el)` | Read value from Tom Select or native select |
| `whenTomSelectReady(callback)` | Run callback when helpers loaded (Promise + poll fallback) |
| `waitForTomSelect()` | Promise when `TomSelect` global is available |
| `reinitTomSelectAfterHtml(el, html, opts)` | AJAX cascade: destroy → replace `<option>` HTML → re-init |
| `compactTomSelectOptions(extra)` | Full-width, no search (`minimumResultsForSearch: Infinity`) |
| `initTomSelectPreserveValue(el, opts)` | Init and restore pre-selected native value (edit pages) |
| `initTomSelectAllPreserveValues(sel, opts)` | Batch version of preserve-value init |

## Migration workflow (per page)

1. Replace `$('.my-select').select2({ … })` with `initTomSelect('.my-select', { … })`.
2. Remove class `select2` from the `<select>`; `initTomSelect` adds `tomselect` + `tomselect-migrated`.
3. Use `placeValidationError(el, html)` or `getEnhancementWrapper()` for error placement after the wrapper.
4. Verify modal dropdowns, form submit values, and destroy/re-init flows.

## Select2 → Tom Select option mapping

| Select2 | Tom Select / `initTomSelect` |
|---------|------------------------------|
| `$el.select2(options)` | `initTomSelect($el, options)` |
| `$el.select2('destroy')` | `destroyTomSelect($el)` |
| `$el.select2('open')` | `$el[0].tomselect.open()` |
| `$el.select2('close')` | `$el[0].tomselect.close()` |
| `width: '100%'` / `'200px'` | Mapped to wrapper inline width |
| `placeholder: '…'` | `placeholder: '…'` |
| `allowClear: true` | Adds `clear_button` plugin |
| `multiple: true` | Keep `multiple` attribute on `<select>` |
| `dropdownParent: $('#modal')` | `dropdownParent: '#modal'` or jQuery element |
| `minimumInputLength: N` | Honored in ajax `load` callback |
| `ajax: { url, data, processResults, delay }` | Mapped to Tom Select `load` + `loadThrottle` |
| `data: [{ id, text }]` | Mapped to Tom Select `options` |
| `templateResult` | `render.option` |
| `templateSelection` | `render.item` |
| `escapeMarkup: fn` | `render.option` / `render.item` (HTML allowed) |
| `containerCssClass` | `wrapperClass` |
| `dropdownCssClass` | `dropdownClass` |
| `tags: true` | `create: true` |

### Tom Select native options (pass through)

These work directly in `initTomSelect` without mapping:

- `maxItems`, `maxOptions`, `plugins`, `sortField`, `hideSelected`, `closeAfterSelect`
- `valueField`, `labelField`, `searchField`, `preload`, `loadThrottle`
- `onInitialize`, `onChange`, `onItemAdd`, `onItemRemove`, `onDropdownOpen`, `onDropdownClose`

### Not yet mapped (handle manually in Phase N+)

- Select2 `matcher` → Tom Select `score` callback
- Select2 `language` / i18n → Tom Select `render` + custom strings
- Select2 `maximumSelectionLength` → Tom Select `maxItems`
- `$el.val(x).trigger('change')` → `$el[0].tomselect.setValue(x, true)` or `addItem`/`clear` for multi

## Phase 1 — Tier A static dropdowns (Done)

Migrated pages: class `select2` → `tomselect`, explicit `initTomSelect` / `waitForTomSelect`, no global Select2 init.

| Page | Layout | Controls |
|------|--------|----------|
| `Admin/branch/create.blade.php` | adminconsole | `#branch_country`, `#branch_choose_admin` |
| `Admin/branch/edit.blade.php` | adminconsole | `#branch_country`, `#branch_choose_admin` (staff list + saved admin repopulated) |
| `Admin/reports/followup.blade.php` | admin | `#changeassignee` in `#event-details-modal` |
| `Admin/reports/action_calendar.blade.php` | admin | `#changeassignee` in `#event-details-modal` |
| `AdminConsole/emails/create.blade.php` | adminconsole | `#email_create_users` multi; errors via `getEnhancementWrapper` |
| `AdminConsole/emails/edit.blade.php` | adminconsole | `#email_edit_users` multi |

### Phase 1 per-control checklist

For each migrated control:

- [ ] Open / close dropdown (click + Escape)
- [ ] Keyboard: type to filter, arrow keys, Enter to select
- [ ] Form POST: submitted value matches selection (create + edit)
- [ ] Edit page: previously saved value(s) shown on load
- [ ] No double-init (`isTomSelect(el)` true, `isSelect2(el)` false)
- [ ] Modal selects: dropdown visible above backdrop (`dropdownParent` set where needed)

| `BansalTomSelect.*` | Namespace with the same methods |

Modals: `shown.bs.modal` on `.modal` auto-calls `initModalTomSelects(this)` (dropdownParent = `.modal-content`).

## Phase 2 — Form pages: static + multi + modal (Done)

| Area | Migrated | Still Select2 (Phase 6+) |
|------|----------|---------------------------|
| Client create/edit | Static fields + **`related_files[]`** | — |
| Partner create/edit | `country`, branch modal country, **`getpartnertype` / `partner_type` / `service_workflow`** | Add-branch modal `.select2` chain |
| Product create/edit | **`product_type`, `intake_month`, `#intrested_product`, `#intrested_branch`** | — |
| Leads create | Static fields + **related files, `#lead_source`, `subagent`** | — |
| Modals (`addclientmodal`, etc.) | Static selects + **Add Application `#workflow` / `#partner` / `#product`** | AJAX `contact_name`, other modal Select2 |

Init: page scripts call `waitForTomSelect()` + `initTomSelect()`; modals use global `initModalTomSelects` on `shown.bs.modal`. Add Application cascade: `application-modal-cascade.js`.

## Phase 3 — Filter pages (Done)

| Page | Layout | Controls |
|------|--------|----------|
| `Admin/auditlogs/index.blade.php` | admin | `.audit-staff-select` — single staff filter |
| `Admin/sheets/insights.blade.php` | admin | `.insights-branch-select` — multi branch filter (`closeAfterSelect: false`; no placeholder — avoids empty `branch[]` in GET) |

## Phase 4 — Related Files AJAX + leads static selects (Done)

| Area | Migrated | Notes |
|------|----------|-------|
| Client create | `select[name="related_files[]"]` | `RecipientSelect.initRelatedFiles()` — AJAX + `minimumInputLength: 1` |
| Client edit | same | Preloads from `PageConfig.relatedFilesData` / `.relatedfile` hidden inputs |
| Leads create | related files + `#lead_source` + `subagent` | Removed duplicate inline Select2 block; uses `client-create.js` |
| `recipient-select.js` | `initRelatedFiles`, `ensureRelatedFiles`, `collectRelatedFileEntries` | Shared with email modals (same API endpoint) |

Init: `waitForRecipientSelect()` → `RecipientSelect.initRelatedFiles({ minimumInputLength: 1 })` (no placeholder on multi — avoids empty `related_files[]` in POST).

### Phase 4 review fixes (applied)

| Issue | Fix |
|-------|-----|
| AJAX `valueField: 'id'` but preloaded options only had `value` | `tomselect-init.js` maps both `id` and `value` on `_select2Data` options |
| Edit page: `waitForRecipientSelect` missing if script order wrong | Fallback poll + explicit `App.getUrl('getRecipients')` URL |
| Multi placeholder could inject empty option | Removed `placeholder` from related files init |
| `#lead_source` subagent toggle after Tom Select init | `syncSubagentVisibility()` uses `tomselect.getValue()` |
| `resolveUrl` missed `getRecipients` key used on edit page | Added `App.getUrl('getRecipients')` |

## Phase 5 — Destroy/reinit chains: products + partners (Done)

| Area | Migrated | Pattern |
|------|----------|---------|
| Product create/edit | `#intrested_product`, `#intrested_branch` | `initTomSelectPreserveValue` + `reinitTomSelectAfterHtml` on partner change → `/getnewPartnerbranch` |
| Partner create | `#getpartnertype`, `#partner_type`, `service_workflow` | `compactTomSelectOptions()` + `reinitTomSelectAfterHtml` on category change → `/getpaymenttype` |
| Partner edit | `partner_type`, `service_workflow` | `initTomSelectAllPreserveValues` (saved selections preserved) |
| `tomselect-init.js` | `reinitTomSelectAfterHtml`, `compactTomSelectOptions`, `initTomSelectPreserveValue`, `initTomSelectAllPreserveValues` | Shared cascade helpers |

Add-branch modal `.select2` chain migrated in Phase 6b.

### Phase 5 review fixes (applied)

| Issue | Fix |
|-------|-----|
| Cascade change handlers read `option:selected` (unreliable with Tom Select) | `getEnhancedSelectValue()` |
| `destroyTomSelect` removed `tomselect` class before re-init | `reinitTomSelectAfterHtml` re-adds `tomselect` class |
| Partner create init flag set when Tom Select not loaded | Guard on `typeof TomSelect` before setting flag |
| Edit pages: `product_type` / `intake_month` / `country` saved values | `initTomSelectPreserveValue` on all pre-filled selects |
| Script load race on inline page scripts | `whenTomSelectReady()` poll fallback |
| Master category cleared | Reset `#partner_type` via `reinitTomSelectAfterHtml` |

## Phase 6a — Add Application modal cascade (Done)

| Area | Migrated | Pattern |
|------|----------|---------|
| `addclientmodal.blade.php` | `#workflow`, `#partner`, `#product` | `select.tomselect`; global `initModalTomSelects` on show |
| `addproductmodal`, `addagentmodal`, `addpartnermodal` | same three selects | `select2` → `tomselect` |
| `application-modal-cascade.js` | Shared workflow → partner → product AJAX | `reinitTomSelectAfterHtml`, `getEnhancedSelectValue`, destroy on `hidden.bs.modal` |
| `application-handlers.js` | Removed duplicate cascade | Cascade delegated to shared module |
| `staff/view`, `products/detail`, `agents/detail`, `partner-detail/service-handlers.js` | Removed inline Select2 cascade handlers | Shared module via `admin.blade.php` |
| `custom-form-validation.js`, `agent-custom-form-validation.js` | Post-save form reset | `ApplicationModalCascade.clearSelectValue` |

### Phase 6a review notes

| Item | Detail |
|------|--------|
| URL fallbacks | `site_url + /getpartnerbranch` when `App.getUrl` absent (product/agent pages) |
| Modal scoping | Cascade + destroy scoped to the modal that fired the event |
| Product disable during AJAX | `tomselect.disable()` / `enable()` on modal-scoped `#product` |
| Modal destroy on hide | Clears instances so next open re-inits via `initModalTomSelects` |
| Workflow AJAX error | Hides `.popuploader` on failure |
| Post-save clear | Uses visible `.add_appliation.show` modal element refs |

## Phase 6b — Promotion product multi-select + add-branch modal (Done)

| Area | Migrated | Pattern |
|------|----------|---------|
| `addpartnermodal.blade.php` | `#create_promotion` product picker | `tomselect promotion-product-select` + `multiple`; no empty placeholder option |
| `PromotionController::getpromotioneditform` | Edit form product picker | Same markup |
| `promotion-handlers.js` | `initPromotionProductSelects()` | Tom Select multi, `closeOnSelect: false`, `initTomSelectPreserveValue` on AJAX edit load |
| Partner create/edit | `.addbranch` modal `#branch_country` | Removed Select2 init; Tom Select via `initTomSelect` on modal shown + `compactTomSelectOptions` |
| `partners/detail.blade.php` | `.addbranch .branch_country` | `select2` → `tomselect` (legacy modal markup aligned) |
| `tomselect-bridge.css` | Promotion + add-branch modals | Dropdown z-index / overflow rules |

`contact_name` AJAX fields remain commented out in modal blades — no live migration needed.

## Phase 6c — Email templates, staff/agents, invoice, ongoing filters (Done)

| Area | Migrated | Pattern |
|------|----------|---------|
| Email modals (clients, partners, staff, agents, products, invoice list pages) | `.selecttemplate` | `tomselect` + `email-modal-tomselect.js` on `#emailmodal` shown |
| Client detail | `#changeassignee`, email `#template` | `initTomSelect` multi / compact |
| Staff view | `.staff-timezone-select`, fee edit AJAX selects | Tom Select via shared init + `initTomSelectAllPreserveValues` |
| Invoice group create | Partner picker | `group-invoice-partner-select tomselect` |
| Products fee modal | `residencyelect2`, `installment_type` | `tomselect` + modal `initModalTomSelects` |
| Staff/agents/products/partner detail | Removed dead `timezoneselect2` Select2 inits | Rely on global modal init when `#create_appoint` exists |
| `sheets/ongoing.blade.php` | Branch multi + stage single filters | `ongoing-filter-branch-select`, `ongoing-filter-stage-select` |

## Phase 6d — Header search, action popovers, agreement modal, check-in (Done)

| Area | Migrated | Pattern |
|------|----------|---------|
| Header global search | `.js-data-example-ajaxccsearch` | Tom Select AJAX + optgroups in `modern-search.js`; Ctrl+K / access modal unchanged |
| Action pages | `.assigneeselect2`, `.task_group` | `tomselect` class + `action-popover-tomselect.js` on popover/modal shown |
| Partner agreement modal | `#agreement_represent_region`, `#agreement_default_super_agent` | `tomselect` + `initModalTomSelects` on `#agreementModal` shown; `setEnhancedSelectValue` / `clearEnhancedSelectValue` |
| Check-in modal | `.assineeselect2` | `tomselect` + init on `#checkinmodal` shown |
| Add-my-task popover | `.js-data-example-ajaxccsearch__addmytask` | `RecipientSelect.init` in `popover.js` |

Value reads in action/client scripts use `actionPopoverSelectVal` / `actionPopoverAssigneeLabel`. AJAX assignee list refresh uses `ActionPopoverTomSelect.refreshAssigneeSelect`.

## Phase 6f — Remaining form pages, AJAX task views, modal stragglers (Done)

| Area | Migrated | Pattern |
|------|----------|---------|
| Agents create/edit/import | `country`, `related_office` | `tomselect` + `whenTomSelectReady` → `initTomSelectAllPreserveValues` / `initTomSelectPreserveValue` |
| Staff view | `#primary_office` (both modals) | `tomselect` + global `initModalTomSelects` on modal shown |
| Leads index | Assign-lead modal `assignto` | `tomselect` + init on `#assignlead_modal` with modal `dropdownParent` |
| `ActionController` / `OfficeVisitController` | AJAX `#changeassignee` HTML | `select2` → `tomselect` in PHP output |
| Task / check-in AJAX views | `.taskview`, `.showchecindetail` | `task-view-tomselect.js` hooks `get-assigne-detail`, `get-task-detail`, `get-checkin-detail` |
| Agents add modal | `#represent_partner`, general invoice `application` | `tomselect` + `initModalTomSelects` |
| `ProductsController::getotherinfo` | `degree_level` | `tomselect` + null-safe `$ac`; AJAX init via `task-view-tomselect.js` |
| `ProductsController::getfeeoptionedit` | `.edit_installment_type` | `select2` → `tomselect` (staff fee modal already inits via `initTomSelectAllPreserveValues`) |
| Action / office visit save handlers | `#changeassignee` value read | `getEnhancedSelectValue('#changeassignee')` |
| CRM signatures | `#entity_id` in `#associateModal` | `tomselect` + `initTomSelectPreserveValue` on page load |
| `modal-handlers.js` | Commission / general invoice modals | Replaced `initModalSelect2` with `initModalTomSelects` |

Add-modal / edit-modal `contact_name` AJAX fields remain **commented out** in blades — no live init required.

## Phase 6e — Remove Select2 CDN and global init (Done)

| Area | Change |
|------|--------|
| `layouts/admin.blade.php`, `adminconsole.blade.php` | Removed Select2 CDN CSS/JS; Tom Select only |
| `scripts.js` | Replaced `$(".select2").select2()` with Tom Select fallback for legacy `select.select2` markup |
| `ui-components.js` | `initSelect2` → `initTomSelect`; vendor readiness checks Tom Select |
| `vendor-libs.js`, `legacy-init.js` | Readiness polls Tom Select instead of Select2 |
| Client create/edit | `#lead_source`, `subagent` → `tomselect` (page JS already inited; edit page gained subagent toggle) |
| Invoice create | `#customer_name`, `terms`, line items → `tomselect` + `invoice-create.js` |
| Check-in modal | `.js-data-example-ajax-check` → `RecipientSelect.init` (replaces Select2 in `legacy-init.js`) |
| `partner-detail.js`, `client-detail.js` | Vendor poll uses `initTomSelect` not Select2 |

**Note:** CSS class names like `select2-result-repository` remain in Tom Select render templates for styling continuity (`tomselect-bridge.css`). `modern-search-simple.js` / `modern-search-debug.js` are dev-only and not loaded in production layouts.

## Phase 0 test checklist

Run after deploy; no user-facing change expected.

### Load verification

- [ ] Admin layout: no console errors on any admin page load
- [ ] Admin Console layout: no console errors
- [ ] `typeof TomSelect === 'function'` in browser console
- [ ] `typeof $.fn.select2` is undefined (Select2 CDN removed)
- [ ] `typeof initTomSelect === 'function'` in browser console
- [ ] `typeof BansalTomSelect === 'object'` in browser console
- [ ] `typeof isTomSelect === 'function'` and `typeof getEnhancementWrapper === 'function'`
- [ ] Network tab: Tom Select CSS/JS load from jsDelivr (200)
- [ ] `tomselect-bridge.css` loads with cache-bust query param

### Tom Select regression (post–Phase 6e)

- [ ] Global legacy `select.select2` markup auto-inits via Tom Select fallback in `scripts.js`
- [ ] Header modern search still works (Tom Select AJAX in `modern-search.js`)
- [ ] Modal dropdowns: client detail email modal, add application modal, fee option modals
- [ ] AJAX selects: `.js-data-example-ajax*` still excluded from global init and work per-page
- [ ] Ongoing sheet stage filter (`.ongoing-filter-select2`) still works

### Tom Select foundation (manual smoke test in console)

On any admin page:

```javascript
// Create a throwaway select, init Tom Select, confirm no double-init with Select2
var s = document.createElement('select');
s.className = 'select2 tomselect-migrated';
s.innerHTML = '<option value="">Choose</option><option value="1">One</option>';
document.body.appendChild(s);
var ts = initTomSelect(s, { placeholder: 'Choose' });
ts.open(); // dropdown visible
destroyTomSelect(s);
s.remove();
```

- [ ] Tom Select dropdown opens above page content
- [ ] Element has `tomselect-migrated` class after init
- [ ] Global Tom Select init does not double-init `.tomselect-migrated` elements

### CSS bridge

- [ ] Form-group selects in modals: full width where Select2 was full width
- [ ] Fee option modal: compact height (~35px) when migrated (Phase 1)
- [ ] Commission / general invoice modals: dropdown z-index above modal backdrop when migrated

## Phase 0 review fixes (applied)

| Issue | Fix |
|-------|-----|
| Global `.ts-dropdown { width: 200px }` would break wide dropdowns | Scoped to `.card`/`.modal` form groups only |
| Wrong class `.dropdown-active` (Select2, not Tom Select) | Use `.ts-wrapper.focus` when dropdown is open |
| Failed `new TomSelect()` left `tomselect-migrated` on element | try/catch rolls back migration classes |
| Select2-only options (`multiple`, `closeOnSelect`, etc.) passed to Tom Select | Blocked via `SELECT2_ONLY_KEYS` + explicit mapping |
| `templateResult` returning jQuery DOM (common in CRM) | `normalizeTemplateOutput()` handles jQuery/Element/string |
| Init on Select2-enhanced element | Auto-destroys Select2 before Tom Select init |
| npm `^2.4.3` resolved to 2.6.1 while CDN uses 2.4.3 | Pinned `"tom-select": "2.4.3"` to match CDN |
| Empty CSS rule block | Removed |

## Phase status

| Phase | Scope | Status |
|-------|-------|--------|
| 0 | Foundation — both libraries, helper, bridge CSS, exclude migrated from global init | **Done** |
| 1 | Tier A static pilots (branch, reports, email staff sharing) | **Done** |
| 2 | Form pages — client/partner/product/leads + modal static selects | **Done** |
| 3 | Filter pages — audit logs staff filter, insights branch multi-select | **Done** |
| 4 | Related files AJAX + leads source/subagent | **Done** |
| 5 | Product partner→branch chain; partner create/edit address selects | **Done** |
| 6+ | Application modals, invoice/staff/agents, modern search, ongoing sheet | Pending |
