# Plan: Migrate Education Documents and Migration Documents into Documents Tab

**Goal:** Consolidate the "Education Documents" and "Migration Documents" tabs into the single "Documents" tab (the one that uses categories: `alldocuments`), so all client documents are managed in one place with category-based organization. **Do not implement**—this document is the investigation and plan only.

---

## 1. Current State Summary

### 1.1 Tab structure (client detail page)

| Tab slug (route)   | UI label              | `doc_type` in DB | Data source / behavior |
|--------------------|-----------------------|------------------|-------------------------|
| `documents`        | **Education Documents** | `education`    | `Document::where('doc_type','education')`; upload currently disabled (commented out). |
| `migrationdocuments` | **Migration Documents** | `migration`  | `Document::where('doc_type','migration')`; upload currently disabled (commented out). |
| `alldocuments`     | **Documents**         | `documents`     | Category-based: `DocumentCategoryController::getDocuments()` filters by `doc_type = 'documents'` (or null/empty) and `category_id`. Has Add Checklist, Bulk Upload, category tabs. |
| `notuseddocuments` | Not Used Documents     | `documents` only | `Document::where('not_used_doc', 1)->where('doc_type','documents')`. Education/Migration docs are not shown here. |

### 1.2 Key files

- **View:** `resources/views/Admin/clients/detail.blade.php`
  - Tab list: `#documents-tab` (Education), `#migrationdocuments-tab` (Migration), `#alldocuments-tab` (Documents).
  - Tab panes: `#documents`, `#migrationdocuments`, `#alldocuments` (and `#notuseddocuments`).
  - Queries: Education (~1108), Migration (~1201), Documents tab server-rendered list (~1322), Not Used (~1515–1520).
- **Backend:**
  - `app/Http/Controllers/Admin/Client/DocumentCategoryController.php` — `getCategories()`, `getDocuments()` (only `doc_type = 'documents'` or null/empty).
  - `app/Http/Controllers/Admin/Client/ClientDocumentController.php` — upload, delete, verify, notused, backtodoc; uses `doctype` / `doc_type` (education, migration, documents).
  - `app/Http/Controllers/Admin/AdminController.php` — email sending uses `doc_type` (education / migration vs documents) to build attachment paths (e.g. local `img/documents/` for education/migration, S3 URL for documents).
- **Models:**
  - `app/Models/Document.php` — `doc_type` in fillable; `category_id`; relationship to `DocumentCategory`.
  - `app/Models/DocumentCategory.php` — `getDocumentCount()` and `documents()` only count/include `doc_type = 'documents'` (or null/empty).
- **Frontend:**
  - `public/js/pages/admin/client-detail/document-categories.js` — category tabs and loading documents for Documents tab (calls `/document-categories/documents`).
  - `public/js/pages/admin/client-detail/ui-layout-and-tabs.js` — references `#documents`, `#migrationdocuments`, `#alldocuments`, `#notuseddocuments`.
  - `public/js/pages/admin/client-detail/document-actions.js` — verify/notused/backtodoc; only updates DOM for `res.doc_type == 'documents'`.
- **Routes:** `routes/clients.php` — document and document-category routes (no separate routes per tab; tab is UI only).

### 1.3 Database

- **`documents`:** `doc_type` (`education` | `migration` | `documents`), `category_id` (nullable). Education and Migration rows currently have `category_id` = null.
- **`document_categories`:** One default "General"; plus optional client-specific categories. No "Education" or "Migration" categories today.

### 1.4 Other references to education / migration

- **detail.blade.php ~2285:** Composed email document list: `whereIn('doc_type', ['education','migration','documents'])`; display uses `$docTypes['education'/'migration'/'documents']`.
- **detail.blade.php ~2307:** File URL for composed email: special handling when `doc_type == 'education' || doc_type == 'migration'` (e.g. local path).
- **AdminController (email send):** Builds attachments from selected documents; education/migration use local path `img/documents/` + `myfile`; documents use `myfile` (S3) and may need download to temp file.

---

## 2. Migration Strategy Options

### Option A: Categories only (recommended for clarity)

- Add two **default** document categories: e.g. "Education" and "Migration" (or keep one "General" and add "Education" and "Migration").
- **Data migration:** For every `documents` row with `doc_type = 'education'`, set `doc_type = 'documents'` and `category_id = <Education category id>`. Same for `doc_type = 'migration'` → Migration category.
- **UI:** Remove the "Education Documents" and "Migration Documents" tabs. Keep a single "Documents" tab with category tabs (General, Education, Migration, + any user-created).
- **Behavior:** All document types use the same flow: category-based list, same upload/verify/not used/back to doc logic. Email composition and AdminController continue to work by using **category** (or a preserved flag) to decide attachment path if needed.

