# My Day + Automatic Time-on-File — Porting Guide (bansalcrm2)

**Purpose:** implement silent **automatic time on student / lead / college / application files** in **bansalcrm2**, using the architecture shipped in migrationmanager2 (15 Sep 2026, Phases A–D).

**Upstream source:** `c:\xampp\htdocs\migrationmanager2\docs\MY_DAY_AUTO_FILE_TIME_PORTING.md`  
**Product plan (upstream):** `migrationmanager2/docs/PLAN_MY_DAY.md`  
**Related in this repo:** `docs/STAFF_WORKLOAD_EFFICIENCY.md` (existing My Day **workload** tiles — keep separate)

This document is the **bansalcrm2** adaptation: domain names, tables, existing code to reuse, and open decisions are filled in for this CRM. Follow the architecture and non-negotiable rules; do not copy migration-law matter names into schema.

---

## 0. How this fits what we already have

bansalcrm2 already ships a **My Day** block on the staff dashboard (`Admin.partials.my-day-panel`) driven by `StaffWorkloadService`:

| Existing (keep) | New (this port) |
|-----------------|-----------------|
| Hours in CRM (login/session strip) | Same strip — reuse; do not rebuild |
| Contact / throughput / caseload KPI tiles | **Untouched** — never inflate from auto/manual minutes |
| Quiet / inactive / applications worked lists | Untouched |
| — | “Already in CRM” event list (read-only diary of today’s writes) |
| — | Time on files (auto focused-tab sessions) |
| — | Files opened (`accessed` only) |
| — | Manual off-CRM logs + copy-summary |

**Reconcile with `STAFF_WORKLOAD_EFFICIENCY.md`:**

- That doc forbids **duration timers on Call / In-Person notes** and forbids using mouse/keystroke data as an **efficiency score**. This port does **neither**.
- Auto time is **focused-tab presence on a detail page**, attributed after promote/close — not “minutes typed into a note.”
- Idle mouse/keyboard listeners exist only to **cut over-credited focus** when staff walk away with the tab still focused — not to score productivity.

Workload cards stay CRM-only. `file_time` feed rows use `task_status = 0` and must be excluded from completed-action / throughput queries.

---

## 1. What you are building

Staff open a **student / lead / college / application** file in the CRM. While that browser tab is focused, the system quietly accrues time. When they leave (or the tab dies), minutes are attributed to that file and explained by the CRM writes they already made (notes, emails, uploads, stage moves, bookings, SMS).

On the personal dashboard, **My Day** gains (below existing workload tiles):

| Block | Source | Staff action |
|--------|--------|--------------|
| Hours in CRM | Existing login / session presence | None (header only — already present) |
| Already in CRM | Union of today’s CRM writes by this staff | Read-only |
| Time on files (auto) | Focused-tab sessions on detail pages | Edit minutes / delete only if not posted |
| Files opened | Sessions still `accessed` (opened, no promote yet) | Read-only |
| Manual logs | Off-CRM work staff choose to log | Modal: student/application/college or Admin + minutes + kind |
| End-of-day summary | Server-built plain text | Copy (and optionally save for managers) |

**Workload KPI cards stay untouched.** Auto/manual minutes must never inflate “Spoke to / Met / Completed / stage moves” style metrics.

---

## 2. Domain mapping (migration CRM → bansalcrm2)

| migrationmanager2 | bansalcrm2 | Role in the design |
|-------------------|------------|--------------------|
| `staff` (`auth:admin`) | `staff` via `Auth::guard('admin')` (`App\Models\Staff`) | Writer of time and CRM events |
| `admins` / client / lead / company | **Student/lead:** `admins` (`App\Models\Admin`, `type` = `client` \| `lead`). **College:** `partners` (`App\Models\Partner`) | The **person or org file** being viewed |
| `client_matters` | `applications` (`App\Models\Application`) | Optional **case** under a student; unit of timed work when an application is selected on detail |
| Matter ref (`client_unique_matter_no`) | Application label (e.g. partner + product + stage, or `#application_id`) | Human-readable label in UI + feed |
| Call / In-person file notes | `notes` with `title` `Call` / `In-Person` (`type` client path or `partner`) | CRM events that **promote** a session |
| Immi / mailbox presets (manual only) | **PRISMS**, provider portal, Outlook skim, internal discussion | Manual overlay kinds only — **not** auto |
| `activities_logs` feed | `activities_logs` (+ `application_activities_logs` for stage moves) | One `file_time` row per recorded session on the student/partner feed |

