# Client Receipts & Application Link – Plan of Action

**Status:** Implemented (2026-02-13)  
**Last updated:** 2026-02-13

---

## 1. Add `application_id` (optional) to receipts

- Add nullable `application_id` column to `account_client_receipts`.
- Receipts with `application_id` = allocated to that application/course.
- Receipts without `application_id` = unallocated (client-level).

**Ongoing sheet logic:**
- Per-application row: show sum of receipts where `application_id` = that application.
- Unallocated receipts: for clients with **one** application only, treat as belonging to that application (backward compatibility).
- Unallocated receipts: for clients with **multiple** applications, show as a separate consideration (e.g. “Unallocated” indicator or exclude from per-course totals until assigned).

---

## 2. Reassign payment to another application

**Approach:** Option A – Edit Receipt modal with Application dropdown.

- Add optional “Application / course” dropdown to the Create/Edit Client Receipt modal.
- When editing a receipt, user can change which application the payment is linked to.
- **Transfer/reassignment reason (required):** When `application_id` is changed, user must provide a reason (e.g. “Transfer to migration – client opted for skilled visa”, “Reassigned to new course – client changed enrolment”). Stored in `reassignment_reason` on the receipt.
- **Activity logging:** Record reassignments in `activities_logs` (or equivalent): who, when, old `application_id`, new `application_id`, and the reason.

---

## 3. Refunds

**Approach:** B – New “refund” receipt linked to original.

- Add `receipt_type` (or extend existing) to support “Refund” (e.g. `receipt_type = 2`).
- Add nullable `parent_receipt_id` (FK to `account_client_receipts.id`) on the refund receipt.
- Refund receipt:
  - Negative `deposit_amount` and/or explicit `receipt_type = 2` (Refund).
  - Same `application_id` as original (or different if reassigning).
  - **Refund reason (required):** User must provide a reason when creating a refund receipt (e.g. “Client withdrew”, “Duplicate payment”). Stored in `refund_reason` on the receipt.
- Ongoing sheet totals: include refund receipts in sums (negative amounts reduce totals).
- Ensure voided receipts (`void_invoice = 1`) are excluded from totals.

---

## 4. Migration

**Approach:** Treat migration as same structure as education – different product/service name only.

- Migration applications live in the same `applications` table.
- Reassigning a payment from education to migration = updating `application_id` to the migration application.
- **Transfer to migration reason (required):** When moving a payment to a migration application (or any reassignment), user must provide a reason via the `reassignment_reason` field (e.g. “Transfer to migration – client pursuing skilled visa pathway”).
- No polymorphic link or separate `migration_case_id` required.

---

## 5. Implementation phases (summary)

| Phase | Change | Notes |
|-------|--------|------|
| **1** | Migration: Add `application_id`, `parent_receipt_id`, `refund_reason`, `reassignment_reason`; extend `receipt_type` for Refund; extend `activities_logs.activity_type` for receipt events | Database schema |
| **2** | Create/Edit Receipt form: optional Application dropdown; **reassignment reason** (required when changing application) | UX |
| **3** | Ongoing sheet query: per-application payment sum; single-application fallback for unallocated | Backend |
| **4** | Reassign: Comprehensive activity log (see §7) for all receipt actions | Backend + audit |
| **5** | Refund: New receipt flow with `receipt_type=Refund`, `parent_receipt_id`, negative amount; **refund reason** (required) | Form + backend |
| **6** | Totals: Exclude voided receipts; include refunds in sums | Query updates |

---

## 6. Schema changes (planned)

```
account_client_receipts:
  - application_id (nullable, FK applications.id)
  - parent_receipt_id (nullable, FK account_client_receipts.id) – for refunds
  - receipt_type: extend if needed (1 = Client Receipt, 2 = Refund, etc.)
  - refund_reason (text, required when receipt_type = Refund) – reason for refund
  - reassignment_reason (text, required when application_id is changed) – reason for transfer/reassignment (e.g. transfer to migration)
```

---

## 7. Activity log – comprehensive format

All receipt-related actions must create an activity log entry in `activities_logs`. Use `activity_type` for categorisation: `receipt_created`, `receipt_edited`, `receipt_reassigned`, `receipt_refunded`, `receipt_voided`, `receipt_validated`. Extend `activities_logs.activity_type` if needed. Each entry must include full context for audit and traceability.

