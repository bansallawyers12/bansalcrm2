# Office Visits: Single Blade + Remove Archived & All Tabs

**Date:** 2026-02-06  
**Status:** Approved — implementation in progress  
**Scope:** bansalcrm2 and migrationmanager2 In Person / office-visits page

**Decisions (2026-02-06):**
- Default tab: **Waiting** (sidebar and `/office-visits` → waiting).
- Archive: **Remove entirely** — remove archive action, POST route, and Archive link in modal (option B).
- Archived data: No data; no separate archived view needed.
- Apply same changes to **migrationmanager2**.

---

## Goals

1. **Single Blade file:** Replace the 5 office-visits Blade files (index, waiting, attending, completed, archived) with one view that uses a `$activeTab` variable.
2. **Remove Archived and All tabs:** The page will show only **Waiting**, **Attending**, and **Completed**. Remove the "Archived" and "All" tabs, their routes, and related UI.

---

## Current State

| Item | Current |
|------|--------|
| **Blade files** | `Admin/officevisits/index.blade.php`, `waiting.blade.php`, `attending.blade.php`, `completed.blade.php`, `archived.blade.php` (5 files) |
| **Routes** | `/office-visits` (All), `/office-visits/waiting`, `/office-visits/attending`, `/office-visits/completed`, `/office-visits/archived`, `/office-visits/create` |
| **Controller methods** | `index()`, `waiting()`, `attending()`, `completed()`, `archived()` — each returns a different view |
| **Tabs on page** | Waiting, Attending, Completed, Archived, All |
| **Sidebar** | "In Person" link goes to `officevisits.waiting`; active state checks `officevisits.index`, `.waiting`, `.attending`, `.completed`, `.archived` |
| **Archive action** | POST `/office-visits/archive`; "Archive" link in check-in detail modal (in `layouts/admin.blade.php`); JS in `legacy-init.js` |

---

## Target State

| Item | Target |
|------|--------|
| **Blade files** | One file: `Admin/officevisits/index.blade.php` (or `list.blade.php`) |
| **Routes** | Keep only: `/office-visits/waiting`, `/office-visits/attending`, `/office-visits/completed`, `/office-visits/create`. Remove: `/office-visits`, `/office-visits/archived`. Optional: redirect `/office-visits` → `/office-visits/waiting`. |
| **Controller** | One shared method (or three methods that pass `$activeTab` and return the same view). Remove `index()` and `archived()` list views. |
| **Tabs on page** | Only: **Waiting**, **Attending**, **Completed** (with counts). No Archived, no All. |
| **Archive** | Remove "Archive" link from check-in detail modal. Optionally keep `archive()` route for future use or remove it. |

---

## Implementation Plan

### Phase 1: Controller – single view + remove All/Archived list actions

**File:** `app/Http/Controllers/Admin/OfficeVisitController.php`

1. **Introduce `$activeTab` and single view**
   - Define allowed tabs: `'waiting'`, `'attending'`, `'completed'`.
   - For `waiting()`, `attending()`, `completed()`: keep the same query logic (status 0, 2, 1 and `is_archived = 0`), but pass `activeTab` and return **one** view, e.g.:
     - `return view('Admin.officevisits.index', compact('lists', 'totalData', 'activeTab'));`
   - Use `$activeTab` so the Blade can set the active tab and build branch filter URLs.

2. **Remove or redirect `index()` (All)**
   - **Option A:** Remove the route for `/office-visits` and the `index()` method. Add a route redirect: `/office-visits` → `/office-visits/waiting` (so old links still work).
   - **Option B:** Keep one route `/office-visits` that behaves like "default tab" (e.g. same as waiting) and pass `activeTab = 'waiting'` and return the same single view.

3. **Remove `archived()` list**
   - Remove the `archived()` method (or leave it as a stub that redirects to waiting if you prefer).
   - Remove the route: `Route::get('/office-visits/archived', ...)`.

4. **Optional: Archive action**
   - **Option A (recommended):** Keep `archive(Request $request)` and POST route for future use (e.g. if you add an Archived tab back later). Remove only the "Archive" link from the check-in detail modal so users don’t see it.
   - **Option B:** Remove the "Archive" link and the `archive()` action + route entirely.

---

### Phase 2: Single Blade file

**New/updated file:** `resources/views/Admin/officevisits/index.blade.php` (single file; others deleted)

1. **Layout and structure**
   - Keep: `@extends('layouts.admin')`, same section structure, flash message, card header "In Person", "Create In Person" button.

2. **Counts**
   - Only three counts: Waiting, Attending, Completed (all with `is_archived = 0`).
   - Remove: `$InPersonCount_All_type`, `$InPersonCount_archived_type` (and any PHP that computes them in the view). Prefer moving count logic to the controller and passing counts in; if kept in view, only compute the three needed.

3. **Tabs**
   - Only three `<li>` items: Waiting, Attending, Completed.
   - Use `$activeTab` to add `class="nav-link active"` to the correct tab; others get `class="nav-link"`.
   - Tab links: `URL::to('/office-visits/waiting')`, `.../attending`, `.../completed`.

4. **Branch dropdown**
   - Base URL for "All Branches" and per-branch links: depend on `$activeTab`, e.g.:
     - `$baseUrl = '/office-visits/' . $activeTab;`
     - All Branches: `{{ URL::to($baseUrl) }}`
     - Per branch: `{{ URL::to($baseUrl . '?office=' . $branch->id . '&office_name=' . urlencode($branch->office_name)) }}`