**Rule that ports cleanly:** time attaches to the **application** when one is selected on student/lead detail; otherwise to the **student/lead** (`admins.id`). One student with two applications = two auto sessions if both are opened (switch application → flush old session, start new).

**College files:** sessions attach to `partners.id` (no application). Use a **record type** so partner ids are never confused with `admins.id` (see §8 and ActivitiesLog note below).

### Critical bansalcrm2 gotcha — `activities_logs.client_id`

Partner activity rows **reuse** `client_id` for `partners.id` and set `task_group = partner` (`ActivitiesLog::TASK_GROUP_PARTNER`). Student queries must use `ActivitiesLog::forStudentRecords()` (or equivalent). Auto session feed posts and promotion lookups must honour the same split — never treat partner id 5910 as student 5910.

Staff credit on activities uses `created_by` → **`staff.id`**. Do not trust `ActivitiesLog::createdBy()` (it points at `Admin`).

---

## 3. Architecture (keep layers separate)

```
Detail page (student / lead / partner)
  └── file-time-session.js
        POST heartbeat / blur / idle-cut
              ↓
        StaffFileSessionService          (rename of upstream StaffMatterSessionService)
              ↓ promote via StaffDayCrmEventsService::forStaffOnRecord()
              ↓ close via scheduled my-day:close-stale-sessions
              ↓ post activities_logs activity_type = file_time

Dashboard My Day
  ├── StaffWorkloadService / DashboardService   → existing hours + KPI tiles (DO NOT MERGE)
  ├── StaffDayHoursService (optional thin wrap) → “Hours in CRM” for diary header if needed
  ├── StaffDayCrmEventsService                  → “Already in CRM” (read-only)
  ├── StaffFileSessionService                   → auto + opened
  └── StaffFileTimeService                      → manual overlay + copy summary text
```

**Two tables, never merged:**

| Table | Lifecycle | Has kind/title? | One-running timer? |
|-------|-----------|-----------------|--------------------|
| `staff_file_sessions` (or `staff_matter_sessions` if keeping upstream name) | Silent focus sessions | No | No (many open files OK) |
| `staff_file_time_entries` | Manual / logged overlay | Yes | Yes (at most one `is_running` per staff) |

Prefer **`staff_file_sessions`** naming in bansalcrm2 (we have applications, not matters). Auto sessions **must not** pause manual timers and **must not** write into the overlay table.

---

## 4. Non-negotiable product rules

1. **No double-logging for in-CRM work.** If the staff wrote a note/email/upload/stage move while the file was open, auto time covers it. Manual presets must not duplicate “sent email”, “created note”, etc.
2. **Credit the writer only.** Sessions and events use the logged-in `staff.id`. Never credit a colleague for someone else’s tab. Caseload assignee / application `user_id` is irrelevant for auto time.
3. **Focused time only.** Count only while `document.hasFocus() && visibilityState === 'visible'`. Blur / tab switch / close stops counting immediately (no grace). Gap is not credited; refocus continues the same day session.
4. **Idle cut.** 15 min no input while focused → “Still on {ref}?”. No answer in 2 min → cut seconds back to idle start.
5. **Promotion, not button.** Session starts `accessed`. Becomes `recorded` when any CRM write by this staff on this record lands in `[started_at, last_heartbeat_at]`, **or** after ≥ 2 minutes focused with no write (“reviewed file”).
6. **Under 2 min + no write stays `accessed`.** Shows under “Files opened”, no feed post.
7. **Feed only for `recorded` (then closed).** One timeline row per session; never for pure `accessed`.
8. **Personal view only.** No “view as colleague” on the dashboard. Manager reports (if any) use a stored end-of-day snapshot, not live peeking. Same rule as existing My Day workload.
9. **Workload cards CRM-only.** `file_time` feed rows use `task_status = 0` and must be excluded from `StaffWorkloadService` / completed-action queries.

