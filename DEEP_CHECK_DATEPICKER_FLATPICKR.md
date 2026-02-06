# Deep Check: Datepicker → Flatpickr

## Summary

- **jQuery UI datepicker**: Was only used in **partners/detail.blade.php** (agreement modal). **Replaced with Flatpickr** in a previous change.
- **All other date inputs** either already use Flatpickr (via `scripts.js` and page-specific JS) or use native HTML5 `type="date"`.
- **jquery.datetimepicker** (xdsoft): Files exist in `public/js` and `public/css` but are **never loaded**; `.datetimepicker` is handled by **Flatpickr** in `scripts.js`.

---

## 1. jQuery UI

| Location | Usage | Status |
|----------|--------|--------|
| `resources/views/Admin/partners/detail.blade.php` | Agreement modal `#agreement_contract_start`, `#agreement_contract_expiry` | **Replaced** with Flatpickr; jQuery UI CSS/JS removed |
| `resources/views/Admin/applications/index.blade.php` | **Autocomplete** only (assignee, partner) | Keep jQuery UI – not datepicker |
| `resources/views/Admin/applications/overdue.blade.php` | **Autocomplete** only | Keep jQuery UI |
| `resources/views/Admin/applications/finalize.blade.php` | **Autocomplete** only | Keep jQuery UI |

---

## 2. Flatpickr (current usage)

- **Loaded**: `resources/js/vendor-libs.js` (Vite) → `window.flatpickr`
- **Global init**: `public/js/scripts.js` initializes Flatpickr for:
  - `.datepicker` → `dateFormat: "Y-m-d"`
  - `.dobdatepicker` → `dateFormat: "d/m/Y"`
  - `.dobdatepickers` → `dateFormat: "d/m/Y"` + age calculation
  - `.filterdatepicker` → `dateFormat: "Y-m-d"`
  - `.datepicker-input` → `dateFormat: "d/m/Y"`
  - `.contract_expiry` → `dateFormat: "Y-m-d"`
  - `.datetimepicker` → `dateFormat: "Y-m-d H:i"`, `enableTime: true`
  - `.daterange` → `mode: "range"`, `dateFormat: "Y-m-d"`
- **Page-specific inits**: agents/detail, products/detail, users/view, client-detail (application-handlers, invoice-handlers, notes-handlers, etc.), partner-detail (invoice-handlers, application-handlers, notes-handlers), recent_clients/index, sheets/ongoing, clientreceiptlist – all use Flatpickr.

---

## 3. jquery.datetimepicker (xdsoft)

| File | Status |
|------|--------|
| `public/js/jquery.datetimepicker.full.min.js` | **Not loaded** by any view/layout |
| `public/css/jquery.datetimepicker.min.css` | **Not loaded** by any view/layout |

- **Conclusion**: Dead code. `.datetimepicker` is handled by **Flatpickr** in `scripts.js`. Safe to delete these two files if you want to clean up.

---

## 4. Native HTML5 `type="date"` (remaining)

**Action/client popover dates (`#popoverdatetime`)**: Replaced with Flatpickr. They now use `type="text"` and class `flatpickr-date`; `scripts.js` initializes Flatpickr on `shown.bs.popover` for `#popoverdatetime`.

These still use the browser’s native date picker (optional to replace later):

| File | Field / context |
|------|------------------|
| `resources/views/Admin/invoice/show.blade.php` | `payment_date[]` |
| `resources/views/Admin/invoice/unpaid.blade.php` | `payment_date[]` |
| `resources/views/Admin/invoice/edit.blade.php` | `discount_date`, `payment_date[]` (2) |
| `resources/views/Admin/invoice/general-invoice.blade.php` | `payment_date[]` |
| `resources/views/Admin/invoice/commission-invoice.blade.php` | `discount_date`, `payment_date[]` |
| `resources/views/Admin/reports/action_calendar.blade.php` | `followup_date` |
| `resources/views/Admin/reports/followup.blade.php` | `followup_date` |

---

## 5. Commented / legacy code

- **`public/js/popover.js`**: All `#embeddingDatePicker` and `.datepicker()` calls are **commented out**; notes say “migrated to Flatpickr / HTML5 date inputs”. No change needed.

---

## 6. Inputs with class `datepicker` (all use Flatpickr)

These use class `datepicker` (or `dobdatepicker`, `filterdatepicker`, etc.) and are initialized by `scripts.js` or page-specific JS with **Flatpickr**:

- Partners: detail (agreement modal), addpartnermodal
- Clients: addclientmodal, create, edit, applicationdetail
- Products: addproductmodal
- Leads: create
- Invoice: edit-gen, commission-invoice
- Account: payableunpaid
- PromotionController (PHP): promotion start/end dates
- Agents: create, edit
- Plus `.dobdatepickers`, `.filterdatepicker`, `.startdatepicker`, `.enddatepicker`, `.datepicker-input` in various views – all inited with Flatpickr.

---

## Actions taken

1. **partners/detail.blade.php**: jQuery UI datepicker removed; agreement modal dates use Flatpickr; jQuery UI CSS/JS removed from this page.
2. **Action/client popover dates**: All `#popoverdatetime` inputs (ActionController, action/index, assigned_by_me, assign_to_me, completed, clients/detail) changed from `type="date"` to `type="text"` with class `flatpickr-date`; Flatpickr is initialized in `scripts.js` on `shown.bs.popover`.
3. No other `.datepicker()` or jQuery UI datepicker usage found.

## Optional next steps

1. ~~**Remove dead files**~~ **Done**: Removed `public/js/jquery.datetimepicker.full.min.js` and `public/css/jquery.datetimepicker.min.css` (unused; Flatpickr handles `.datetimepicker`).
2. **Unify date UX** (optional): Replace remaining native `type="date"` in invoice/reports (list in §4) with `type="text"` + Flatpickr and init in `scripts.js`.