### Option B: Keep doc_type, show in Documents tab

- Do **not** change `doc_type` on existing rows. Add default categories "Education" and "Migration".
- **Backend:** `DocumentCategoryController::getDocuments()` and `DocumentCategory::getDocumentCount()` are extended to include rows where `doc_type = 'education'` when the selected category is Education, and `doc_type = 'migration'` when the selected category is Migration. Map category name/slug to doc_type for this.
- **Data migration:** Set `category_id` on education/migration documents to the corresponding new default category (and optionally keep `doc_type` for backward compatibility and email logic).
- **UI:** Same as Option A: remove the two tabs, single Documents tab with category tabs.

### Option C: Full merge into existing “documents” type

- Same as Option A but do not add Education/Migration as categories; assign all education/migration documents to "General" (or one "Legacy" category). Simpler schema but you lose the distinction in the UI unless you add a separate label/column.

**Recommendation:** Option A (or B if you want to keep `doc_type` for reporting/email logic without changing attachment logic). Option A is simpler long-term; attachment logic can use `category->name` or a small mapping table if needed.

---

## 3. Implementation Plan (High Level)

### Phase 1: Database and categories

1. **Default categories**
   - Add two default categories (e.g. "Education", "Migration") in a new migration or seeder, with `is_default = true`, `user_id`/`client_id` = null (same pattern as "General").
2. **Data migration**
   - Migration script:
     - For each document with `doc_type = 'education'`: set `doc_type = 'documents'`, `category_id = <Education category id>`.
     - For each document with `doc_type = 'migration'`: set `doc_type = 'documents'`, `category_id = <Migration category id>`.
   - If choosing Option B, only set `category_id` and keep `doc_type`; then extend getDocuments/getDocumentCount to include education/migration by category.

### Phase 2: Backend

3. **DocumentCategoryController**
   - If Option A: no change to filter logic; education/migration rows become `doc_type = 'documents'` with a category.
   - If Option B: in `getDocuments()` and in `DocumentCategory::getDocumentCount()`, when the category is Education/Migration, also include rows with `doc_type = 'education'` / `doc_type = 'migration'` (and optionally set `category_id` on those rows so relationship is consistent).
4. **ClientDocumentController**
   - Upload: if you re-enable upload for “Education” or “Migration”, ensure new uploads use `doctype = 'documents'` and the correct `category_id` (and no longer use `doctype = 'education'` or `'migration'`).
   - Delete, verify, notused, backtodoc: already work by `doc_id`; ensure any place that filters by `doc_type` (e.g. returning list after action) includes the new category-based documents (automatic if they become `doc_type = 'documents'`).
5. **AdminController (email)**
   - Today attachment path depends on `doc_type` (education/migration = local, documents = S3). After migration, either:
     - Resolve by `category_id` / category name (e.g. Education/Migration → local path if that’s how files are stored), or
     - Store a consistent path in `documents.myfile` (e.g. full URL or key) and use one resolution path for all (recommended long-term).

### Phase 3: Frontend (client detail)

6. **detail.blade.php**
   - Remove the two `<li>` nav items for "Education Documents" and "Migration Documents".
   - Remove the two tab panes `#documents` and `#migrationdocuments` (and their content).
   - Remove `'documents'` and `'migrationdocuments'` from `$allowedTabs` (and any `$tabAliases` if present).
   - Ensure direct URL/refresh with `?tab=documents` or `tab=migrationdocuments` redirects or defaults to `alldocuments` (or remove those from allowed tabs so they fall back to activities).
7. **Documents tab (alldocuments)**
   - No structural change; category tabs will now include Education and Migration. Ensure default selected category and “first load” behavior still make sense (e.g. General or first alphabetically).
8. **Not Used Documents**
   - Decide: should “Not Used” show only current `doc_type = 'documents'` or also former education/migration? After Phase 1 they are all `doc_type = 'documents'`, so the existing query may be enough. If you keep `doc_type` (Option B), extend the Not Used query to include `doc_type in ('documents','education','migration')` and `not_used_doc = 1`.