---

## 5. Automatic time-on-file (deep dive)

Core layer — port this first if you only ship one piece.

### 5.1 Session identity

One row per:

```
staff_id × record_type × record_id × application_key × session_date
```

| Field | bansalcrm2 meaning |
|-------|--------------------|
| `staff_id` | `staff.id` |
| `record_type` | `student` \| `partner` (leads use `student` — same `admins` row) |
| `record_id` | `admins.id` or `partners.id` |
| `application_key` | `COALESCE(application_id, 0)` — student/lead only; always `0` for partner |
| `session_date` | Business timezone date via existing `StaffWorkloadService::dayBounds()` / `config('app.timezone')` (**Australia/Melbourne**) |

- Reopening the same file later the same day **continues** the same row (focused seconds only ever increase via `max(existing, incoming)`).
- Switching application on student detail (`data-application-id` / `?applicationId=`) = **new record**: flush blur for old application, reset local accumulator, heartbeat for new application.
- Sheets (Ongoing, Checklist, COE, …) and list/search pages → **no** session (detail pages only).

### 5.2 Status machine

```
accessed ──write in window──► recorded ──stale/close──► closed (+ feed if minutes ≥ 1)
    │                              ▲
    └── ≥120s focused, no write ───┘   (is_reviewed_only = true)
```

Same-day heartbeat after close **reopens** to `recorded` or `accessed` (clears `ended_at`). Overnight dead tabs are handled by stale heartbeat, not a midnight job.

### 5.3 Heartbeat contract

| Constant | Value | Meaning |
|----------|-------|---------|
| Heartbeat interval | 60s | While focused |
| Stale threshold | 180s | No heartbeat → ended at `last_heartbeat_at` |
| Reviewed-only | 120s | Focused, no write → still `recorded` |
| Idle warning | 15 min | No mouse/keyboard/scroll/click/touch |
| Idle grace | 2 min | Then `idle-cut` |

**Client** maintains a local `focusedSeconds` accumulator (`requestAnimationFrame` while focused). Each heartbeat sends the **total for that session today**, not a delta. Server stores `max(db, client)`.

**Blur / `pagehide`:** prefer `navigator.sendBeacon` with `FormData` including `_token` (CSRF). Fallback to `fetch` + keepalive.

**Multi-tab:** `BroadcastChannel('my-day-session')` — when another tab gains focus on a *different* record, stop local tick (server still authoritative via last heartbeat).

### 5.4 Promotion (server-side only)

On heartbeat (and again just before close):

1. Query CRM events for this staff + record + window `[started_at, last_heartbeat_at]` via `StaffDayCrmEventsService::forStaffOnRecord(...)`.
2. If any event → `recorded`, `is_reviewed_only = false`.
3. Else if `focused_seconds >= 120` → `recorded`, `is_reviewed_only = true`.
4. Else stay `accessed`.

**Do not** hang promotion off every write controller. Detect on heartbeat/close by reusing the same event union the dashboard already lists.

**Attribution rule for notes without an application id:** if the session has a selected `application_id`, events with `application_id` null on that student still count for the open application. Events with a *different* `application_id` do not.

### 5.5 Closing and feed post

Scheduled every 5 minutes:

```
my-day:close-stale-sessions
→ StaffFileSessionService::closeStale(now())
```

Register in `app/Console/Kernel.php` (alongside existing `withoutOverlapping` jobs). Ensure production cron runs the scheduler.

For each non-closed row with `last_heartbeat_at < now − 180s`:

