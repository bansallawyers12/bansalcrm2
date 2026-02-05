# Office Structure Implementation Plan (bansalcrm2)

**Context:** This CRM has **Clients** (admins with role 7) and **Applications** (under clients). There are **no matters** (unlike migrationmanager2). We assign **clients** and **staff** to **offices** (Branch). Applications inherit the client’s office; they do not have their own office field.

---

## 1. Your data model (simplified)

| Entity        | Table        | Office assignment | Notes                                      |
|---------------|--------------|-------------------|--------------------------------------------|
| **Client**    | `admins`     | `office_id`       | role = 7                                   |
| **Staff**     | `admins`     | `office_id`       | role != 7                                  |
| **Application** | `applications` | None           | Belongs to client; office = client’s office |
| **Check-in**  | `checkin_logs` | `office` (branch id) | Which office the visit is at            |
| **Branch**    | `branches`   | —                 | Internal offices (office_name, address, …)  |

- **No** `client_matters` table.
- **No** “assign office to matter” — only “assign client/staff to office”.
- Application’s “office” is always the **client’s** `office_id`.

---

## 2. Implementation steps (simplified)

### Phase 0: Pre-implementation

- **0.1** Run the verification queries from the previous plan (confirm `admins.office_id`, `branches.*`, `checkin_logs.office`, and the report JOIN work).
- **0.2** Backup DB and commit current code.

---

### Phase 1: Models

**1.1 Branch** (`app/Models/Branch.php`)

- Add `$fillable`: `office_name`, `address`, `city`, `state`, `zip`, `country`, `email`, `phone`, `mobile`, `contact_person`, `choose_admin`.
- Add relationships:
  - `staff()`: `hasMany(Admin::class, 'office_id')->where('role', '!=', 7)`.
  - `clients()`: `hasMany(Admin::class, 'office_id')->where('role', 7)` (clients assigned to this office).
  - Optionally `activeStaff()` same as staff but `->where('status', 1)`.

Do **not** add `matters()` or `documents()` — no matters in this system.

**1.2 Admin** (`app/Models/Admin.php`)

- Add `office_id` (and any other controller-set fields) to `$fillable`: e.g. `office_id`, `position`, `team`, `telephone`, `permission`, `show_dashboard_per`, `verified`, `client_id`, `staff_id`, `phone`, `country_code`.
- Add `office()`: `belongsTo(Branch::class, 'office_id')`.

**1.3 Document** (`app/Models/Document.php`)

- **Option A (recommended):** Remove `office_id` from `$fillable` and add a short comment that the column exists but is unused (simpler model).
- **Option B:** Keep `office_id` in fillable and add `office()` relationship for future use. No `resolved_office` or matter logic.

**1.4 CheckinLog** (`app/Models/CheckinLog.php`)

- Add `office()`: `belongsTo(Branch::class, 'office', 'id')` (column is `office`, not `office_id`).
- Do **not** rename `office` to `office_id` — the report `noofpersonofficevisit` uses `checkin_logs.office` in a JOIN.

---

### Phase 2: Controllers

**2.1 UserController**

- **Validation:** In `store` and `edit`, add `'office' => 'nullable|exists:branches,id'` (or `required` if every user must have an office).
- **N+1:** In user list queries (active/inactive), add `->with(['usertype', 'office'])`.

**2.2 StaffController (optional)**

- If staff should have an office: add office dropdown to create/edit, set `$obj->office_id`, validate `'office' => 'nullable|exists:branches,id'`. Otherwise skip.

**2.3 Client creation / lead conversion**

- **UserController:** When creating a user with role 7 (client), office is already in the form and saved as `office_id` — no change needed once validation and fillable are in place.
- **ClientController (lead → client):** Already sets `$obj->office_id = $lead->staffuser->office_id ?? null`. Optional: allow overriding office when converting (e.g. office dropdown on convert form). Not required for this plan.

---

### Phase 3: Views

- **User index:** Use `$list->office->office_name` (with controller eager load `with('office')`).
- **User view:** Use `$admin->office->office_name` and load user with `with('office')`.
- **ActionController:** Use `$admin->office->office_name` and load admins with `with('office')` where used.
- **OfficeVisitController (check-in lists):** Use `$CheckinLog->office->office_name` and `CheckinLog::with('office')` where listing.

---

### Phase 4: Optional

- **Foreign keys:** Migration adding FK from `admins.office_id` and `checkin_logs.office` to `branches.id` (optional but recommended).
- **Document:** If Option A, remove `office_id` from Document `$fillable` and add comment.

---

## 3. What we are not doing

- No `client_matters` table or matter-related code.
- No “assign office to matter” or matter-level office.
- No `Application.office_id` — applications do not have their own office; office comes from the client.
- No Document `resolved_office` or matter-based logic.
- No renaming `checkin_logs.office` to `office_id` (would break existing report).
- Branch stays as `Model` (not Authenticatable).

---

## 4. Summary: “assign clients to offices”

- **Clients** are assigned to an office via `admins.office_id` (role = 7).
- **Staff** are assigned to an office via `admins.office_id` (role != 7).
- **Branch** model gets `clients()` and `staff()` so you can do e.g. `$branch->clients` and `$branch->staff`.
- **Ongoing Sheet** already filters by `admins.office_id` — that is “clients by office”.
- **Applications** have no office field; reporting by office for applications is done via the client (e.g. join applications to admins and filter by `admins.office_id`).

This plan keeps the system simpler and aligned with “clients and staff assigned to offices, applications under clients, no matters.”
