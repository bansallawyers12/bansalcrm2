# Documents Table Columns Review

**Total rows:** 198,891  
**Generated:** 2026-02-11

---

## Summary

| Category | Count | Description |
|----------|-------|-------------|
| **Critical** | 17 | Required for core document storage and display |
| **Recommended to keep** | 6 | Used in business logic, filters, or integrations |
| **Zero data / Evaluate** | 16 | No data today; may be planned features or removable |

---

## 1. Critical (keep)

Essential for document storage, retrieval, and core CRM functionality.

| Column | Non-null rows | Notes |
|--------|---------------|-------|
| `id` | 198,891 | Primary key |
| `file_name` | 197,883 | Display name; 1,008 null |
| `filetype` | 197,817 | File extension (pdf, jpg, etc.) |
| `myfile` | 197,853 | Stored filename/path |
| `user_id` | 198,891 | Uploader/owner |
| `client_id` | 198,891 | Client association |
| `file_size` | 197,883 | File size in bytes |
| `type` | 198,891 | Entity type (e.g. client) |
| `doc_type` | 194,890 | Document category (education, migration, documents) |
| `created_at` | 198,891 | Created timestamp |
| `updated_at` | 198,891 | Updated timestamp |
| `origin` | 198,891 | Source/context |
| `document_type` | 198,891 | Document type classification |
| `priority` | 198,891 | Priority (e.g. normal, high) |
| `signer_count` | 198,891 | Number of signers (often 0) |
| `status` | 198,891 | Status (draft, sent, signed, etc.) |

---

## 2. Recommended to keep

Used in business logic, filters, S3, or verification workflows.

| Column | Non-null rows | Notes |
|--------|---------------|-------|
| `myfile_key` | 69,078 | AWS S3 key for cloud-stored files |
| `mail_type` | 5,477 | Used in email preview/links (MailReport, Document) |
| `checklist` | 57,816 | Checklist label (e.g. Education, Migration) |
| `checklist_verified_by` | 8,501 | Admin who verified checklist |
| `checklist_verified_at` | 8,501 | Verification timestamp |
| `category_id` | 58,156 | Links to `document_categories` |
| `not_used_doc` | 25,026 | "Not used" flag for filtering |

---

## 3. Zero data – evaluate

These columns have **0 non-null rows**. Decide based on planned features.

### 3a. Signature workflow (planned / new feature)

Model and scopes reference these. Keep if e-signature workflow is planned.

| Column | Notes |
|--------|-------|
| `created_by` | Creator for signature workflow; used in `creator()` and `scopeForSignatureWorkflow()` |
| `title` | Document title |
| `labels` | JSON array |
| `due_at` | Due date for signing |
| `primary_signer_email` | Primary signer email |
| `last_activity_at` | Last activity timestamp |
| `archived_at` | Archive timestamp |
| `signature_doc_link` | Link to document for signing |
| `signed_doc_link` | Link to signed document |
| `signed_hash` | SHA-256 hash for integrity |
| `hash_generated_at` | Hash generation timestamp |

### 3b. Polymorphic relation (documentable)

| Column | Notes |
|--------|-------|
| `documentable_type` | Morph type (e.g. `App\Models\Admin`) |
| `documentable_id` | Morph ID |

Used in `scopeAssociated()` / `scopeAdhoc()` and visibility logic. Currently unused in data; keep if you plan polymorphic document associations.

### 3c. Application linking

Added in migration `2026_02_10_100001`. Migration command `MigrateApplicationDocumentsToDocumentsTable` populates these.

| Column | Notes |
|--------|-------|
| `application_id` | Links to `applications` |
| `application_list_id` | Links to application list |
| `application_stage` | Stage in application workflow |

Keep if application-document linking is active or planned.

---

## 4. Recommendation summary

| Action | Columns |
|--------|---------|
| **Keep** | All Critical and Recommended columns |
| **Keep if signature workflow planned** | All 3a columns |
| **Keep if polymorphic docs planned** | `documentable_type`, `documentable_id` |
| **Keep if application linking planned** | All 3c columns |
| **Can remove** | Only if corresponding feature is definitely abandoned and no code references the column |

---

## 5. Columns with partial data

Some columns have nulls in a minority of rows:

| Column | Non-null | Null count | Notes |
|--------|----------|------------|-------|
| `file_name` | 197,883 | ~1,008 | Consider backfilling |
| `filetype` | 197,817 | ~1,074 | Consider backfilling |
| `myfile` | 197,853 | ~1,038 | Consider backfilling |
| `doc_type` | 194,890 | ~4,001 | May be intentional |
| `myfile_key` | 69,078 | ~129,813 | Expected; local vs S3 storage |
