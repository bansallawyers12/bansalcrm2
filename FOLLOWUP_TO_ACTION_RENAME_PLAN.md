# Follow-up → Action Rename Plan

**Purpose:** Rename "follow-up" (and related terms) to "action" across the client/partner action system for consistent identification. The Action tab and client/partner follow-ups use the same underlying data (`Note` with `folloup = 1`).

**Status:** Planned — to be applied tomorrow.  
**Do not apply yet.**

---

## Scope

- **In scope:** Client and partner actions (Notes with `folloup = 1`, `type` in `['client','partner']`), routes, controllers, views, services, and JS that create/update/list them.
- **Out of scope:**
  - Lead follow-ups (`FollowupController`, `Followup` model, `followups` table) — separate system.
  - Invoice follow-ups (`InvoiceFollowup`).
  - Database column renames (`followup_date`, `folloup`) — would require migrations.

---

## A. Routes

### `routes/clients.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 1 | 47 | `// Follow-up routes` | `// Action routes` |
| 2 | 48 | `/clients/followup/store` | `/clients/action/store` |
| 2 | 48 | `clients.followup.store` | `clients.action.store` |
| 3 | 49 | `/clients/followup_application/store_application` | `/clients/action_application/store_application` |
| 3 | 49 | `clients.followup.store_application` | `clients.action.store_application` |
| 4 | 50 | `/clients/followup/retagfollowup` | `/clients/action/retag` |
| 4 | 50 | `clients.followup.retagfollowup` | `clients.action.retag` |
| 5 | 51 | `/clients/personalfollowup/store` | `/clients/personalaction/store` |
| 5 | 51 | `clients.personalfollowup.store` | `clients.personalaction.store` |
| 6 | 52 | `/clients/updatefollowup/store` | `/clients/updateaction/store` |
| 6 | 52 | `clients.updatefollowup.store` | `clients.updateaction.store` |
| 7 | 53 | `/clients/reassignfollowup/store` | `/clients/reassignaction/store` |
| 7 | 53 | `clients.reassignfollowup.store` | `clients.reassignaction.store` |

### `routes/web.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 8 | 529 | `/followup-dates` | `/action-calendar` |
| 9 | 642 | `/partners/followup_partner/store_partner` | `/partners/action_partner/store_partner` |

---

## B. Controllers

### `app/Http/Controllers/Admin/Client/ClientActionController.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 10 | 15 | `"and followups for clients"` | `"and actions for clients"` |
| 11 | 19 | `followupstore` | `actionstore` |
| 12 | 20 | `followupstore_application` | `actionstore_application` |
| 13 | 21 | `reassignfollowupstore` | `reassignactionstore` |
| 14 | 22 | `updatefollowup` | `updateaction` |
| 15 | 23 | `retagfollowup` | `retagaction` |
| 16 | 24 | `personalfollowup` | `personalaction` |
| 17 | 35 | `//Asssign followup and save` | `//Assign action and save` |
| 18 | 36 | `function followupstore()` | `function actionstore()` |
| 19 | 47–60 | `$followup` | `$action` (all in this method) |
| 20 | 81 | `'Followup Assigned by'` | `'Action Assigned by'` |
| 21 | 88 | `'Followup set for'` | `'Action set for'` |
| 22 | 106 | `//Task reassign and update exist followup` | `//Task reassign and update existing action` |
| 23 | 107 | `function reassignfollowupstore()` | `function reassignactionstore()` |
| 24 | 118–135 | `$followup` | `$action` (all in this method) |
| 25 | 160 | `'Followup Assigned by'` | `'Action Assigned by'` |
| 26 | 167 | `'Followup set for'` | `'Action set for'` |
| 27 | 186 | `function updatefollowup()` | `function updateaction()` |
| 28 | 196–214 | `$followup` | `$action` (all in this method) |
| 29 | 240 | `'Followup Assigned by'` | `'Action Assigned by'` |
| 30 | 247 | `'Followup set for'` | `'Action set for'` |
| 31 | 267 | `//Personal followup` | `//Personal action` |
| 32 | 268 | `function personalfollowup()` | `function personalaction()` |
| 33 | 272 | `'personalfollowup request data'` | `'personalaction request data'` |
| 34 | 304 | `'personalfollowup parsed client_id'` | `'personalaction parsed client_id'` |
| 35 | 308 | `'personalfollowup: Invalid client_id'` | `'personalaction: Invalid client_id'` |
| 36 | 318–330 | `$followup` | `$action` (all in this method) |
| 37 | 335 | `'Error saving followup in personalfollowup'` | `'Error saving action in personalaction'` |
| 38 | 363 | `'Personal Task Followup Assigned by'` | `'Personal Task Action Assigned by'` |
| 39 | 389 | `function retagfollowup()` | `function retagaction()` |
| 40 | 393–405 | `$followup` | `$action` (all in this method) |
| 41 | 412 | `route('followup.index')` | `route('action.index')` |
| 42 | 426 | `'Followup Assigned by'` | `'Action Assigned by'` |
| 43 | 438 | `route('followup.index')` | `route('action.index')` |
| 44 | 443 | `function followupstore_application()` | `function actionstore_application()` |
| 45 | 449–475 | `$followup` | `$action` (all in this method) |
| 46 | 492 | `'Followup Assigned by'` | `'Action Assigned by'` |

