# PostgreSQL Performance Rollout Plan (Page / URL Wise)

**Purpose:** Apply database and application performance improvements in phases, with measurable before/after checks per URL.

**Stack:** Laravel 13, PostgreSQL  
**Measurement standard:** Server HTML **TTFB** (Time To First Byte). Google “good” TTFB is **≤ 800 ms**.  
**Reference:** Baseline timings in [`slow-pages.md`](../slow-pages.md) (measured Aug 2026 on local Postgres).

**Status:** Planning only — nothing in this document has been applied automatically.

---

## Context: Is 1–2 Lakh Rows a Problem?

No — PostgreSQL handles 100k–200k rows per table easily when queries are indexed and paginated. Degradation usually comes from:

- Missing indexes on `WHERE` / `JOIN` / `ORDER BY` columns
- Full table scans (`LOWER(TRIM(column))`, `LIKE '%term%'`)
- Loading all rows instead of paginating
- Large `TEXT` columns (`notes`, `emails`, `activities_logs`)
- Log tables growing without archival
- Running `count()` + `paginate()` (double scan) on every list page

---

## Step 0 — Baseline (Do Once Before Any Change)

1. Use **production** or a DB copy with real 1–2 lakh row counts.
2. Log in as **super admin** (same user for every test).
3. For each URL in the phases below, record **TTFB** (DevTools → Network → document request).
4. Note filter state used (default vs filtered) so retests are comparable.
5. On Postgres, run `EXPLAIN (ANALYZE, BUFFERS)` for the 3 slowest pages.
6. Enable `pg_stat_statements` and snapshot the top 10 slow queries.

### Baseline tracking sheet

| URL | Filters / notes | Before TTFB | After TTFB | Pass? (≤800ms) |
|-----|-----------------|-------------|------------|----------------|
| | | | | |

**Pass rule:** TTFB ≤ 800 ms, **or** at least **50%+ improvement** vs baseline before moving to the next phase.

---

## Phase 1 — Database Indexes (Low Risk, Cross-Cutting)

Apply as one migration batch on production using `CREATE INDEX CONCURRENTLY`.

| # | Index to add | Primary pages benefited |
|---|--------------|-------------------------|
| 1.1 | `notifications (receiver_id, created_at DESC)` | `/all-notifications` |
| 1.2 | `notifications (receiver_id, receiver_status)` | `/all-notifications` |
| 1.3 | `applications (client_id)` | Client detail, sheets, dashboard |
| 1.4 | `applications (user_id, status)` | Sheets, `/applications-finalize` |
| 1.5 | `applications (checklist_sheet_status)` | Checklist / ongoing sheets |
| 1.6 | `staff_login_logs (user_id, created_at DESC)` | `/audit-logs` |
| 1.7 | `staff_login_logs (created_at)` | `/audit-logs` |
| 1.8 | `client_phones (client_id)` | Client detail, SMS |
| 1.9 | `invoices (client_id)` | Client detail → Accounts tab |
| 1.10 | `invoice_details (invoice_id)` | `/invoice/edit/{id}` |
| 1.11 | `account_client_receipts (client_id)` | Receipts / commission report |

### Already well indexed (verify in production)

| Table | Notes |
|-------|-------|
| `activities_logs` | 8 indexes (dashboard, client activity, recent clients) |
| `notes` | 4 indexes including partial for open actions |
| `documents` | 7 indexes |
| `emails` | `(user_id, client_id, created_at)` |
| `sms_logs` | Good coverage |
| `admins` | `user_id`, `assignee`, `assignee` trgm |

### Re-test after Phase 1

| URL | Baseline (from slow-pages) | Target after Phase 1 |
|-----|----------------------------|----------------------|
| `/audit-logs` | ~6.3 s (~19 MB HTML) | < 2 s |
| `/all-notifications` | ~0.9 s | < 0.5 s |
| `/clients/detail/{id}` | measure | 10–30% faster |

---

## Phase 2 — Critical Slow Pages (Client Sheets)

**Shared root cause:** `applications` ↔ `admins` join + `LOWER(TRIM(stage))` in `WHERE` prevents btree index use.

### 2A — Ongoing Sheet

