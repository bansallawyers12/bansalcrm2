# Database Schema Comparison: bansalcrm2 vs migrationmanager2

## Date: January 26, 2026

This document compares the database schemas for client import/export between **bansalcrm2** and **migrationmanager2**.

---

## Main Client Table

### ✅ **Both Systems Use: `admins`**
- **Primary Key**: `id` (integer, auto-increment)
- **Client Identifier**: `client_id` (varchar 255)
- **Filter**: `role = 7` (clients)
- **bansalcrm2 Records**: 53,318

**Key Differences:**
- **bansalcrm2**: Stores `passport_number` directly in `admins` table
- **migrationmanager2**: May store passport in separate `client_passport_informations` table

---

## Related Tables Comparison

### 1. **Client Phone Numbers**

#### ✅ **bansalcrm2: `client_phones`**
- **Primary Key**: `id` (integer)
- **Foreign Key**: `client_id` (integer)
- **Total Records**: 17,759

**Columns:**
- `id` (integer, NOT NULL)
- `user_id` (integer, nullable)
- `client_id` (integer, nullable)
- `contact_type` (varchar 50, nullable)
- `client_country_code` (varchar 80, nullable) ⚠️ **Note: Field name**
- `client_phone` (varchar 100, nullable) ⚠️ **Note: Field name**
- `created_at`, `updated_at` (timestamps)

#### ❓ **migrationmanager2: `client_contacts` (assumed)**
- Likely uses different field names:
  - `country_code` instead of `client_country_code`
  - `phone` instead of `client_phone`

**⚠️ IMPORTANT**: Field mapping required during import/export:
- `country_code` ↔ `client_country_code`
- `phone` ↔ `client_phone`

---

### 2. **Client Emails**

#### ❌ **bansalcrm2: `client_emails` - DOES NOT EXIST**
- **Status**: Table not found in database
- **Impact**: Import/export services reference `ClientEmail` model, but table doesn't exist
- **Workaround**: Emails stored in `admins.email` (primary email only)

#### ✅ **migrationmanager2: `client_emails` (assumed)**
- Likely has separate table for multiple emails per client
- Fields likely include:
  - `client_id`
  - `email`
  - `email_type`
  - `is_verified`
  - `verified_at`

**⚠️ CRITICAL ISSUE**: bansalcrm2 cannot import/export multiple emails because table doesn't exist!

---

### 3. **Client Passport Information**

#### ❌ **bansalcrm2: `client_passport_informations` - DOES NOT EXIST**
- **Status**: Table not found in database
- **Workaround**: Passport info stored in `admins` table:
  - `country_passport` (varchar 200)
  - `passport_number` (varchar 255)

#### ✅ **migrationmanager2: `client_passport_informations` (assumed)**
- Likely has separate table with:
  - `passport_number`
  - `passport_country`
  - `passport_issue_date`
  - `passport_expiry_date`

**⚠️ IMPORTANT**: bansalcrm2 stores passport in `admins` table, migrationmanager2 uses separate table.

---

### 4. **Client Travel Information**

#### ❌ **bansalcrm2: `client_travel_informations` - DOES NOT EXIST**
- **Status**: Table not found in database
- **Impact**: Cannot import/export travel history

#### ✅ **migrationmanager2: `client_travel_informations` (assumed)**
- Likely has table with:
  - `travel_country_visited`
  - `travel_arrival_date`
  - `travel_departure_date`
  - `travel_purpose`

**⚠️ CRITICAL ISSUE**: bansalcrm2 cannot import/export travel information!

---

### 5. **Client Visa Countries**

#### ❌ **bansalcrm2: `client_visa_countries` - DOES NOT EXIST**
- **Status**: Table not found in database
- **Impact**: Cannot import/export visa information

#### ✅ **migrationmanager2: `client_visa_countries` (assumed)**
- Likely has table with:
  - `visa_country`
  - `visa_type`
  - `visa_description`
  - `visa_expiry_date`
  - `visa_grant_date`

**⚠️ CRITICAL ISSUE**: bansalcrm2 cannot import/export visa information!

---

### 6. **Client Character Information**

