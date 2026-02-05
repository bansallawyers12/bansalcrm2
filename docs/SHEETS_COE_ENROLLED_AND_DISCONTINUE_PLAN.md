# Plan: COE Issued & Enrolled Sheet and Discontinue Sheet (Reuse Ongoing)

**Status:** **Final — do not apply yet.**

**Goal:** Add two new sheets (COE Issued & Enrolled, Discontinue) by reusing the existing Ongoing Sheet UI and logic. All three sheets use the **same table columns** as the current Ongoing sheet. Design so more sheet types (and optionally different columns per sheet) can be added later with minimal duplication.

**In scope for this phase:**
- One shared view, one controller with sheet type parameter.
- Same columns for all three sheets (Course Name, CRM Reference, Client Name, DOB, Payment, Institute, Branch, Assignee, Visa Expiry, Visa Category, Current Stage, Comment). No extra columns (e.g. no Discontinue reason/note in the table at this stage).
- No new tables. **No migrations.**

**Later (out of scope):** Extra columns per sheet (e.g. Discontinue reason/note), full column config for different columns per sheet, insights for new sheets.

---

## 1. Current State Summary

### 1.1 Ongoing Sheet Logic (from `OngoingSheetController`)

- **Data:** One row per **application** (same client can appear multiple times).
- **Included:** Applications where:
  - `applications.status != 2` (not discontinued)
  - Stage **not** in: `'coe issued'`, `'enrolled'`, `'coe cancelled'` (case-insensitive).
- **Excluded:** Discontinued apps (`status = 2`) and apps in COE Issued / Enrolled / COE Cancelled.

So **Ongoing** = “in progress” applications (pre–COE issued / pre–enrolled, and not discontinued).

### 1.2 What the Ongoing Sheet Has

| Layer | Location | Notes |
|-------|----------|--------|
| **View** | `resources/views/Admin/sheets/ongoing.blade.php` | Page header “Ongoing Sheet”, filter bar, table (Course, CRM Ref, Client, DOB, Payment, Institute, Branch, Assignee, Visa Expiry, Visa Category, Current Stage, Comment), pagination, sheet-comment modal |
| **Controller** | `App\Http\Controllers\Admin\OngoingSheetController` | `index()`, `buildBaseQuery()`, `applyFilters()`, `applySorting()`, `storeSheetComment()`, etc. |
| **Routes** | `routes/clients.php` | `clients.sheets.ongoing`, `clients.sheets.ongoing.insights`, `clients.sheets.ongoing.update`, `clients.sheets.ongoing.sheet-comment` |
| **Sidebar** | `resources/views/Elements/Admin/left-side-bar.blade.php` | Sheets dropdown with “Ongoing Sheet” link |

The view uses:

- Same columns for all rows (no sheet-specific columns in the table).
- Same filter set: Office, Assignee, Branch, Current Stage, Visa Expiry From/To, Search.
- Same “sheet comment” feature (per application).
- Session-persisted filters and per-sheet session key.

So the **only** thing that changes between sheet types is **which applications** are selected (query criteria). The UI can be shared.

---

## 2. New Sheets to Add

### 2.1 COE Issued and Enrolled Sheet

- **Criteria:** Applications where:
  - `applications.status != 2`
  - Stage **in** (case-insensitive): `'coe issued'`, `'enrolled'`.
- **Exclude:** COE Cancelled (and any other stages). So this sheet = “success path” (COE issued or already enrolled).

### 2.2 Discontinue Sheet

- **Criteria:** Applications where:
  - `applications.status = 2` (discontinued).
- Same filters as other sheets: Office, Assignee, Branch, Current Stage, Visa Expiry From/To, Search. Same columns as Ongoing (no Discontinue reason/note columns at this stage).

---

## 3. Reuse Strategy: One View, Multiple “Sheet Types”

### 3.1 Option A (Recommended): Single Shared View + One Controller with Sheet Type Parameter

- **One view:** e.g. `resources/views/Admin/sheets/sheet.blade.php` (or keep name `ongoing.blade.php` and treat it as the “generic” sheet view).
- **One controller:** e.g. keep `OngoingSheetController` but generalize it to handle a `sheet` type: `ongoing` | `coe_enrolled` | `discontinue`.
- **Routes:**  
  - `GET /clients/sheets/ongoing` → same as now.  
  - `GET /clients/sheets/coe-enrolled` → same view, `sheet=coe_enrolled`.  
  - `GET /clients/sheets/discontinue` → same view, `sheet=discontinue`.
