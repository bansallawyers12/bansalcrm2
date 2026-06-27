# public/js and the `@legacy` Vite alias

After the Phase 2 Vite migration, **`public/js/**` is source code for the bundler**, not a set of scripts to load directly in the browser.

## How it works

| Piece | Role |
|-------|------|
| `vite.config.js` → `@legacy` | Maps to `public/js/` |
| `resources/js/*-entry.js` | Vite entry points that `import '@legacy/...'` |
| `public/build/assets/*` | Production bundles served to the browser |
| `asset('js/jquery-3.7.1.min.js')` | **Exception:** jQuery stays sync in layout `<head>` (Phase 2a) |

Example:

```js
// resources/js/admin-layout-scripts.js
import '@legacy/custom-form-validation.js';
import '@legacy/modern-search.js';
```

Do **not** add new `<script src="{{ asset('js/…') }}">` tags for files under `public/js/` (except jQuery).

## Where to add or change behaviour

1. Edit the file under `public/js/` (or add a new one there).
2. Import it from the appropriate Vite entry in `resources/js/` (layout script, page entry, or dynamic import).
3. If it is a new entry surface, register the entry in `vite.config.js` `input` and load it with `@vite([...])` in the Blade view.
4. Run `npm run build` (or `npm run dev` locally).

## npm vs `@legacy`

| Load via npm + Vite | Still `@legacy` (`public/js`) |
|---------------------|-------------------------------|
| Bootstrap, flatpickr, DataTables core, Tom Select lib, TinyMCE, ApexCharts, FullCalendar | CRM page handlers, form validation, modals, search, most admin UI logic |

New third-party libraries should go through npm and a dedicated `resources/js/*-init.js` entry when possible.

## Auditing unused files

```bash
npm run audit:legacy-js
# or: node scripts/audit-legacy-js.cjs
```

Lists `public/js/**/*.js` files not imported via `@legacy` and not referenced elsewhere.  
`jquery-3.7.1.min.js` is excluded automatically (loaded via `asset()` in layouts).

## Removed rollback copies (Track B)

All removed — do not restore:

| Path | Replacement |
|------|-------------|
| `public/assets/tinymce/` | npm `tinymce` + `resources/js/tinymce-init.js` |
| `public/js/tinymce-init.js` | `resources/js/tinymce-init.js` (Vite entry) |
| `public/js/apexcharts.min.js` | npm `apexcharts` + `resources/js/apexcharts-init.js` |
| `public/js/modern-search-simple.js`, `modern-search-debug.js` | Dev-only; removed (production uses `modern-search.js`) |

## UI helpers (toast / confirm)

| Helper | File | Notes |
|--------|------|-------|
| `showToast(message, type)` | `resources/js/vendor-libs.js` | iziToast; loaded in layout `<head>` |
| `toastMsg(message, type)` | `public/js/common/utilities.js` | Thin wrapper around `showToast` |
| `window.alert()` | shim in `vendor-libs.js` | Routes to `showToast` (non-blocking) |
| `crmConfirm(message)` | `public/js/common/confirm-dialog.js` | Bootstrap 5 modal; returns `Promise<boolean>` |
| `data-crm-confirm="…"` | same | Declarative confirm on forms/links/buttons |

Import `confirm-dialog.js` from layout script entries (`admin-layout-scripts.js`, `adminconsole-layout-scripts.js`, `agent-layout-scripts.js`).