#### ❌ **bansalcrm2: `client_characters` - DOES NOT EXIST**
- **Status**: Table not found in database
- **Impact**: Cannot import/export character information

#### ✅ **migrationmanager2: `client_characters` (assumed)**
- Likely has table with:
  - `type_of_character`
  - `character_detail`
  - `character_date`

**⚠️ CRITICAL ISSUE**: bansalcrm2 cannot import/export character information!

---

### 7. **Client Addresses**

#### ❌ **bansalcrm2: `client_addresses` - DOES NOT EXIST**
- **Status**: Table not found in database
- **Workaround**: Address stored directly in `admins` table:
  - `address` (text)
  - `city` (varchar 255)
  - `state` (varchar 100)
  - `country` (varchar 100)
  - `zip` (varchar 40)

#### ✅ **migrationmanager2: `client_addresses` (assumed)**
- Likely has separate table for multiple addresses per client
- Fields likely include:
  - `address_line_1`, `address_line_2`
  - `suburb`, `city`, `state`, `country`, `zip`
  - `regional_code`
  - `start_date`, `end_date`
  - `is_current`

**⚠️ IMPORTANT**: bansalcrm2 only supports single address (in `admins` table), migrationmanager2 supports multiple addresses.

---

### 8. **Test Scores**

#### ✅ **bansalcrm2: `test_scores`**
- **Primary Key**: `id` (bigint)
- **Foreign Key**: `client_id` (bigint)
- **Total Records**: 55

**Columns:**
- `id` (bigint, NOT NULL)
- `user_id` (bigint, nullable)
- `client_id` (bigint, nullable)
- `type` (varchar 191, nullable)
- **TOEFL**: `toefl_Listening`, `toefl_Reading`, `toefl_Writing`, `toefl_Speaking` (varchar 191), `toefl_Date` (date)
- **IELTS**: `ilets_Listening`, `ilets_Reading`, `ilets_Writing`, `ilets_Speaking` (varchar 191), `ilets_Date` (date)
- **PTE**: `pte_Listening`, `pte_Reading`, `pte_Writing`, `pte_Speaking` (varchar 191), `pte_Date` (date)
- **Other**: `score_1`, `score_2`, `score_3`, `sat_i`, `sat_ii`, `gre`, `gmat` (varchar 191)
- `created_at`, `updated_at` (timestamps)

#### ❓ **migrationmanager2: `test_scores` (assumed)**
- May or may not have this table
- Structure likely similar if exists

**✅ COMPATIBLE**: Both systems likely support test scores.

---

### 9. **Activities Logs**

#### ✅ **bansalcrm2: `activities_logs`**
- **Primary Key**: `id` (integer)
- **Foreign Key**: `client_id` (integer)
- **Total Records**: 1,355,899

**Columns:**
- `id` (integer, NOT NULL)
- `client_id` (integer, nullable)
- `created_by` (integer, nullable)
- `subject` (varchar 255, nullable)
- `description` (text, nullable)
- `use_for` (integer, nullable)
- `followup_date` (date, nullable)
- `task_group` (varchar 25, nullable)
- `task_status` (integer, NOT NULL)
- `pin` (integer, NOT NULL)
- `created_at`, `updated_at` (timestamps)

**⚠️ NOTE**: bansalcrm2 doesn't have `activity_type` column, but import/export services reference it.

#### ✅ **migrationmanager2: `activities_logs` (assumed)**
- Likely has similar structure
- May include `activity_type` field

**✅ MOSTLY COMPATIBLE**: Minor field differences may exist.

---

### 10. **Client Services**

#### ✅ **bansalcrm2: `client_service_takens`**
- **Primary Key**: `id` (integer)
- **Foreign Key**: `client_id` (integer)
- **Total Records**: 10,693

**Columns:**
- `id` (integer, NOT NULL)
- `client_id` (integer, nullable)
- `service_type` (varchar 50, nullable)
- `mig_ref_no` (varchar 500, nullable)
- `mig_service` (varchar 555, nullable)
- `mig_notes` (text, nullable)
- `edu_course` (varchar 555, nullable)
- `edu_college` (varchar 555, nullable)
- `edu_service_start_date` (varchar 50, nullable)
- `edu_notes` (text, nullable)
- `created_at`, `updated_at` (timestamps)