| | |
|---|---|
| **URL** | `/clients/sheets/ongoing` |
| **Baseline** | ~7.2 s |

| Step | Action |
|------|--------|
| 2A.1 | Add index: `applications (status, stage)` or partial indexes per sheet type |
| 2A.2 | Add `admins` partial index: `(type, office_id) WHERE is_archived = 0 AND is_deleted IS NULL` |
| 2A.3 | **Code:** replace `LOWER(TRIM(stage))` with normalized `stage` column or functional index |
| 2A.4 | Default date filter (e.g. last 90 days) when no filter selected |
| 2A.5 | Re-test with same filters as baseline |

### 2B — COE Issued & Enrolled

| | |
|---|---|
| **URL** | `/clients/sheets/coe-enrolled` |
| **Baseline** | ~6.5 s |

Same steps as 2A. Add index on `(status, stage)` for stages `coe issued`, `enrolled`.

### 2C — Discontinue Sheet

| | |
|---|---|
| **URL** | `/clients/sheets/discontinue` |
| **Baseline** | ~6.1 s |

Index: `applications (status)` where `status = 2`.

### 2D — Refund Sheet

| | |
|---|---|
| **URL** | `/clients/sheets/refund` |
| **Baseline** | ~5.9 s |

Index: `applications (status)` where `status = 8`.

### 2E — Checklist Sheet

| | |
|---|---|
| **URL** | `/clients/sheets/checklist` |
| **Baseline** | ~5.9 s |

Index: `applications (checklist_sheet_status, stage)`.

### 2F — Sheets Insights

| | |
|---|---|
| **URL** | `/clients/sheets/insights` |
| **Baseline** | ~2.5 s |

Cache aggregate counts (Redis, 5 min TTL) instead of live full-table scans.

### Re-test after Phase 2

All six sheet URLs — target **< 2 s** each.

---

## Phase 3 — List Pages (Clients / Leads / Reports)

### 3A — Clients List

| | |
|---|---|
| **URL** | `/clients` |
| **Baseline** | ~2.2 s |

| Step | Action |
|------|--------|
| 3A.1 | Index: `admins (type, is_archived, created_at DESC)` partial for active clients |
| 3A.2 | **Code:** remove or defer `count()` — paginate without total or use cached count |
| 3A.3 | **Code:** lazy `withCount(applications)` — only when column is shown |
| 3A.4 | `pg_trgm` on `first_name`, `last_name`, `email` if name search is slow |

### 3B — Leads List

| | |
|---|---|
| **URL** | `/leads` |
| **Baseline** | ~0.9 s |

Same `admins` indexes with `type = lead`.

### 3C — Archived Clients

| | |
|---|---|
| **URL** | `/archived` |
| **Baseline** | ~1.0 s |

Index: `admins (is_archived, archived_on DESC)`.

### 3D — Visa Expiry Report

| | |
|---|---|
| **URL** | `/reports/visaexpires` |
| **Baseline** | ~1.9 s (~4 MB HTML) |

Index: `admins (visaexpiry)` where not null.

### Re-test after Phase 3

`/clients`, `/leads`, `/archived`, `/reports/visaexpires`.

---

## Phase 4 — Actions Module

### 4A — All Actions

| | |
|---|---|
| **URL** | `/action` |
| **Baseline** | ~2.4 s |

| Step | Action |
|------|--------|
| 4A.1 | Confirm `notes_open_actions_assign_date_idx` exists in production |
| 4A.2 | **Code:** server-side pagination only (no `->get()` on full set) |
| 4A.3 | Default filter: open actions + last 30 days |

### 4B — Assigned By Me

| | |
|---|---|
| **URL** | `/action/assigned-by-me` |
| **Baseline** | ~3.3 s (was ~5.1 s before prior fix) |

| Step | Action |
|------|--------|
| 4B.1 | Index: `notes (assigned_to, status, action_assign_date)` if missing |
| 4B.2 | Verify single grouped count query is deployed in production |

### Re-test after Phase 4

`/action`, `/action/assigned-by-me` — target **< 1 s**.

---

## Phase 5 — Partners

### 5A — Partners List

| | |
|---|---|
| **URL** | `/partners` |
| **Baseline** | ~4.3 s |

