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

---

## To match (excluding Source)

### 1. Emergency contact

| Item | migrationmanager2 | bansalcrm2 |
|------|-------------------|------------|
| **emergency_contact_no** | Saved on client (admins). | Not on client/lead forms; only in import/export guide. |
| **emergency_contact_type** | Saved from request (no validation). | Not on forms. |

**Action:** Either add emergency contact number + type (and type dropdown) to bansalcrm2 client/lead forms and save/export/import, or accept that only migrationmanager2 has this data.

---

### 2. Gender

| Item | migrationmanager2 | bansalcrm2 |
|------|-------------------|------------|
| **Allowed values** | `Male, Female, Other` | Verify dropdowns use same three values. |

**Action:** Confirm both use exactly: Male, Female, Other.

---

### 3. Partner / dependants – relationship and company type

migrationmanager2 validation (client create/update):

| Field | Allowed values |
|-------|----------------|
| **partner_relationship_type.*** | Husband, Wife, Ex-Husband, Ex-Wife, Defacto |
| **partner_company_type.*** | Accompany Member, Non-Accompany Member |
| **children_relationship_type.*** | Son, Daughter, Step Son, Step Daughter |
| **children_company_type.*** | Accompany Member, Non-Accompany Member |
| **parent_relationship_type.*** | Father, Mother, Step Father, Step Mother, Mother-in-law, Father-in-law |
| **parent_company_type.*** | Accompany Member, Non-Accompany Member |
| **siblings_relationship_type.*** | Brother, Sister, Step Brother, Step Sister |
| **siblings_company_type.*** | Accompany Member, Non-Accompany Member |
| **others_relationship_type.*** | Cousin, Friend, Uncle, Aunt, Grandchild, Granddaughter, Grandparent, Niece, Nephew, Grandfather |
| **others_company_type.*** | Accompany Member, Non-Accompany Member |

**Action:** In bansalcrm2, find any partner/children/parent/siblings/others forms or import/export; ensure dropdowns and validation use the same values (and same spelling, e.g. Defacto vs De Facto).

---

### 4. Spouse – English score and skill assessment

| Field | migrationmanager2 | bansalcrm2 |
|-------|-------------------|------------|
| **spouse_has_english_score** | `Yes, No` | Verify if present and same. |
| **spouse_has_skill_assessment** | `Yes, No` | Verify if present and same. |
| **spouse_test_type** | Same as main test type list (IELTS, IELTS_A, …). | Verify if present and same. |

**Action:** If bansalcrm2 has spouse English/skill-assessment fields, align allowed values and test type list.

---

### 5. Date formats

migrationmanager2 uses regex for dates: `\d{2}/\d{2}/\d{4}` (e.g. DD/MM/YYYY).

**Action:** Ensure bansalcrm2 import/export and any shared payloads use the same format where dates are exchanged.

---

### 6. Type (lead vs client)

| Field | migrationmanager2 | bansalcrm2 |
|-------|-------------------|------------|
| **type** | `required|in:lead,client` on create. | Verify lead vs client is represented the same way in export/import. |

**Action:** Confirm both use `lead` and `client` as the only two values where type is used in shared data.

---

## Excluded (for now)

| Field | Reason |
|-------|--------|
| **Source** | Excluded per request. migrationmanager2: `SubAgent, Others`. bansalcrm2: “Sub Agent” + Source::all(). |

---

## Where to look

- **migrationmanager2:** `app/Http/Controllers/CRM/ClientsController.php` (e.g. `store()` validation ~581–712), client detail/edit views, ClientExportService / ClientImportService.
- **bansalcrm2:** `app/Http/Controllers/Admin/Client/ClientController.php`, `app/Http/Controllers/Admin/LeadController.php`, `resources/views/Admin/clients/`, `resources/views/Admin/leads/`, ClientExportService, ClientImportService.

When you implement a field, move it from “To match” to “Already matched” in this file and note any differences you normalized.
