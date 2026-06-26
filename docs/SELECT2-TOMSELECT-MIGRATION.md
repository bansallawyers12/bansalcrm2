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
| `waitForTomSelect()` | Promise when `TomSelect` global is available |

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

| Area | Migrated | Deferred (still Select2) |
|------|----------|---------------------------|
| Client create/edit | `#visa_type`, `country_passport`, `#country_select`, `service`, `#assign_to`, `#tag` (create only) | `related_files` → **Phase 4** |
| Partner create/edit | `country`, `branch_country` (add-branch modal) | `#getpartnertype` → `#partner_type` chain → **Phase 5**; add-branch modal `.select2` chain |
| Product create/edit | `product_type`, `intake_month`, `#intrested_product` / `#intrested_branch` | — |
| Leads create | Same static set as client create (via `client-create.js`) | `related_files`, `lead_source`, `subagent` → **Phase 4** |
| Modals (`addclientmodal`, `addpartnermodal`, `addproductmodal`) | Static: `application`, `fee_type`, `template`, `agent_id`, `checklist[]`, `degree_level`, `document_type` | `workflow`/`partner`/`product` chains, `applicationselect2*`, `productselect2`, AJAX `contact_name` |

Init: page scripts call `waitForTomSelect()` + `initTomSelect()`; modals use global `initModalTomSelects` on `shown.bs.modal`.

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

Add-branch modal `.select2` chain remains Select2 (deferred to Phase 6).

## Phase 6 candidates

1. Application/modal handlers — `applicationselect2`, `productselect2`, modal workflow chains
2. Invoice, staff, agents, action pages (`.timezoneselect2`, template selects)
3. Header modern search (`modern-search.js`) — last
4. Ongoing sheet stage filter (`.ongoing-filter-select2`)

## Pilot pages (superseded — see Phase 6)

## Phase 0 test checklist

Run after deploy; no user-facing change expected.

### Load verification

- [ ] Admin layout: no console errors on any admin page load
- [ ] Admin Console layout: no console errors
- [ ] `typeof TomSelect === 'function'` in browser console
- [ ] `typeof $.fn.select2 === 'function'` still true
- [ ] `typeof initTomSelect === 'function'` in browser console
- [ ] `typeof BansalTomSelect === 'object'` in browser console
- [ ] `typeof isTomSelect === 'function'` and `typeof getEnhancementWrapper === 'function'`
- [ ] Network tab: Tom Select CSS/JS load from jsDelivr (200)
- [ ] `tomselect-bridge.css` loads with cache-bust query param

### Select2 regression (unchanged behaviour)

- [ ] Global `.select2` elements still initialize (clients list modals, partner forms, etc.)
- [ ] Header modern search still works (Select2 ajax)
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
- [ ] Global Select2 init does not attach to `.tomselect-migrated` element

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
