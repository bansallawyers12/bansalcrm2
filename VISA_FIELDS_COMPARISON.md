# Visa Information Fields Comparison: bansalcrm2 vs migrationmanager2

## Date: January 26, 2026

This document compares visa information fields between **bansalcrm2** and **migrationmanager2** for import/export compatibility.

---

## bansalcrm2 Database Schema

### **admins Table - Visa Columns:**

| Column Name | Data Type | Nullable | Description |
|-------------|-----------|----------|-------------|
| `visa_type` | varchar | YES | Visa Type (e.g., "Student", "Work", "PR") |
| `visa_opt` | varchar | YES | Visa Details/Description (additional visa information) |
| `visaexpiry` | date | YES | Visa Expiry Date |
| `prev_visa` | text | YES | Previous Visa Information |
| `is_visa_expire_mail_sent` | integer | YES | System flag for expiry email notification |

**Note:** bansalcrm2 does NOT have a separate `client_visa_countries` table. All visa data is stored in the `admins` table.

---

## bansalcrm2 Export Service

### **In Client Object (getClientBasicData):**
```php
'visa_type' => $client->visa_type ?? null,
'visa_opt' => $client->visa_opt ?? null,
'visaExpiry' => $client->visaExpiry ?? null, // Uses accessor, column is visaexpiry
```

### **In visa_countries Array (getClientVisaCountries):**
```php
[
    'visa_country' => $client->country ?? null, // Uses client's country as visa country
    'visa_type' => $client->visa_type ?? null,
    'visa_description' => $client->visa_opt ?? null, // visa_opt mapped to visa_description
    'visa_expiry_date' => $client->visaExpiry ?? null, // Uses accessor for visaexpiry column
    'visa_grant_date' => null, // Not stored in bansalcrm2
]
```

