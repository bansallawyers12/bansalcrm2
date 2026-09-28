# HTTP polling & Australia performance — bansalcrm2

**Purpose:** Inventory where this CRM uses HTTP polling (and related timers), why staff in **Australia** can perceive slowness while **India** feels fine, and a **safe** improvement order that does not break office visits, My Day file time, or the Elite email inbox.

**Status:** Review / planning only — no changes applied from this document alone.  
**Related upstream:** `migrationmanager2` production Apache error log review (Sep 2025) — ModSecurity noise + brief PHP-FPM drops; same hosting patterns may apply to `*.bansalcrm.com` properties.  
**Last reviewed:** 2026-09-28

---

## 1. Executive summary

| Finding | Detail |
|--------|--------|
| Main AU slowness driver | **Geographic latency** to origin server (likely India/Asia) × **many small HTTP requests** per page |
| bansalcrm2 vs migrationmanager2 | bansalcrm2 polls **more aggressively** (office visits every **4 s** vs **10 s**) and has **Echo/Reverb effectively off** |
| Real production outage signal (shared hosting) | Brief PHP-FPM / FastCGI errors — affects all regions briefly, not AU-only |
| Safest fix order | Enable real-time **first**, keep polling as **fallback**, then tune intervals; infrastructure (region, PHP-FPM, ModSecurity) in parallel |

**One-line diagnosis for AU staff:**

> Same app and server as India, but every background poll costs ~200–350 ms round-trip from Australia vs ~30–80 ms from India — and bansalcrm2 **used to** poll office visits every 4 seconds on every admin page (§2.1 now gated off by default).

---

## 2. HTTP polling inventory (browser → server)

### 2.1 Global — every admin layout page (highest impact)

**Status:** ✅ **FIXED** (2026-09-28)

**Reason:** Global 4 s HTTP polling is **off by default** via feature flag `CRM_OFFICE_VISIT_POLLING_ENABLED` → `config('crm.office_visit_polling_enabled')` (`config/crm.php`, default `false`). `admin.blade.php` only runs `loadOfficeVisitNotifications`, the 4 s `setInterval`, visibility refresh, and per-popup `check-checkin-status` polling when that flag is `true`. With the flag off (current default), admin pages no longer generate the highest-impact AU background load from this section. Popup UI, button actions, routes, and the Echo listener hook are unchanged — only automatic background delivery stops until polling is re-enabled or Echo/Reverb is live.

**Re-enable (per environment):** set `CRM_OFFICE_VISIT_POLLING_ENABLED=true` in `.env`, then `php artisan config:clear` (or `config:cache` on production).

| What | Endpoint | Interval | File | When flag off |
|------|----------|----------|------|---------------|
| Office visit notifications | `GET /fetch-office-visit-notifications` | **4 s** (if enabled) | `resources/views/layouts/admin.blade.php` | **No background poll** |
| Check-in status (per open popup) | `GET /check-checkin-status?checkin_id=` | **4 s** (if enabled) | same | **No background poll** |
| Mark notification seen | `POST /mark-notification-seen` | On close/action | same | Unchanged |
| Attend / update check-in | `POST /attend_session`, `POST /update-checkin-status` | On button click | same | Unchanged |

**Backend:** `app/Http/Controllers/Admin/OfficeVisitController.php` — comment notes reception UI delivery via polling every 3–5 s when polling fallback is enabled.

**Real-time (intended):** `admin.blade.php` subscribes to `Echo.private('user.{id}').listen('.OfficeVisitNotificationCreated', …)` when `window.Echo` exists.

**Remaining gap:** `resources/js/bootstrap.js` has Laravel Echo **commented out**; `config/broadcasting.php` defaults `BROADCAST_DRIVER` to `null`. With polling off and Echo off, office visit popups do not auto-appear — enable Echo/Reverb (Phase B) or turn polling back on for a site.

---

### 2.2 My Day — automatic file time (client & partner detail)

| What | Endpoint | Interval | File |
|------|----------|----------|------|
| Focus heartbeat | `POST /dashboard/my-day/sessions/heartbeat` | **60 s** (focused tab) | `public/js/my-day/file-time-session.js` |
| Tab blur / leave | `POST /dashboard/my-day/sessions/blur` | On blur / visibility | same |

**Bootstrap partial:** `resources/views/partials/my-day-session-script.blade.php`  
**Included on:**

- `resources/views/Admin/clients/detail.blade.php`
- `resources/views/Admin/partners/detail.blade.php`

**Routes:** `routes/web.php` — `dashboard.my-day.sessions.heartbeat`, `dashboard.my-day.sessions.blur`  
**Controller:** `app/Http/Controllers/Admin/StaffFileSessionController.php`

---

### 2.3 Elite email inbox (heavy when that screen is open)

**Status:** ✅ **Tuned** (2026-09-28) — polling kept; intervals and payload reduced for AU.