| Step | Action |
|------|--------|
| 5A.1 | Index: `partners (status, created_at)` or active-only partial |
| 5A.2 | **Code:** paginate + `select` only list columns |
| 5A.3 | Eager-load counts in one query, not per row |

### 5B — Inactive Partners

| | |
|---|---|
| **URL** | `/partners-inactive` |
| **Baseline** | ~4.0 s |

Same as 5A with inactive status filter.

### Re-test after Phase 5

`/partners`, `/partners-inactive` — target **< 1.5 s**.

---

## Phase 6 — Dashboard & Staff

### 6A — Dashboard

| | |
|---|---|
| **URL** | `/dashboard` |
| **Baseline** | ~2.2 s |

| Step | Action |
|------|--------|
| 6A.1 | Confirm dashboard index migration applied (`activities_logs`, `notes` by `created_by`) |
| 6A.2 | Cache “My Day” widget data (5 min TTL) |
| 6A.3 | Limit default date range to today / this week |

### 6B — Staff Active

| | |
|---|---|
| **URL** | `/staff/active` |
| **Baseline** | ~1.6 s |

Re-test after Phase 1; usually sufficient.

### Re-test after Phase 6

`/dashboard`.

---

## Phase 7 — Client / Lead Detail

| | |
|---|---|
| **URLs** | `/clients/detail/{id}`, `/leads/detail/{id}` |
| **View** | `resources/views/Admin/clients/detail.blade.php` |

| Step | Action |
|------|--------|
| 7.1 | **Code:** load only active tab via AJAX (notes, activities, emails, documents per tab) |
| 7.2 | Verify `client_id` indexes on child tables exist in production |
| 7.3 | **Code:** fix INV-2 residual (~line 1775) — use `@$applicationdata->workflow` instead of `Workflow::where('id', $invoicelist->application_id)` |
| 7.4 | Paginate activities and notes (20–50 per page, not all rows) |

### Re-test after Phase 7

Test three clients: low activity, average, and heavy (many notes/activities).

---

## Phase 8 — Signatures & Elite Email

| URL | Baseline | Action |
|-----|----------|--------|
| `/signatures` | ~3.1 s | Index `documents` by signature status; paginate list |
| `/signatures/create` | ~1.7 s | Lazy-load client dropdown (search API, not full `admins` list) |
| `/emails/elite` | ~1.0 s | Default 30-day date filter; indexes already present |

---

## Phase 9 — Ongoing Maintenance

| # | Task | Frequency |
|---|------|-----------|
| 9.1 | `VACUUM ANALYZE` on `activities_logs`, `notes`, `emails`, `staff_login_logs` | Weekly |
| 9.2 | Archive `activities_logs` / `staff_login_logs` older than 2 years | Quarterly |
| 9.3 | Drop `mail_reports_conv_migration_backup_*` after verification | Once |
| 9.4 | Review `pg_stat_statements` top 10 slow queries | Monthly |
| 9.5 | Consider table partitioning on log tables when rows exceed ~10 lakh | As needed |

---

## Full URL Checklist (All Measured Pages)

Use this master list to track every URL from `slow-pages.md` plus detail pages.

| Phase | URL | Baseline TTFB | After TTFB | Pass? |
|-------|-----|---------------|------------|-------|
| 2 | `/clients/sheets/ongoing` | 7.2 s | | |
| 2 | `/clients/sheets/coe-enrolled` | 6.5 s | | |
| 1 | `/audit-logs` | 6.3 s | | |
| 2 | `/clients/sheets/discontinue` | 6.1 s | | |
| 2 | `/clients/sheets/checklist` | 5.9 s | | |
| 2 | `/clients/sheets/refund` | 5.9 s | | |
| 5 | `/partners` | 4.3 s | | |
| 5 | `/partners-inactive` | 4.0 s | | |
| 4 | `/action/assigned-by-me` | 3.3 s | | |
| 8 | `/signatures` | 3.1 s | | |
| 2 | `/clients/sheets/insights` | 2.5 s | | |
| 4 | `/action` | 2.4 s | | |
| 6 | `/dashboard` | 2.2 s | | |
| 3 | `/clients` | 2.2 s | | |
| 3 | `/reports/visaexpires` | 1.9 s | | |
| 8 | `/signatures/create` | 1.7 s | | |
| 6 | `/staff/active` | 1.6 s | | |
| — | `/applications-finalize` | 1.3 s | | |
| — | `/leads/create` | 1.2 s | | |
| — | `/partners/create` | 1.0 s | | |
| 8 | `/emails/elite` | 1.0 s | | |
| 3 | `/archived` | 1.0 s | | |
| 1 | `/all-notifications` | 0.9 s | | |
| — | `/followups` | 0.9 s | | |
| 3 | `/leads` | 0.9 s | | |
| — | `/invoice/edit/{id}` | 0.8 s | | |
| 7 | `/clients/detail/{id}` | measure | | |
| 7 | `/leads/detail/{id}` | measure | | |

