# Fields to Match Between bansalcrm2 and migrationmanager2

Use this checklist to align lead/client form fields and allowed values so both CRMs accept the same data for import/export and merge.

**Source is excluded** from this list for now.

---

## Already matched (reference)

| Field | Notes |
|-------|--------|
| **Marital status** | Same options in both: Never Married, Engaged, Married, De Facto, Separated, Divorced, Widowed. |
| **Contact type** (phone) | Aligned: Personal, Office, Work, Mobile, Business, Secondary, Father, Mother, Brother, Sister, Uncle, Aunt, Cousin, Others, Partner, Not In Use. |
| **Email type** | Aligned: Personal, Work, Business, Secondary, Additional, Sister, Brother, Father, Mother, Uncle, Auntie. |
| **English test score (test type)** | Aligned: IELTS, IELTS_A, PTE, TOEFL, CAE, OET, CELPIP, MET, LANGUAGECERT. bansalcrm2 uses `client_testscore` table. |
| **Start process** | Removed from bansalcrm2 (UI + column dropped). Not in migrationmanager2. |
| **Country of Passport** | Both store **country name** (e.g. India, Australia). bansalcrm2 migration + views + import normalization done. |
| **Gender** | Same options in both: Male, Female, Other. bansalcrm2 validation updated to `in:Male,Female,Other` on lead and client (edit). |
| **Emergency contact** | Removed from migrationmanager2. Both systems align: bansalcrm2 uses multiple phone numbers; migrationmanager2 no longer has `emergency_contact_no` / `emergency_contact_type`. |

---

## To match (excluding Source)

### 1. Date formats

migrationmanager2 uses regex for dates: `\d{2}/\d{2}/\d{4}` (e.g. DD/MM/YYYY).

**Action:** Ensure bansalcrm2 import/export and any shared payloads use the same format where dates are exchanged.

---

### 2. Type (lead vs client)

| Field | migrationmanager2 | bansalcrm2 |
|-------|-------------------|------------|
| **type** | `required|in:lead,client` on create. | Verify lead vs client is represented the same way in export/import. |

**Action:** Confirm both use `lead` and `client` as the only two values where type is used in shared data.

---

## Excluded (for now)

| Field | Reason |
|-------|--------|
| **Source** | Excluded per request. migrationmanager2: `SubAgent, Others`. bansalcrm2: "Sub Agent" + Source::all(). |
| **Partner / dependants** | Excluded. migrationmanager2 has structured partner/children/parent/siblings/others with relationship + company type. bansalcrm2 has only `married_partner` (text); no dependant list. |
| **Spouse â€“ English score and skill assessment** | Not relevant in bansalcrm2 at this stage. |

---

## Where to look

- **migrationmanager2:** `app/Http/Controllers/CRM/ClientsController.php` (e.g. `store()` validation ~581â€“712), client detail/edit views, ClientExportService / ClientImportService.
- **bansalcrm2:** `app/Http/Controllers/Admin/Client/ClientController.php`, `app/Http/Controllers/Admin/LeadController.php`, `resources/views/Admin/clients/`, `resources/views/Admin/leads/`, ClientExportService, ClientImportService.

When you implement a field, move it from â€œTo matchâ€ to â€œAlready matchedâ€ in this file and note any differences you normalized.