- **Controller behaviour:**
  - Read `sheet` from route (or first segment after `/sheets/`).
  - `buildBaseQuery($request, $sheetType)` applies different where clauses:
    - `ongoing`: current logic (exclude status 2, exclude stages coe issued, enrolled, coe cancelled).
    - `coe_enrolled`: status != 2, stage in ('coe issued', 'enrolled').
    - `discontinue`: status = 2.
  - Pass to view: `sheetType`, `sheetTitle`, `sheetRoute`, `rows`, and all existing vars (e.g. `perPage`, `activeFilterCount`, `offices`, `branches`, `assignees`, `currentStages`).
- **View:** Uses `$sheetTitle` for the page header and `$sheetRoute` for form actions, links, and assignee bar. **Same table columns for all three sheets** (no extra columns in this phase).
- **Session:** Use a session key per sheet type, e.g. `ongoing_sheet_filters`, `coe_enrolled_sheet_filters`, `discontinue_sheet_filters`, so each sheet remembers its own filters.

**Pros:** One view to maintain; one controller with a small amount of branching; easy to add more sheet types later.  
**Cons:** None for this scope; controller has a small amount of branching per sheet type.

### 3.2 Future: What If a New Sheet Has Different Columns? (Not in this phase)

At this stage all three sheets use the **same columns**. When a future sheet needs different columns, two options (for reference only; not implemented now):

#### Approach 1: Column config (recommended when needed)

- Define columns per sheet type in config or controller; pass `$columns` to the view; view loops over `$columns` for header and body. Add any extra select fields in `buildBaseQuery` for that sheet type.

#### Approach 2: Table partial per sheet

- Include a table partial per sheet type, e.g. `@include('Admin.sheets.partials.table-' . $sheetType, ...)`, with each partial defining its own columns.

**This phase:** No column config; one shared table with the same columns for Ongoing, COE Issued & Enrolled, and Discontinue.

### 3.4 Option C: Separate Controller per Sheet, Shared Partial View

- One **partial** for the table + filters (e.g. `sheets/partials/sheet-table.blade.php`) and each sheet has a thin view that sets title/route and includes the partial.
- Controllers: `OngoingSheetController`, `CoeEnrolledSheetController`, `DiscontinueSheetController`, each with its own `buildBaseQuery()` but shared logic (e.g. a trait or base class for `applyFilters`, `applySorting`, `storeSheetComment`).

**Pros:** Very explicit per-sheet behaviour; good if business logic diverges a lot later.  
**Cons:** More files and duplication of filter/sort/session logic unless a base class or trait is used.

### 3.5 Recommendation

Use **Option A**: single shared view + one controller with a `sheet` type parameter. Add a **config or enum** for sheet types so adding a new sheet is “add one route, one menu item, one branch in base query, one session key”.

---

## 4. Implementation Checklist (When You Apply)

### 4.1 Backend

- [ ] **Sheet type**
  - Define sheet types: `ongoing`, `coe_enrolled`, `discontinue` (e.g. config `config/sheets.php` or class constant). For each: label, route name, session key for filters. Same columns for all; no column config in this phase.
- [ ] **Routes** (`routes/clients.php`)
  - Keep `GET /clients/sheets/ongoing` → same controller (e.g. `index` with type `ongoing`).
  - Add `GET /clients/sheets/coe-enrolled` → same controller, type `coe_enrolled`.
  - Add `GET /clients/sheets/discontinue` → same controller, type `discontinue`.
  - Keep `GET /clients/sheets/ongoing/insights` as is (optional; can extend to other sheets later).
  - Sheet-comment and update routes: keep under `/ongoing/` or generalize to `/sheets/sheet-comment`; behaviour is sheet-agnostic.
- [ ] **Controller**
  - Resolve `sheetType` from route (e.g. `Route::currentRouteName()` or route parameter).
  - `buildBaseQuery($request, $sheetType)`:
    - `ongoing`: existing logic (exclude status 2, exclude stages coe issued, enrolled, coe cancelled).
    - `coe_enrolled`: `whereNotIn('applications.status', [2])` + `whereRaw('LOWER(TRIM(applications.stage)) IN (?, ?)', ['coe issued', 'enrolled'])`.
    - `discontinue`: `where('applications.status', 2)`.
  - `currentStages` for filters: ongoing = current logic; coe_enrolled = COE Issued / Enrolled only; discontinue = stages where status = 2.
  - Session key per `sheetType`: e.g. `coe_enrolled_sheet_filters`, `discontinue_sheet_filters`.
  - Pass to view: `sheetType`, `sheetTitle`, `sheetRoute`, `rows`, and existing vars (offices, branches, assignees, currentStages, perPage, activeFilterCount).