1. Run `promoteIfWritten` again (note saved seconds before tab death must count).
2. `ended_at = last_heartbeat_at` (not `now()`).
3. `confirmed_minutes = round(focused_seconds / 60)` if unset.
4. If was `recorded` and minutes ≥ 1 → post/update feed row.
5. Status → `closed`.

Feed subject shapes:

- With activities: `logged {N}m on {ref} · {count} activities`
- Reviewed only: `logged {N}m on {ref} · reviewed file`

`activity_type = file_time`, `created_by = staff_id`, `task_status = 0`, `pin = 0`.

- Student/lead sessions: `client_id = admins.id`, `task_group` null / non-partner; set `use_for` if the column is used for application linkage.
- Partner sessions: `client_id = partners.id`, `task_group = partner`.

Editing minutes after post **updates** the same feed row.

### 5.6 Minutes split across CRM events (display only)

At **read time** for the board / copy summary:

- Load events for the session window.
- Sort stably by `sort_at`, then event key.
- Split `confirmed_minutes` evenly: `base = intdiv(N, count)`, remainder goes to the **first** event so the sum is exact.
- Store nothing per-event in the DB.

These chips also decorate “Already in CRM” rows when event keys match.

### 5.7 Detail-page wiring

Emit once on every record detail view:

| Page | Blade / route | Bootstrap |
|------|---------------|-----------|
| Student | `resources/views/Admin/clients/detail.blade.php` (`clients.detail`) | `recordType: 'student'`, `recordId`, `applicationId` from `$applicationId` / tab `data-application-id` |
| Lead | same detail stack via `leads.detail` | Same as student (`admins` row) |
| College | `resources/views/Admin/partners/detail.blade.php` (`partners.detail`) | `recordType: 'partner'`, `applicationId: null` |

```js
window.MyDaySession = {
  recordType: 'student' | 'partner',
  recordId: <admins.id or partners.id>,
  applicationId: <applications.id or null>,
  ref: '<human label>',
  csrf: '<token>',
  routes: {
    heartbeat: '.../sessions/heartbeat',
    blur: '.../sessions/blur',
    idleCutBase: '.../sessions'  // append /{id}/idle-cut
  }
};
```

Load a **standalone** script under `public/js/` (no dependency on the main detail bundle). Tracking must never throw into the page UI — swallow network errors.

**Access control:** heartbeat/blur must re-check the staff can open that record (same gate as the detail page, including CRM access grants where applicable). Ownership checks on update/delete/idle-cut by `session.staff_id`.

---

## 6. Manual overlay (second layer)

Use only for work the CRM **cannot** see: drafting offline, government / provider portals, mailbox skim *on this file*, internal discussion, Admin/no-file admin work.

| Field | Notes |
|-------|--------|
| `kind` | Education presets (see §14 decisions) |
| `title` | Required short text |
| `application_id` / `record_*` | Nullable; null + admin flag = Admin row |
| `status` | Prefer **log completed minutes** modal (no live timer) for v1 catch-up |
| `confirmed_minutes` | 1–480 at Done |
| `is_running` | If live timer is shipped: partial unique one true per staff |

On Done with a linked student/application → post `file_time` to that feed. Partner Done → partner feed (`task_group = partner`). Admin rows stay on My Day only (no feed).

---

## 7. “Already in CRM” event union

Read-only service. Cap list (e.g. 50) with “and N more”. Reuse the **actor** attribution rules from `docs/STAFF_WORKLOAD_EFFICIENCY.md` §3.

Typical sources (writer = this staff, today in `Australia/Melbourne`):

| Kind | Source in bansalcrm2 |
|------|----------------------|
| Contact note | `notes` created by staff: `title` Call / In-Person (student or partner) — not assigned tasks |
| Email | `emails.user_id` staff-sent / attributed (`type` client/lead/partner); exclude inbound-only / system |
| Document | `documents` (`created_by` prefer / `user_id`) |
| SMS | `sms_logs.sender_id` outbound |
| Stage / app feed | `application_activities_logs` (`user_id`, e.g. `type = stage`) |
| Booking / follow-up | Appointments / follow-up creates by staff (diary — list as CRM write, not Layer A contact) |
| Other feed | `activities_logs.created_by` staff writes — **exclude** duplicate “added a note” audit copies when the note itself is already in the union; **exclude** `activity_type = file_time` |