**Note:** After renaming methods, update route definitions in `routes/clients.php` to call the new method names.

### `app/Http/Controllers/Admin/PartnersController.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 47 | 2676 | `function followupstore_partner()` | `function actionstore_partner()` |
| 48 | 2679–2713 | `$followup` | `$action` (all in this method) |
| 49 | 2744 | `'Followup Assigned by'` | `'Action Assigned by'` |

**Note:** Update `routes/web.php` to point to `actionstore_partner`.

### `app/Http/Controllers/Admin/AdminController.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 50 | 1967 | `'Followup Assigned by'` | `'Action Assigned by'` |

### `app/Http/Controllers/Admin/ReportController.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 51 | 109 | `function followupdates()` | `function actionCalendar()` or `actionDates()` |
| 52 | 111 | `'Admin.reports.followup'` | `'Admin.reports.action_calendar'` |

**Note:** Rename `resources/views/Admin/reports/followup.blade.php` → `action_calendar.blade.php` and update route in `web.php` to use the new controller method.

---

## C. Services

### `app/Services/DashboardService.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 53 | 21 | `"Get today's followup count"` | `"Get today's action count"` |
| 54 | 25 | `getTodayFollowupCount()` | `getTodayActionCount()` |
| 55 | 40 | `'Error getting today followup count'` | `'Error getting today action count'` |
| 56 | 66 | `// Active followup` | `// Active action` |
| 57 | 80 | `// Apply date filter based on followup_date` | `// Apply date filter based on action date (followup_date column)` |

**Note:** If `getTodayFollowupCount` is called elsewhere (e.g. layout, dashboard), update those call sites to `getTodayActionCount`.

---

## D. Views — Action Pages

### `resources/views/Admin/action/index.blade.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 58 | 837 | URL `clients/reassignfollowup/store` | `clients/reassignaction/store` |
| 59 | 841 | `note_type: 'follow_up'` | `note_type: 'action'` |
| 60 | 907 | URL `clients/updatefollowup/store` | `clients/updateaction/store` |
| 61 | 909 | `note_type:'follow_up'` | `note_type:'action'` |
| 62 | 958 | URL `clients/personalfollowup/store` | `clients/personalaction/store` |
| 63 | 960 | `note_type:'follow_up'` | `note_type:'action'` |

### `resources/views/Admin/action/completed.blade.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 64 | 131 | `@sortablelink('followup_date','Assign Date')` | `@sortablelink('followup_date','Action Date')` or `'Due Date'` |
| 65 | 500 | URL `clients/reassignfollowup/store` | `clients/reassignaction/store` |
| 66 | 503 | `note_type:'follow_up'` | `note_type:'action'` |

### `resources/views/Admin/action/assigned_by_me.blade.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 67 | 65 | `@sortablelink('followup_date','Assign Date')` | `@sortablelink('followup_date','Action Date')` or `'Due Date'` |
| 68 | 360 | URL `clients/reassignfollowup/store` | `clients/reassignaction/store` |
| 69 | 362 | `note_type:'follow_up'` | `note_type:'action'` |
| 70 | 414 | URL `clients/updatefollowup/store` | `clients/updateaction/store` |
| 71 | 416 | `note_type:'follow_up'` | `note_type:'action'` |

### `resources/views/Admin/action/assign_to_me.blade.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 72 | 85 | `@sortablelink('followup_date','Follow-up Date')` | `@sortablelink('followup_date','Action Date')` or `'Due Date'` |
| 73 | 261 | `@sortablelink('followup_date','Follow-up Date')` | `@sortablelink('followup_date','Action Date')` or `'Due Date'` |
| 74 | 521 | URL `clients/followup/store` | `clients/action/store` |
| 75 | 524 | `note_type:'follow_up'` | `note_type:'action'` |