**Export Structure:**
- ✅ Exports `visa_type` (in both client object and visa_countries array)
- ✅ Exports `visa_opt` as `visa_description` in visa_countries array
- ✅ Exports `visaexpiry` as `visaExpiry` in client object and `visa_expiry_date` in visa_countries array
- ❌ Does NOT export `visa_grant_date` (always null - not stored)
- ❌ Does NOT export `visa_country` separately (uses client's country)
- ❌ Does NOT export `prev_visa` (previous visa information)
- ❌ Does NOT export `is_visa_expire_mail_sent` (system flag)

---

## bansalcrm2 Import Service

### **From Client Object:**
```php
$client->visa_type = $clientData['visa_type'] ?? null;
$client->visa_opt = $clientData['visa_opt'] ?? null;
$client->visaexpiry = $this->parseDate($clientData['visaExpiry'] ?? null);
```

### **From visa_countries Array:**
```php
// Uses first visa entry only
$visaData = $importData['visa_countries'][0];
$client->visa_type = $visaData['visa_type'] ?? null; // Only if empty
$client->visa_opt = $visaData['visa_description'] ?? null; // Maps visa_description to visa_opt
$client->visaexpiry = $this->parseDate($visaData['visa_expiry_date'] ?? null);
// Note: visa_grant_date is ignored (not stored)
```

**Import Behavior:**
- ✅ Imports `visa_type` from client object OR visa_countries array
- ✅ Imports `visa_opt` from client object OR maps `visa_description` from visa_countries array
- ✅ Imports `visaexpiry` from client object (`visaExpiry`) OR visa_countries array (`visa_expiry_date`)
- ❌ Ignores `visa_grant_date` (not stored in bansalcrm2)
- ❌ Ignores `visa_country` from visa_countries array (uses client's country instead)
- ⚠️ Only imports FIRST visa entry from visa_countries array (bansalcrm2 supports single visa)

---

## migrationmanager2 (Assumed Structure)

### **Likely Has:**
- Separate `client_visa_countries` table
- Fields: `visa_country`, `visa_type`, `visa_description`, `visa_expiry_date`, `visa_grant_date`
- Possibly: `visa_subclass` or `visa_subclass_code` (user mentioned "visa subclass")

---

## Field Mapping Summary

### **bansalcrm2 → migrationmanager2:**

| bansalcrm2 (admins table) | Export JSON | migrationmanager2 (assumed) |
|---------------------------|-------------|----------------------------|
| `visa_type` | `visa_type` | `visa_type` |
| `visa_opt` | `visa_description` (in visa_countries) | `visa_description` |
| `visaexpiry` | `visaExpiry` (client) / `visa_expiry_date` (visa_countries) | `visa_expiry_date` |
| `country` | `visa_country` (in visa_countries) | `visa_country` |
| N/A | `visa_grant_date` (always null) | `visa_grant_date` |
| ❓ | ❓ `visa_subclass` (not found) | ❓ `visa_subclass` (may exist) |

### **migrationmanager2 → bansalcrm2:**

| migrationmanager2 (assumed) | Import JSON | bansalcrm2 (admins table) |
|----------------------------|-------------|---------------------------|
| `visa_type` | `visa_type` | `visa_type` |
| `visa_description` | `visa_description` | `visa_opt` |
| `visa_expiry_date` | `visa_expiry_date` | `visaexpiry` |
| `visa_country` | `visa_country` | Ignored (uses client's country) |
| `visa_grant_date` | `visa_grant_date` | Ignored (not stored) |
| ❓ `visa_subclass` | ❓ `visa_subclass` | ❓ Not found (may need to add) |

---

## Potential Issues

### 1. **Missing Field: `visa_subclass`**
- **Status**: User mentioned "visa subclass" but field not found in bansalcrm2 database
- **Impact**: If migrationmanager2 exports `visa_subclass`, it will be lost during import
- **Recommendation**: Check if migrationmanager2 exports this field, and if needed, add `visa_subclass` column to bansalcrm2 `admins` table

### 2. **Single vs Multiple Visas**
- **bansalcrm2**: Supports only ONE visa per client (stored in admins table)
- **migrationmanager2**: May support MULTIPLE visas (separate table)
- **Impact**: Only first visa entry is imported/exported
- **Current Behavior**: ✅ Works but loses additional visa entries

### 3. **Visa Country Mapping**
- **bansalcrm2**: Uses client's `country` field as visa country
- **migrationmanager2**: May have separate `visa_country` field
- **Impact**: Visa country from migrationmanager2 is ignored, uses client's country instead
- **Current Behavior**: ⚠️ May cause data loss if visa country differs from client country

### 4. **Visa Grant Date**
- **bansalcrm2**: Does NOT store `visa_grant_date`
- **migrationmanager2**: May export `visa_grant_date`
- **Impact**: Grant date is lost during import
- **Current Behavior**: ✅ Handled gracefully (ignored)

### 5. **Previous Visa Information**
- **bansalcrm2**: Has `prev_visa` column but NOT exported/imported
- **migrationmanager2**: Unknown if it has this field
- **Impact**: Previous visa info not transferred
- **Current Behavior**: ⚠️ Data not transferred

---

## Fields Currently Handled

### ✅ **Exported/Imported:**
1. `visa_type` - Visa Type ✅
2. `visa_opt` / `visa_description` - Visa Details ✅
3. `visaexpiry` / `visa_expiry_date` - Visa Expiry Date ✅

### ❌ **Not Exported/Imported:**
1. `prev_visa` - Previous Visa Information ❌
2. `is_visa_expire_mail_sent` - System Flag ❌
3. `visa_grant_date` - Visa Grant Date ❌ (not stored in bansalcrm2)
4. `visa_subclass` - Visa Subclass ❌ (not found, may need to add)

---

## Recommendations

### **If migrationmanager2 exports `visa_subclass`:**

1. **Add column to bansalcrm2:**
   ```sql
   ALTER TABLE admins ADD COLUMN visa_subclass VARCHAR(255) NULL;
   ```

2. **Update Export Service:**
   ```php
   'visa_subclass' => $client->visa_subclass ?? null,
   ```

3. **Update Import Service:**
   ```php
   $client->visa_subclass = $clientData['visa_subclass'] ?? null;
   // And in visa_countries import:
   $client->visa_subclass = $visaData['visa_subclass'] ?? null;
   ```

### **If migrationmanager2 has separate visa_country:**

- Current implementation uses client's country as visa country
- Consider adding `visa_country` column to admins table if visa country differs from client country

---

## Summary

### **Current Status:**
- ✅ Basic visa fields (`visa_type`, `visa_opt`, `visaexpiry`) are exported/imported correctly
- ⚠️ `visa_subclass` field not found - may need to add if migrationmanager2 exports it
- ⚠️ `prev_visa` exists but not exported/imported
- ❌ `visa_grant_date` not supported (not stored in bansalcrm2)
- ⚠️ Only single visa supported (first entry from array)

### **Action Required:**
1. Verify if migrationmanager2 exports `visa_subclass`
2. If yes, add `visa_subclass` column to bansalcrm2 and update import/export services
3. Consider if `prev_visa` should be exported/imported
4. Consider if `visa_country` should be separate from client's country