Each event row shape used downstream:

```
key, kind, title, time, sort_at, record_type, record_id, application_id?, ref?
```

`forStaffOnRecord(...)` filters the same union by record (+ application-null attribution rule). Keep `Schema::hasTable` guards so unit tests can omit unused tables.

---

## 8. Schema to copy

### 8.1 `staff_file_sessions` (auto)

| Column | Type | Purpose |
|--------|------|---------|
| `id` | PK | |
| `staff_id` | FK `staff`, cascade | Writer |
| `record_type` | string(16) | `student` \| `partner` |
| `record_id` | unsigned int | `admins.id` or `partners.id` |
| `application_id` | nullable FK `applications` | Selected application (student only) |
| `application_key` | unsigned int default 0 | `COALESCE(application_id, 0)` for uniqueness |
| `session_date` | date | Business day |
| `status` | string(16) | `accessed` \| `recorded` \| `closed` |
| `focused_seconds` | unsigned int | Accrued focus |
| `idle_cut_seconds` | unsigned int | Audit of idle removals |
| `confirmed_minutes` | smallint nullable | Set at close; editable |
| `event_count` | smallint nullable | Snapshot for feed subject |
| `is_reviewed_only` | bool | Rule: ≥2m no write |
| `started_at` / `last_heartbeat_at` / `ended_at` | timestamps | |
| `activities_log_id` | nullable unique FK | Feed row |
| timestamps | | |

Indexes:

- **Unique** `(staff_id, record_type, record_id, application_key, session_date)`
- `(staff_id, session_date)`
- `(last_heartbeat_at)` for closer
- Unique `activities_log_id`

Create/find under `lockForUpdate()` so concurrent heartbeats do not duplicate.

### 8.2 `staff_file_time_entries` (manual)

| Column | Purpose |
|--------|---------|
| `staff_id`, `record_type?`, `record_id?`, `application_id?` | Writer + target |
| `kind`, `title` | Preset + text |
| `status`, `is_running` | Timer state (optional for v1) |
| `clock_seconds`, `confirmed_minutes` | Helper clock vs published minutes |
| `started_at`, `completed_at` | |
| `activities_log_id` | Feed when posted |

Partial unique: `(staff_id) WHERE is_running = true` if live timer ships. Confirm DB engine support (MySQL 8+ functional/partial indexes vary — enforce `pauseAllRunning` in service either way).

### 8.3 Optional `staff_day_summaries`

Store the copied end-of-day text for manager/admin workload views (`AdminConsole/staff_workload`). Snapshot nightly if you need historical Teams dumps without recompute. Personal dashboard stays live-only.

---

## 9. HTTP API surface

All authenticated as CRM staff (`auth:admin`). JSON.

Suggested prefix (align with existing `/my-day` redirect → dashboard `#my-day`):

### Auto sessions

| Method | Path | Body / notes |
|--------|------|--------------|
| POST | `/dashboard/my-day/sessions/heartbeat` | `record_type`, `record_id`, `application_id?`, `focused_seconds` (0–86400) |
| POST | `/dashboard/my-day/sessions/blur` | Same (beacon-friendly) |
| POST | `/dashboard/my-day/sessions/{id}/idle-cut` | `idle_started_at` ISO |
| PATCH | `/dashboard/my-day/sessions/{id}` | `confirmed_minutes` 1–480 |
| DELETE | `/dashboard/my-day/sessions/{id}` | Only if `activities_log_id` null |

### Manual overlay