---

## Recommended Rollout Order

```
Step 0  → Baseline all URLs
Phase 1 → Indexes (one migration)     → re-test audit-logs, notifications, client detail
Phase 2 → Sheets (biggest win)        → re-test 6 sheet URLs
Phase 3 → Client / lead lists         → re-test /clients, /leads, /archived, visa report
Phase 4 → Actions                     → re-test /action pages
Phase 5 → Partners                    → re-test /partners
Phase 6 → Dashboard
Phase 7 → Client detail
Phase 8 → Signatures / elite email
Phase 9 → Ongoing maintenance
```

---

## Index Gap Summary (Tables With Only Primary Key Today)

| Table | Suggested indexes |
|-------|-------------------|
| `notifications` | `(receiver_id, created_at)`, `(receiver_id, receiver_status)` |
| `applications` | `(client_id)`, `(user_id, status)`, `(checklist_sheet_status)` |
| `staff_login_logs` | `(user_id, created_at)`, `(created_at)` |
| `client_phones` | `(client_id)`, `(phone, country_code)` |
| `invoices` | `(client_id)` |
| `invoice_details` | `(invoice_id)` |
| `account_client_receipts` | `(client_id)` |
| `checkin_logs` | `(client_id)`, `(status, created_at)` |
| `admins` | Partial list index; `(office_id, type)`; `(visaexpiry)`; trgm on name/email |

---

## Application Patterns to Fix (Non-Index)

1. **`LOWER(TRIM(column))` in WHERE** — use normalized column or functional index (ongoing sheets).
2. **Double scan on lists** — `count()` then `paginate()` on `/clients` and similar.
3. **Large TEXT in list queries** — avoid selecting body/HTML columns on index pages.
4. **No archival** — `activities_logs`, `staff_login_logs`, old `notifications` grow forever.
5. **`LIKE '%term%'`** — needs `pg_trgm`, not btree.
6. **N+1 queries** — use eager loading / batch lookups (`ClientDetailEagerLoads` pattern).

---

## Related Files

| File | Purpose |
|------|---------|
| [`slow-pages.md`](../slow-pages.md) | Original TTFB measurements |
| [`database/migrations/2026_09_22_150110_add_dashboard_performance_indexes.php`](../database/migrations/2026_09_22_150110_add_dashboard_performance_indexes.php) | Dashboard / My Day indexes |
| [`database/migrations/2026_09_22_152700_add_admins_allocation_indexes.php`](../database/migrations/2026_09_22_152700_add_admins_allocation_indexes.php) | Admins assignee indexes |
| [`database/migrations/2026_02_11_100000_add_indexes_for_recent_clients_page.php`](../database/migrations/2026_02_11_100000_add_indexes_for_recent_clients_page.php) | Recent clients indexes |
| [`database/migrations/2026_06_25_120000_add_partner_detail_performance_indexes.php`](../database/migrations/2026_06_25_120000_add_partner_detail_performance_indexes.php) | Partner detail indexes |
| [`app/Http/Controllers/Admin/OngoingSheetController.php`](../app/Http/Controllers/Admin/OngoingSheetController.php) | Sheet queries (main bottleneck) |
| [`app/Traits/ClientQueries.php`](../app/Traits/ClientQueries.php) | Client list filters |
| [`resources/views/Admin/clients/detail.blade.php`](../resources/views/Admin/clients/detail.blade.php) | Client detail view |

---

*Last updated: 2026-09-25 — planning document only; no changes applied.*