5. **Table**
   - Same table structure and body (ID, Date, Start, Contact Name, Contact Type, Visit Purpose, Assignee, Wait Time, Action). Data is already filtered by the controller per tab.

6. **Pagination**
   - Keep `$lists->appends(\Request::except('page'))->render()`.

7. **Modals and scripts**
   - Include the same modals (e.g. Compose Email) and `@section('scripts')` / inline JS once (same as in current waiting.blade.php). Remove any tab-specific JS that referenced Archived or All.

8. **Delete old Blades**
   - After the single view works: delete `waiting.blade.php`, `attending.blade.php`, `completed.blade.php`, `archived.blade.php` from `resources/views/Admin/officevisits/`.
   - Keep only the one view used by the controller (e.g. `index.blade.php`).

---

### Phase 3: Routes

**File:** `routes/web.php`

1. **Remove**
   - `Route::get('/office-visits', [OfficeVisitController::class, 'index'])->name('officevisits.index');`
   - `Route::get('/office-visits/archived', [OfficeVisitController::class, 'archived'])->name('officevisits.archived');`

2. **Keep**
   - `Route::get('/office-visits/waiting', ...)->name('officevisits.waiting');`
   - `Route::get('/office-visits/attending', ...)->name('officevisits.attending');`
   - `Route::get('/office-visits/completed', ...)->name('officevisits.completed');`
   - `Route::get('/office-visits/create', ...)->name('officevisits.create');`
   - `Route::post('/office-visits/archive', ...)->name('officevisits.archive');` (optional; keep if you retain the archive action)
   - `Route::get('/office-visits/change_assignee', ...);`

3. **Optional redirect**
   - Add: `Route::get('/office-visits', fn () => redirect()->route('officevisits.waiting'))->name('officevisits.index');` so `/office-visits` and any old links to "All" or index still land on Waiting.

---

### Phase 4: Sidebar and "active" state

**File:** `resources/views/Elements/Admin/left-side-bar.blade.php`

1. **Active state**
   - Current condition includes `officevisits.index` and `officevisits.archived`. Remove those.
   - Use only: `officevisits.waiting`, `officevisits.attending`, `officevisits.completed` for the In Person active state, e.g.:
     - `if(Route::currentRouteName() == 'officevisits.waiting' || Route::currentRouteName() == 'officevisits.attending' || Route::currentRouteName() == 'officevisits.completed')`

2. **Link**
   - Keep "In Person" pointing to `route('officevisits.waiting')` (no change).

---

### Phase 5: Remove Archive from check-in detail modal

**File:** `resources/views/layouts/admin.blade.php`

1. **Remove the Archive link**
   - Find the check-in detail modal and remove the line that contains the "Archive" link/button, e.g.:
     - `<a href="javascript:;" class="archive-checkin-detail" ...><i class="fa fa-trash"></i> Archive</a>`

**File:** `resources/js/legacy-init.js` (optional)

2. **Archive handler**
   - If you remove the Archive button entirely, you can leave the `.archive-checkin-detail` handler in place (it will never run) or remove it to avoid dead code. If you keep the `archive()` route for future use, keeping the handler is harmless.

---

### Phase 6: Notifications and other references

1. **Notification URL**
   - In `OfficeVisitController` (and anywhere else that sets a notification URL for office visits), ensure the URL points to a valid route, e.g. `URL::to('/office-visits/waiting')`. No change needed if it already does.

2. **Search**
   - Grep for `officevisits.index`, `officevisits.archived`, `/office-visits/archived`, `/office-visits` (as list URL) and update or remove only office-visits–specific references. Left-side-bar is already covered in Phase 4.

---

## Summary Checklist

| # | Task | File(s) |
|---|------|--------|
| 1 | Add `$activeTab` to waiting/attending/completed; return single view | `OfficeVisitController.php` |
| 2 | Remove `index()` and `archived()` list methods (or redirect index → waiting) | `OfficeVisitController.php` |
| 3 | Create single Blade with 3 tabs, counts, branch URL from `$activeTab` | `Admin/officevisits/index.blade.php` |
| 4 | Delete waiting, attending, completed, archived Blades | `Admin/officevisits/*.blade.php` |
| 5 | Remove routes for `/office-visits` and `/office-visits/archived`; optional redirect | `routes/web.php` |
| 6 | Update sidebar active state (drop index + archived) | `left-side-bar.blade.php` |
| 7 | Remove Archive link from check-in detail modal | `layouts/admin.blade.php` |
| 8 | (Optional) Keep or remove `archive()` action and POST route | Controller + `web.php` |

---

## Order of work (recommended)

1. **Controller:** Add `activeTab` and single view; keep three methods (waiting, attending, completed) returning the same view. Then remove or redirect `index()` and remove `archived()`.
2. **Blade:** Implement single `index.blade.php` with 3 tabs and `$activeTab`; test all three routes.
3. **Routes:** Remove index and archived routes; add redirect if desired.
4. **Sidebar:** Update active condition.
5. **Modal:** Remove Archive link.
6. **Cleanup:** Delete the four redundant Blade files.

---

## Rollback

- Keep a backup of the five Blade files and the controller before changes. Restoring the old routes and controller methods and the five Blades will roll back the UI to the current behaviour.

---

**Document version:** 1.0  
**Status:** Plan only — do not apply until approved.