| What | Interval | File | Notes |
|------|----------|------|-------|
| Auto inbox fetch (silent) | **25 s** default (`CRM_ELITE_INBOX_AUTO_POLL_MS`) | `resources/views/elite/emails-inbox.blade.php` | Inbox panel only |
| Burst poll after send | **10 s** for **5 min** (`CRM_ELITE_INBOX_BURST_POLL_MS`) | same | Pauses when tab hidden |
| Manual fetch | On button | same (`doFetchInbox(false)`) | Full list + `sync=1` unchanged |
| Silent poll API | `since_ts` + `light=1` | `EliteEmailController::inbox` | New rows only, max 50, no mailbox list |

Config: `config/crm.php` (`elite_inbox_*_poll_ms`). Auto and burst polls pause when `document.hidden`.

---

### 2.4 Notification bell / messages — defined but not on a timer

| Endpoint | Status | File |
|----------|--------|------|
| `GET /fetch-notification` | Function exists; **not** on `setInterval` | `resources/js/legacy-init.js` |
| `GET /fetch-messages` | Function exists; **not** on `setInterval` | same |

The `setInterval` block at the bottom of `legacy-init.js` is empty (“Polling functions commented out”). **Not a current AU load source.**

---

### 2.5 Server-side scheduled jobs (not browser polling)

| Command | Schedule | File |
|---------|----------|------|
| `ses:sync-inbound` | Every minute | `app/Console/Kernel.php` |
| `my-day:close-stale-sessions` | Scheduled | same |
| `my-day:snapshot-summaries` | Scheduled | same |
| `access:expire-grants` | Hourly | same |

Adds server load for all users; not geography-specific.

---

### 2.6 Timers that are NOT server polling

| File | Purpose |
|------|---------|
| `public/js/inactivity-logout.js` | Local inactivity check every 5 s (localStorage) |
| `resources/views/Admin/officevisits/index.blade.php` | Waiting-time display clock |
| `resources/views/documents/sign.blade.php` | Wait for SignaturePad library |
| `public/js/pages/admin/client-edit.js` | Wait for RecipientSelect library |
| `resources/views/Admin/clients/edit.blade.php`, `detail.blade.php` | OTP resend countdown UI |
| `public/js/common/tomselect-init.js`, `google-maps.js` | Wait for vendor libraries |

---

## 3. Comparison with migrationmanager2

| Area | migrationmanager2 | bansalcrm2 |
|------|-------------------|------------|
| Office visit poll | **10 s** (`resources/js/app.js`) | **4 s** when `CRM_OFFICE_VISIT_POLLING_ENABLED=true`; **off by default** (`layouts/admin.blade.php` + `config/crm.php`) |
| Echo / Reverb | Active + polling fallback | Echo **not initialized**; broadcast driver **null** |
| Notification bell poll | **30 s** when Echo disconnected | **Disabled** (commented interval) |
| Broadcast unread poll | **60 s** (`public/js/broadcasts.js`) | Not present |
| My Day heartbeat | Client / company / lead detail | Client + partner detail |
| Email inbox | Different stack | Elite inbox **25 s** default (+ 10 s burst; light silent API) |
| Booking page reload poll | Some booking views reload on timer | Not same pattern in bansalcrm2 |

**Implication:** For the same origin server, bansalcrm2 likely feels **slower in Australia** than migrationmanager2 because it depends more on HTTP polling and polls office visits **2.5× more often**.

---

## 4. Why India is fine and Australia feels slow

### 4.1 Latency model

```
Staff browser  →  Cloudflare (optional)  →  Origin (PHP-FPM + DB)
```

| Region | Typical round-trip to India-hosted origin |
|--------|-------------------------------------------|
| India | ~30–80 ms |
| Australia | ~200–350+ ms |

Each poll is a full round-trip. Example on one admin page for 1 minute:

- Office visits: 15 requests/min × 250 ms ≈ **3.75 s** of wait (AU) vs 15 × 50 ms ≈ **0.75 s** (IN)
- Plus page loads, form saves, DataTables AJAX, email inbox, My Day heartbeat, etc.

### 4.2 What it is not

- Not ModSecurity **blocking** AU users (production error log showed warnings only, zero blocks)
- Not a separate “Australia bug” in Laravel routes
- Not active `fetch-notification` polling in bansalcrm2 today

### 4.3 Shared hosting notes (from migrationmanager production log, Sep 2025)

Relevant if bansalcrm2 shares similar Apache + ModSecurity + PHP-FPM hosting:

| Signal | Meaning |
|--------|---------|
| ~99.99% ModSecurity warnings | Log noise; Cloudflare headers / `cf_clearance` false positives |
| 6 PHP-FPM / FastCGI errors in 1 hour | Brief backend disconnects — 502 risk for all regions |
| Top noisy URLs | Polling endpoints (`fetch-office-visit-notifications`, heartbeat, notifications) |

---

## 5. Safe improvement plan (do not skip steps)

Apply in order so **existing features keep working**.