#### ❓ **migrationmanager2: `client_service_takens` (assumed)**
- May or may not have this table
- Structure likely similar if exists

---

## Summary of Missing Tables in bansalcrm2

### ❌ **Critical Missing Tables:**
1. `client_emails` - Cannot import/export multiple emails
2. `client_passport_informations` - Passport stored in `admins` table instead
3. `client_travel_informations` - Cannot import/export travel history
4. `client_visa_countries` - Cannot import/export visa information
5. `client_characters` - Cannot import/export character information
6. `client_addresses` - Addresses stored in `admins` table instead

### ✅ **Existing Tables:**
1. `admins` - Main client table ✅
2. `client_phones` - Phone numbers ✅ (different field names)
3. `test_scores` - Test scores ✅
4. `activities_logs` - Activities ✅ (minor field differences)
5. `client_service_takens` - Services ✅

---

## Field Name Differences

### ClientPhone Table:
| migrationmanager2 | bansalcrm2 |
|------------------|------------|
| `country_code` | `client_country_code` |
| `phone` | `client_phone` |

### Passport Storage:
| migrationmanager2 | bansalcrm2 |
|------------------|------------|
| Separate `client_passport_informations` table | Stored in `admins` table columns |
| `passport_number` in separate table | `passport_number` in `admins` table |
| `passport_country` in separate table | `country_passport` in `admins` table |

### Address Storage:
| migrationmanager2 | bansalcrm2 |
|------------------|------------|
| Separate `client_addresses` table (multiple addresses) | Stored in `admins` table (single address) |
| `address_line_1`, `address_line_2`, `suburb` | `address` (text field) |
| `is_current`, `start_date`, `end_date` | Not supported |

---

## Import/Export Compatibility Issues

### 🔴 **Critical Issues:**
1. **Multiple Emails**: bansalcrm2 cannot import/export multiple emails (table doesn't exist)
2. **Travel History**: bansalcrm2 cannot import/export travel information (table doesn't exist)
3. **Visa Information**: bansalcrm2 cannot import/export visa data (table doesn't exist)
4. **Character Information**: bansalcrm2 cannot import/export character data (table doesn't exist)
5. **Multiple Addresses**: bansalcrm2 only supports single address (in `admins` table)

### ⚠️ **Field Mapping Required:**
1. **ClientPhone**: `country_code` ↔ `client_country_code`, `phone` ↔ `client_phone`
2. **Passport**: Separate table ↔ `admins` table columns
3. **Addresses**: Multiple addresses ↔ Single address in `admins` table

### ✅ **Compatible:**
1. **Test Scores**: Both systems support (structure similar)
2. **Activities**: Mostly compatible (minor field differences)
3. **Basic Client Data**: Compatible (stored in `admins` table)

---

## Recommendations

### Option 1: Create Missing Tables (Recommended)
Create the missing tables in bansalcrm2 to match migrationmanager2:
1. `client_emails`
2. `client_passport_informations`
3. `client_travel_informations`
4. `client_visa_countries`
5. `client_characters`
6. `client_addresses`

### Option 2: Update Import/Export Services
Modify the services to:
1. Handle missing tables gracefully
2. Map fields correctly (e.g., `country_code` → `client_country_code`)
3. Store passport/address in `admins` table when separate tables don't exist
4. Skip import of data that cannot be stored (travel, visa, character)

### Option 3: Hybrid Approach
- Create critical tables (`client_emails`, `client_passport_informations`)
- Update services to handle optional tables (travel, visa, character)
- Map fields correctly for existing tables

---

## Next Steps

1. **Verify migrationmanager2 schema** - Query actual database to confirm table structures
2. **Create missing tables** - Add tables that are critical for import/export
3. **Update import/export services** - Handle field mapping and missing tables
4. **Test import/export** - Verify data transfers correctly between systems

---

## Notes

- This comparison is based on:
  - bansalcrm2 actual database schema (queried)
  - migrationmanager2 assumptions (from code references)
  - Import/export service code analysis
  
- **Action Required**: Verify migrationmanager2 actual schema to confirm assumptions.