| Method | Path | Notes |
|--------|------|-------|
| GET | `/dashboard/my-day/diary` (or embed in dashboard payload) | Board + sessions + events |
| POST | `/dashboard/my-day/file-time` | Start timer (if shipped) |
| POST | `/dashboard/my-day/file-time/log` | One-shot completed log (**preferred v1**) |
| POST | `.../pause\|park\|resume\|done\|reopen` | Lifecycle if live timer |
| PATCH / DELETE | `.../file-time/{entry}` | Open only for delete |

### Summary

| Method | Path | Notes |
|--------|------|-------|
| GET | `/dashboard/my-day/copy-summary` | Plain text + structured sections |
| POST | `/dashboard/my-day/copy-summary` | Optional save snapshot |
| GET | `/dashboard/my-day/record-search?q=` | Typeahead students/applications/partners; respect access rules |

Wire controllers under `App\Http\Controllers\Admin\` (or `CRM\`) next to `MyDayController`. Register routes in `routes/web.php` with `auth:admin`.

---

## 10. Dashboard UI composition

Existing layout (`Admin/dashboard.blade.php`):

1. Existing **My Day workload** panel (`my-day-panel`) — keep as-is  
2. **New diary sections** immediately **below** that panel (still above follow-up calendar / other widgets), inside `#my-day` or a sibling `#my-day-file-time`

Recommended diary sections (order):

1. Header: “My day diary” + Hours in CRM (reuse) + “+” manual log  
2. Already in CRM (list + optional minutes chips)  
3. Split: Time on files (auto) | Files opened  
4. Collapsible End-of-day: manual logs list + `<pre>` summary + Copy  

CSS: dedicated stylesheet tokens aligned with `.my-day-*` already in the panel. Distinct classes for auto vs manual vs opened (e.g. `.my-day-auto`, `.my-day-opened`).

JS: one script owning state from `data-initial-*` attributes; PATCH minutes on blur of inline inputs; **never** re-render workload KPI tiles on heartbeat.

---

## 11. Copy-summary text contract

Server builds the string so clipboard matches storage:

```
{Staff name} — {date}
Hours in CRM: …

— Already in CRM —
{ref} · {kind} · {title} · {time} · {Nm?}

— Manual logs —
{ref} · {kind} · {title} · {Nm}

— Admin / no file —
…

— Time on files (auto) —
{ref} · {Nm} · {N activities|reviewed file}

— Files opened —
{ref} · {Nm} (open)

— Still open —
{ref} · {kind} · {title} · {status}
```

Minutes on CRM event lines prefer auto-session splits; otherwise share remaining record minutes across that file’s events (deterministic sort).

---

## 12. Scheduler / ops

| Command | Schedule | Job |
|---------|----------|-----|
| `my-day:close-stale-sessions` | every 5 minutes, withoutOverlapping | Close dead heartbeats + feed post |
| `my-day:snapshot-summaries` (optional) | nightly | Persist copy text for managers |

Without the closer, sessions stay open forever and never post. Verify cron hits `php artisan schedule:run` in each environment.

---

## 13. Suggested build order (green at each step)

1. **Schema + models + factories** for `staff_file_sessions` (and overlay if shipping both).
2. **CRM events service** (`forStaff` + `forStaffOnRecord`) + unit tests — align with workload attribution.
3. **Session service** (heartbeat, blur, idleCut, promote, closeStale, board, feed) + unit tests — no UI yet.
4. **Closer command + schedule** in `app/Console/Kernel.php`.
5. **Routes + Form Requests + controller** + feature auth tests.
6. **Detail script + Blade config** on student/lead + partner detail — sessions start accruing silently.
7. **Dashboard diary Blade/CSS/JS** below existing workload panel — staff see auto + opened + copy.
8. **Manual overlay** — log-minutes modal + board + feed on Done.
9. **Activity-feed icon** for `file_time`; confirm `StaffWorkloadService` and action queries exclude it.

Ship 1–4 without UI if you want a safe merge: sessions simply do not get created until step 6.

---

## 14. Porting checklist for bansalcrm2

### Must map — filled for this CRM