---

## E. Views — Client Pages

### `resources/views/Admin/clients/detail.blade.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 76 | 2896 | `clientFollowup:` | `clientAction:` |
| 76 | 2896 | URL `/clients/followup/store` | `/clients/action/store` |

### `resources/views/Admin/clients/addclientmodal.blade.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 77 | 1511 | `/clients/followup_application/store_application` | `/clients/action_application/store_application` |

---

## F. Views — Partner Pages

### `resources/views/Admin/partners/addpartnermodal.blade.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 78 | 1685 | `/partners/followup_partner/store_partner` | `/partners/action_partner/store_partner` |

---

## G. Views — Reports

### `resources/views/Admin/reports/followup.blade.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 79 | 2 | `@section('title', 'Followup')` | `@section('title', 'Action Calendar')` |
| 80 | 26 | `<h4>Followup</h4>` | `<h4>Action Calendar</h4>` |
| 81 | 205 | `/clients/followup/retagfollowup` | `/clients/action/retag` |
| 82 | 207 | `id="followup_client_id"` | `id="action_client_id"` (and update JS in same file ~line 140) |
| 83 | 140 | `#followup_client_id` | `#action_client_id` |

**File rename:** `followup.blade.php` → `action_calendar.blade.php` (and update `ReportController` to return `'Admin.reports.action_calendar'`).

---

## H. Views — Left Sidebar

### `resources/views/Elements/Admin/left-side-bar.blade.php`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 84 | 193 | `$countfollowup` | `$countaction` (if uncommenting) |
| 85 | 195 | `$countfollowup` | `$countaction` (if uncommenting) |
| 86 | 201 | `Today Followup` | `Today Actions` or `Due Today` |
| 87 | 201 | `countfollowup` | `countaction` |

**Note:** "Today Followup" block is currently commented out. If you re-enable it, use the new variable names and route (e.g. `/action-calendar`).

---

## I. JavaScript

### `public/js/pages/admin/client-detail/assignments.js`

| # | Line | Current | Change to |
|---|------|---------|-----------|
| 88 | 36 | `clientFollowup` | `clientAction` |
| 88 | 36 | `/clients/followup/store` | `/clients/action/store` |
| 89 | 42 | `note_type:'follow_up'` | `note_type:'action'` |

**Note:** Client detail view passes `clientFollowup` (or `clientAction`) into JS. Ensure the config key in `detail.blade.php` matches.

---

## J. Do Not Rename

- **DB columns:** `followup_date`, `folloup` in `notes` (and related models). Keep as-is unless you add a separate migration.
- **Note model:** `$fillable`, `$sortable` entries for those columns.
- **Lead system:** `FollowupController`, `Followup`, `FollowupType`, `followups` table, `/followup/*` routes, lead timeline views.
- **Invoice:** `InvoiceFollowup`, invoice controller `$followupsaved`.
- **Form fields:** `assignnote`, `popoverdatetime`, `popoverdate`, `rem_cat`, etc. — keep for selectors unless you refactor JS.
- **Data attributes:** `data-followupdate` — optional to rename; if so, update all JS that reads it.

---

## Summary

| Category | Count |
|----------|-------|
| Routes | 9 |
| Controller | 43 |
| Service | 5 |
| Views (Blade) | 30 |
| JavaScript | 3 |
| **Total** | **~90** |

**Files touched:** ~18.

---

## Suggested Order of Application

1. **Routes** — Update `clients.php` and `web.php` (URLs and route names). Add new action routes; keep old follow-up routes temporarily if you need backward compatibility.
2. **Controllers** — Rename methods and update route references. Point routes to new method names.
3. **Services** — Rename `DashboardService` methods and update callers.
4. **Views** — Update form actions, config keys, labels, and `note_type`.
5. **JavaScript** — Update URLs, config keys, and `note_type`.
6. **Reports** — Rename `followup.blade.php` → `action_calendar.blade.php`, update controller and route.
7. **Search** — Grep for `followup`, `follow_up`, `Followup Assigned`, `clientFollowup`, etc., to catch any missed references.
8. **Test** — Create/reassign/complete actions from client detail, Action tab, partner modals, and report/calendar. Verify notifications and redirects.

---

*Last updated: 2026-01-25. To be applied tomorrow.*