9. **Email composition (same blade)**
   - The list that uses `whereIn('doc_type', ['education','migration','documents'])` can stay as-is for Option A (all are `documents`). Display label can use category name (Education/Migration/General) instead of doc_type. Update the file URL logic (~2307) to use a single resolution path or category-based path after Phase 2.

### Phase 4: JS and other references

10. **document-categories.js**
    - Ensure when a new category is added (Education/Migration), switching to that category loads the correct documents (handled by backend if categories and data migration are correct).
11. **ui-layout-and-tabs.js**
    - Remove references to `#documents` and `#migrationdocuments` if they are used for tab switching or layout; keep `#alldocuments` and `#notuseddocuments`.
12. **document-actions.js**
    - Verify/notused/backtodoc: after migration, all these docs are `doc_type = 'documents'`, so existing `res.doc_type == 'documents'` DOM updates remain correct. If you keep education/migration as doc_type (Option B), either extend DOM logic to refresh the correct list (e.g. by category) or keep reload.
13. **document-upload.js / blade-inline.js / bulk upload**
    - Ensure bulk upload and single upload for the Documents tab send `category_id` and `doctype = 'documents'`; no references to education/migration doctype in these flows after migration.

### Phase 5: Cleanup and testing

14. **Search and replace**
    - Grep for `doc_type.*education`, `doc_type.*migration`, `doctype.*education`, `doctype.*migration`, and `documents-tab` / `migrationdocuments-tab` in views, JS, and controllers; update or remove as per plan.
15. **Regression tests**
    - Upload in Documents tab (General, Education, Migration categories).
    - Verify, Not Used, Back to Doc from Not Used.
    - Email composition: attach documents from different categories; send and check attachments.
    - Not Used Documents list shows expected rows after moving docs to Not Used.
16. **Redirects / bookmarks**
    - If users have bookmarks to `.../documents` or `.../migrationdocuments`, add a redirect (e.g. to `.../alldocuments` or `...?tab=alldocuments`) so old links still work.

---

## 4. File Checklist (for when you implement)

| Area | File(s) | Action |
|------|--------|--------|
| DB | New migration | Add default categories Education, Migration. |
| DB | New migration | Update documents: education → doc_type=documents + category_id; same for migration. |
| Backend | `DocumentCategoryController` | Option B only: extend getDocuments/getDocumentCount by category → doc_type. |
| Backend | `ClientDocumentController` | Ensure upload/actions use category_id and doctype=documents where applicable. |
| Backend | `AdminController` | Attachment path: switch to category or unified myfile resolution. |
| View | `detail.blade.php` | Remove Education/Migration tabs and panes; update allowedTabs; fix email doc list labels/URLs. |
| View | `detail.blade.php` | Not Used: extend doc_type filter if Option B. |
| JS | `ui-layout-and-tabs.js` | Remove #documents, #migrationdocuments from tab list if used. |
| JS | `document-actions.js` | Confirm DOM refresh for documents tab after verify/notused/backtodoc. |
| Routes | `clients.php` | Optional: redirect old tab slugs to alldocuments. |

---

## 5. Risks and Notes

- **File storage:** Education and Migration may currently be stored under paths that differ from the Documents tab (e.g. local vs S3). The plan does not move physical files; it only changes `doc_type` and `category_id`. Ensure `documents.myfile` (and `myfile_key` if used) remain valid after migration; fix AdminController and detail.blade.php attachment/URL logic to use one resolution strategy.
- **Not Used:** Today only `doc_type = 'documents'` appears in Not Used. After migration (Option A), education/migration docs that are moved to Not Used will be `doc_type = 'documents'` and will appear there; no query change needed. With Option B, extend the Not Used query to include education/migration if you want them there.
- **Backwards compatibility:** Old bookmarks to `tab=documents` and `tab=migrationdocuments` should redirect to the Documents tab (`tab=alldocuments`) to avoid 404 or wrong tab.

---

## 6. Summary

- **Current:** Three separate tabs (Education Documents, Migration Documents, Documents) with different `doc_type` values and no category for education/migration.
- **Target:** One "Documents" tab with category tabs (General, Education, Migration, + custom). Education and Migration data become either `doc_type = 'documents'` with category_id (Option A) or keep doc_type but appear in Documents tab via category (Option B).
- **Steps:** Add default categories → data migration → backend filters and attachment logic → remove two tabs and update blade/JS → test and optional redirects.

No code has been implemented; this document is the investigation and plan only.