- [x] Staff auth guard and id: `auth:admin` → `staff.id`
- [x] Record primary key on detail: `admins.id` (student/lead), `partners.id` (college)
- [x] Optional case id: `applications.id` when application selected on student detail
- [x] Business timezone + day bounds: `StaffWorkloadService::dayBounds()` / `config('app.timezone')`
- [x] Activity timeline: `activities_logs` (+ partner `task_group`); insert with `task_status = 0`, never mark task complete
- [ ] Record access gate reused on heartbeat (mirror client/partner detail authorization + grants)
- [ ] List of write sources that count as “CRM work” for promotion (start from §7)

### Must decide — recommendations

| Decision | Recommendation for bansalcrm2 |
|----------|-------------------------------|
| Manual preset kinds | `prisms`, `provider_portal`, `mailbox`, `draft`, `internal`, `other` |
| Student vs partner identity | **Polymorphic** `record_type` + `record_id` (required — separate tables, overlapping ids) |
| Leads vs clients | Same `record_type = student` (both live in `admins`) |
| Manager visibility | Personal-only on dashboard; optional `staff_day_summaries` for Admin Console later |
| Live timer vs log modal | **Log-minutes-only modal for v1**; live timer optional later |
| Idle thresholds | Keep 15m / 2m |
| Table naming | Prefer `staff_file_sessions` over `staff_matter_sessions` |

### Must not do

- [ ] Do not store auto time inside `notes` / actions tables
- [ ] Do not let `file_time` inflate KPI / workload cards (`StaffWorkloadService`)
- [ ] Do not require a Start button for in-CRM file time
- [ ] Do not count background tabs or blurred windows
- [ ] Do not post feed rows for `accessed`-only opens
- [ ] Do not treat sheet list views as detail sessions
- [ ] Do not confuse partner `client_id` with student `admins.id` in feed/promotion
- [ ] Do not add note-level call duration timers (forbidden by workload spec)

### Education-specific gotchas (this CRM)

- Multiple applications per student → application switcher / `data-application-id` must flush sessions.
- Shared assignees on `admins.assignee` → still credit the **logged-in** staff only.
- Bulk list pages, sheets, search → **no** session.
- Embedded iframes / PRISMS / provider portals in another tab → only count while the CRM tab is focused; portal work is **manual** overlay.
- Student-from-college note flow (`PartnersController::addstudentnote`): promotion for a **partner** session may see a partner activity while the note sits on the student — decide in `forStaffOnRecord` so college sessions promote on college contact without double-counting student spoke-to semantics.

---

## 15. Edge cases learned upstream (still apply)

1. **Application id vs label.** Heartbeats need the **numeric** `applications.id` from the tab/`applicationId` query, not the human ref string.
2. **`sendBeacon` and CSRF.** Beacons cannot set custom headers; put `_token` in the form body.
3. **Focused seconds never decrease** except via explicit idle-cut. Clients that send lower totals after refresh must not wipe accrued time — use `max()`.
4. **Stale close uses last heartbeat, not now.** Otherwise laptop-sleep over-credits.
5. **Promote again at close.** Last write can land after the final heartbeat.
6. **Reviewed-only feed** still needs a synthetic event when splitting minutes for display.
7. **Partial unique `is_running`** needs DB support; enforce in service with `pauseAllRunning` either way.
8. **Partner detail** — confirm `recordId` is `partners.id` before wiring the script.
9. **Silent failures.** Tracking JS must not break counselling workflows; fail closed (no toast on network error).
10. **Tests without `RefreshDatabase`.** Build minimal sqlite `:memory:` schema in `setUp` (staff, admins, partners, applications, notes/docs/activities, sessions). Freeze time with `Carbon::setTestNow`. Reuse patterns from `tests/Unit/Services/StaffWorkloadServiceTest.php`.

---

## 16. Test matrix (minimum)

### Auto sessions