### Phase A — Measure (no behaviour change)

- [ ] Confirm origin server region (hosting panel / `curl` timing from IN vs AU)
- [ ] Browser DevTools → Network: count requests/min on admin home, client detail, Elite inbox
- [ ] Verify `BROADCAST_DRIVER`, Reverb/Pusher env vars on production
- [ ] Confirm whether `window.Echo` is defined on a live admin page (console)

### Phase B — Enable real-time before reducing polls

| Action | Why safe |
|--------|----------|
| Enable Laravel Echo + Reverb (or Pusher) on admin layout | Office visit instant delivery |
| Keep `fetch-office-visit-notifications` as fallback | Reception still gets alerts if WebSocket drops |
| Dedupe in `showTeamsNotification` (already skips duplicate `id`) | Avoid double popups when Echo + poll both fire |

**Do not** remove or slow office-visit polling until Echo is stable for AU staff for several days.

### Phase C — Tune fallback intervals (after Phase B)

| Current | Suggested fallback only | Feature preserved |
|---------|-------------------------|-------------------|
| Office visits **4 s** | **10–15 s** | Alerts via Echo; poll as safety net |
| Check-in popup **4 s** | **5–10 s** | Popup still auto-closes |
| Elite inbox **25 s** (tuned) | Keep or **30 s** if still heavy | Manual “Get Emails” unchanged |
| My Day heartbeat **60 s** | Keep initially | File time accuracy |

### Phase D — Infrastructure (low code risk)

- [ ] Server or read replica closer to AU users (largest win)
- [ ] PHP-FPM: `pm.max_children`, memory, slow log around peak hours
- [ ] ModSecurity: whitelist Cloudflare headers/cookies; log blocks only
- [ ] Static assets via CDN (helps CSS/JS; does not fix AJAX latency alone)

### Phase E — What not to change without regression testing

| Area | Risk |
|------|------|
| `attend_session` / `update-checkin-status` | Reception “Pls Send” / “Client Sent” flow |
| `mark-notification-seen` | Stuck or duplicate popups |
| Elite burst poll after send | Delayed appearance of sent mail in inbox |
| Re-enabling `legacy-init.js` notification interval | Extra global load |
| My Day `file-time-session.js` idle / heartbeat logic | Incorrect billable/focus minutes |

---

## 6. AU regression test checklist (before/after any change)

Run from an Australian network (or VPN) and from India.

| # | Scenario | Pass criteria |
|---|----------|---------------|
| 1 | Reception: new office visit | Popup within acceptable delay; sound if enabled |
| 2 | Assignee: “Pls Send” / “Client Sent” | Status updates; popup closes |
| 3 | Client detail: stay on tab 3+ min | My Day heartbeat continues; blur on tab switch |
| 4 | Elite inbox: leave open 2 min | New mail banner; no duplicate rows |
| 5 | Elite: send email | Burst poll shows sent item without manual refresh |
| 6 | Echo disconnected (simulate) | Office visit poll fallback still delivers within tuned interval |
| 7 | Page load client detail | No new JS errors; tabs load |

---

## 7. File reference index

| Topic | Path |
|-------|------|
| Office visit polling + Echo hook | `resources/views/layouts/admin.blade.php` |
| Office visit polling feature flag | `config/crm.php` (`CRM_OFFICE_VISIT_POLLING_ENABLED`) |
| Office visit API | `app/Http/Controllers/Admin/OfficeVisitController.php` |
| Routes (fetch, check-in) | `routes/web.php` |
| Echo bootstrap (disabled) | `resources/js/bootstrap.js` |
| Broadcast config | `config/broadcasting.php` |
| My Day session JS | `public/js/my-day/file-time-session.js` |
| My Day partial | `resources/views/partials/my-day-session-script.blade.php` |
| My Day routes | `routes/web.php` (`dashboard.my-day.*`) |
| Elite inbox polling | `resources/views/elite/emails-inbox.blade.php` |
| Elite inbox poll intervals | `config/crm.php` (`CRM_ELITE_INBOX_*_POLL_MS`) |
| Legacy notification helpers | `resources/js/legacy-init.js` |
| Inactivity logout (local) | `public/js/inactivity-logout.js` |
| My Day porting context | `docs/MY_DAY_AUTO_FILE_TIME_PORTING.md` |

---

## 8. Priority summary

| Priority | Item | Effort | AU impact |
|----------|------|--------|-----------|
| 1 | Enable Echo/Reverb for office visits | Medium | High — restores instant alerts without re-enabling 4 s poll |
| 2 | Origin closer to AU / edge for static | Infra | Highest — helps every request |
| 3 | Slow poll fallbacks after real-time works | Low | Medium |
| 4 | PHP-FPM + ModSecurity tuning | Infra | Medium (stability + less noise) |
| 5 | Elite inbox interval tuning | Low | Medium (only when inbox in use) |

---

*This document is for planning and handoff. Implement changes in small PRs with the checklist in §6.*