### 4.2 Frontend (View)

- [ ] **Single view** (e.g. keep `ongoing.blade.php` or use `sheet.blade.php`; all three routes render it)
  - Page header: use `$sheetTitle` (“Ongoing Sheet”, “COE Issued & Enrolled”, “Discontinue”).
  - Filter form `action`, “Clear filters” link, assignee bar `onchange`, per-page `onchange`: use `$sheetRoute` so they target the correct sheet URL.
  - Table: **same columns for all three sheets** (Course Name, CRM Reference, Client Name, DOB, Payment, Institute, Branch, Assignee, Visa Expiry, Visa Category, Current Stage, Comment). No extra columns in this phase.
  - Sheet comment modal and JS: unchanged (comment per application; one save route).
- [ ] **CSS**
  - Keep existing “ongoing-*” classes; reuse for all sheet types (no visual change).

### 4.3 Sidebar / Navigation

- [ ] **Sheets menu** (`left-side-bar.blade.php`)
  - Add “COE Issued & Enrolled” link → `route('clients.sheets.coe-enrolled')`.
  - Add “Discontinue” link → `route('clients.sheets.discontinue')`.
  - Set `active` class when `Route::currentRouteName()` is `clients.sheets.coe-enrolled` or `clients.sheets.discontinue` (and keep existing ongoing/insights active logic).

### 4.4 Future Sheets (e.g. COE Cancelled, Refund)

- Add new sheet type in config/constants, one route, one branch in `buildBaseQuery`, one menu item, one session key. Same view and controller. If a future sheet needs different columns, introduce column config or a table partial then (see Section 3.2).

---

## 5. Data Criteria Summary

| Sheet | applications.status | applications.stage |
|-------|---------------------|--------------------|
| **Ongoing** | ≠ 2 | NOT IN ('coe issued', 'enrolled', 'coe cancelled') |
| **COE Issued & Enrolled** | ≠ 2 | IN ('coe issued', 'enrolled') |
| **Discontinue** | = 2 | any (filter by stage optional) |

All sheets: same client/app constraints as ongoing (e.g. `admins.role = 7`, `is_archived = 0`, `is_deleted` null). Same joins (products, partners, branches, assignee, client_ongoing_references for ongoing-specific fields; for discontinue, ongoing ref is optional).

---

## 6. Migrations

**No migration needed for this phase.**

- Sheet types and routes are code/config only.
- All three sheets use existing tables: `applications`, `admins`, `products`, `partners`, `branches`, `client_ongoing_references`, `application_activities_logs`. Same columns as the current Ongoing sheet; no new tables or new columns.
- Discontinue reason/note are **not** shown in the sheet table at this stage, so there is no need to add or verify those columns for this feature.

---

## 7. Files to Touch (When Applying)

| Action | File |
|--------|------|
| Add or modify | Sheet type list: `config/sheets.php` (optional) or constants in `OngoingSheetController` (label, route, session key per type) |
| Modify | `routes/clients.php` — add two routes: `coe-enrolled`, `discontinue` |
| Modify | `app/Http/Controllers/Admin/OngoingSheetController.php` — sheet type from route, `buildBaseQuery($request, $sheetType)`, per-type session key, pass `sheetTitle`, `sheetRoute`, existing vars |
| Modify | `resources/views/Admin/sheets/ongoing.blade.php` — use `$sheetTitle`, `$sheetRoute` for header and all links/forms; same table as now |
| Modify | `resources/views/Elements/Admin/left-side-bar.blade.php` — add “COE Issued & Enrolled” and “Discontinue” menu links, active state for new routes |

No new tables, no migrations, no new view file. Same table columns for all three sheets.

---

## 8. Out of Scope for This Phase

- Extra columns (e.g. Discontinue reason, Discontinue note in the sheet table).
- Full column config (different columns per sheet type); same columns for all three sheets now.
- Insights view for COE Enrolled or Discontinue.
- Export/Excel for the new sheets.
- Any database migrations.

---

**Plan finalised. Do not apply until approved.**