- Heartbeat creates one row per staff/record/application/day; second heartbeat updates, does not duplicate.
- Student vs partner with the **same numeric id** do not collide (`record_type` in unique key).
- Stale close ends at `last_heartbeat_at`.
- Idle cut reduces `focused_seconds` and increments `idle_cut_seconds`.
- Note/document/stage inside window → `recorded`; outside → stays `accessed`.
- ≥ 120s no write → `recorded` + `is_reviewed_only`; &lt; 120s → `accessed`.
- Close `recorded` posts `file_time`; close `accessed` does not.
- Partner feed posts set `task_group = partner`.
- Even split of minutes across N events sums to `confirmed_minutes`.
- Guest cannot hit routes; staff A cannot mutate staff B’s session.
- Auto never sets `is_running` on manual table; never changes workload tallies.

### Overlay / dashboard

- Manual Done with application/student → feed; Admin Done → no feed.
- Copy summary includes CRM + auto + manual + opened sections.
- Markup: existing workload strip/tiles still present; diary mounts below.

---

## 17. Source map

### Upstream (migrationmanager2) — copy behaviour from

| Concern | Path |
|---------|------|
| Auto session service | `app/Services/StaffMatterSessionService.php` |
| CRM event union | `app/Services/StaffDayCrmEventsService.php` |
| Manual overlay + copy text | `app/Services/StaffFileTimeService.php` |
| Hours header | `app/Services/StaffDayHoursService.php` |
| Session HTTP | `app/Http/Controllers/CRM/StaffMatterSessionController.php` |
| Overlay HTTP | `app/Http/Controllers/CRM/DashboardMyDayController.php` |
| Detail tracker | `public/js/crm/clients/file-time-session.js` |
| Detail bootstrap | `resources/views/partials/my-day-session-script.blade.php` |
| Dashboard UI | `resources/views/components/dashboard/my-day.blade.php` |
| Closer | `app/Console/Commands/CloseStaleMatterSessions.php` |
| Unit / feature tests | `tests/Unit/Services/StaffMatterSessionServiceTest.php`, `StaffDayCrmEventsServiceTest.php`, … |

### Target (bansalcrm2) — implement here

| Concern | Path |
|---------|------|
| Existing workload My Day | `app/Services/StaffWorkloadService.php`, `resources/views/Admin/partials/my-day-panel.blade.php` |
| Hours / login stats | `app/Services/DashboardService.php` (`getLoginStatistics`) |
| Dashboard host | `app/Http/Controllers/Admin/AdminController.php`, `resources/views/Admin/dashboard.blade.php` |
| My Day route redirect | `app/Http/Controllers/Admin/MyDayController.php`, `routes/web.php` (`staff.my-day`) |
| Student/lead detail | `resources/views/Admin/clients/detail.blade.php` |
| Partner detail | `resources/views/Admin/partners/detail.blade.php` |
| Activities | `app/Models/ActivitiesLog.php` (`forStudentRecords`, `TASK_GROUP_PARTNER`) |
| New session service | `app/Services/StaffFileSessionService.php` *(to create)* |
| New CRM events service | `app/Services/StaffDayCrmEventsService.php` *(to create)* |
| New manual + copy | `app/Services/StaffFileTimeService.php` *(to create)* |
| New closer | `app/Console/Commands/CloseStaleFileSessions.php` *(to create)* |
| Schedule | `app/Console/Kernel.php` |
| Workload product rules | `docs/STAFF_WORKLOAD_EFFICIENCY.md` |

---

## 18. One-paragraph mental model

**My Day diary** sits beside the existing **My Day workload** tiles: a read-only “what I already did” list, silent focused-tab time on open student/college/application files that promotes when those writes happen (or after a short review), optional manual minutes for off-CRM work (PRISMS, portals, mailbox), and a copy-paste summary for Teams — without asking staff to re-log in-CRM work or letting timers rewrite contact/throughput/caseload KPIs.

Keep that mental model, use **polymorphic record identity** for students vs partners, treat **applications** as the case unit, implement **auto sessions first**, and leave `StaffWorkloadService` alone.