### 7a. Receipt created

| Field | Value |
|-------|-------|
| `client_id` | Client ID |
| `created_by` | Admin ID who created |
| `subject` | "Added client receipt [Rec123] – $X.XX" |
| `description` | **Comprehensive:** Receipt ID, trans_no, trans_date, entry_date, payment_method, description text, deposit_amount, application_id (if set) + application name, document attached (yes/no). E.g. JSON or structured text. |

### 7b. Receipt edited (any field change)

| Field | Value |
|-------|-------|
| `client_id` | Client ID |
| `created_by` | Admin ID who edited |
| `subject` | "Updated client receipt [Rec123]" |
| `description` | **Comprehensive:** Receipt ID, trans_no. For each changed field: field name, **before value**, **after value**. List all changes (trans_date, entry_date, payment_method, description, deposit_amount, application_id, etc.). |

### 7c. Receipt reassigned (application_id changed)

| Field | Value |
|-------|-------|
| `client_id` | Client ID |
| `created_by` | Admin ID who reassigned |
| `subject` | "Reassigned receipt [Rec123] from [Course A] to [Course B]" |
| `description` | **Comprehensive:** Receipt ID, trans_no, deposit_amount. Old application_id + application/course name. New application_id + application/course name. **Reassignment reason** (from user). Timestamp. |

### 7d. Refund created

| Field | Value |
|-------|-------|
| `client_id` | Client ID |
| `created_by` | Admin ID who created refund |
| `subject` | "Refund created [Rec456] for original receipt [Rec123] – $X.XX" |
| `description` | **Comprehensive:** Refund receipt ID, trans_no, parent_receipt_id (original Receipt ID), deposit_amount (negative), application_id. **Refund reason** (from user). Original receipt trans_no and amount. Timestamp. |

### 7e. Receipt voided

| Field | Value |
|-------|-------|
| `client_id` | Client ID |
| `created_by` | Admin ID who voided |
| `subject` | "Voided client receipt [Rec123]" |
| `description` | **Comprehensive:** Receipt ID, trans_no, deposit_amount, application_id + name, payment_method, trans_date. Void reason if captured. Timestamp. |

### 7f. Receipt validated

| Field | Value |
|-------|-------|
| `client_id` | Client ID |
| `created_by` | Admin ID who validated |
| `subject` | "Validated client receipt [Rec123]" |
| `description` | **Comprehensive:** Receipt ID, trans_no, deposit_amount, application_id + name. Timestamp. |

### 7g. Suggested description structure (machine-parseable)

For consistency and future reporting, use a structured format in `description`:

```
action: receipt_reassigned
receipt_id: 123
trans_no: Rec123
deposit_amount: 1350.00
old_application_id: 5
old_application_name: Bachelor of IT – Salford College
new_application_id: 10
new_application_name: Skilled Independent Visa – Migration
reassignment_reason: Transfer to migration – client pursuing skilled visa pathway
performed_by: John Smith (admin_id: 42)
performed_at: 2026-02-13 14:30:00
```

Or JSON for flexibility: `{"action":"receipt_reassigned","receipt_id":123,"trans_no":"Rec123",...}`

---

## Implementation summary (completed 2026-02-13)

- **Phase 1:** Migration `2026_02_13_140844` added `application_id`, `parent_receipt_id`, `refund_reason`, `reassignment_reason` to `account_client_receipts`.
- **Phase 2:** Create/Edit Receipt form has optional Application dropdown and reassignment reason (required when changing application).
- **Phase 3:** Ongoing sheet shows per-application payment; single-application fallback for unallocated; excludes voided; includes refunds.
- **Phase 4:** Activity log uses `receipt_created`, `receipt_edited`, `receipt_reassigned`, `receipt_validated`, `receipt_refunded` with detailed descriptions.
- **Phase 5:** Refund flow via "Create Refund" icon on receipts; modal with amount and reason; new receipt with `receipt_type=2`, negative amount, `parent_receipt_id`.
- **Phase 6:** All totals exclude voided receipts and include refunds.
