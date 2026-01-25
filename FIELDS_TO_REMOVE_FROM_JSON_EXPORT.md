# Fields to Remove from Future JSON Exports (migrationmanager2)

## Analysis Date: January 25, 2026

Based on comparing the JSON export file with the bansalcrm2 database schema and codebase, here are the fields that should be **removed from the client object** in future JSON exports from migrationmanager2.

---

## Fields to Remove from `client` Object

### 1. **`naati_test`** ❌
- **Status**: Does NOT exist in bansalcrm2 `admins` table
- **Error**: Causes "column 'naati_test' of relation 'admins' does not exist"
- **Action**: Remove from export
- **Note**: `naati_py` exists and should be kept

### 2. **`email_type`** ❌
- **Status**: Should NOT be in client object
- **Reason**: This field belongs in the `emails` array, not the client object
- **Current Issue**: Present in JSON client object (line 25) but should only be in emails array
- **Action**: Remove from client object (keep in emails array)

### 3. **`contact_type`** ❌
- **Status**: Should NOT be in client object
- **Reason**: This field belongs in the `contacts` array, not the client object
- **Current Issue**: Present in JSON client object (line 26) but should only be in contacts array
- **Action**: Remove from client object (keep in contacts array)

### 4. **`regional_points`** ❌
- **Status**: Does NOT exist in bansalcrm2 `admins` table
- **Reason**: Not found in any database migrations or code usage
- **Action**: Remove from export

---

## Fields That May Not Exist (Need Verification)

### 5. **`py_test`** ⚠️
- **Status**: Not clearly found in ClientController edit method
- **Action**: Verify if this column exists in database before including

### 6. **`py_date`** ⚠️
- **Status**: Not clearly found in ClientController edit method
- **Action**: Verify if this column exists in database before including

### 7. **`py_field`** ⚠️
- **Status**: Not clearly found in ClientController edit method
- **Action**: Verify if this column exists in database before including

---

## Summary

### **Definitely Remove** (4 fields):
1. `naati_test` - Column doesn't exist (causes database error)
2. `email_type` - Wrong location (should be in emails array only)
3. `contact_type` - Wrong location (should be in contacts array only)
4. `regional_points` - Column doesn't exist

### **Verify Before Including** (3 fields):
1. `py_test`
2. `py_date`
3. `py_field`

---

## Recommended Action for migrationmanager2

Update the export service in migrationmanager2 to:

1. **Remove these fields from client object:**
   ```php
   // REMOVE these from getClientBasicData():
   - 'naati_test'
   - 'email_type'  // Keep only in emails array
   - 'contact_type'  // Keep only in contacts array
   - 'regional_points'
   ```

2. **Verify these fields exist before including:**
   ```php
   // Check if these columns exist in database:
   - 'py_test'
   - 'py_date'
   - 'py_field'
   ```

3. **Ensure proper structure:**
   - `email_type` should ONLY be in the `emails` array
   - `contact_type` should ONLY be in the `contacts` array

---

## Current JSON Structure Issues

### ❌ Wrong (Current):
```json
{
  "client": {
    "email_type": "Personal",  // ❌ Should NOT be here
    "contact_type": "Personal",  // ❌ Should NOT be here
    "naati_test": null,  // ❌ Column doesn't exist
    "regional_points": null  // ❌ Column doesn't exist
  },
  "emails": [{
    "email_type": "Personal"  // ✅ Correct location
  }],
  "contacts": [{
    "contact_type": "Personal"  // ✅ Correct location
  }]
}
```

### ✅ Correct (Should Be):
```json
{
  "client": {
    // email_type and contact_type removed from here
    // naati_test removed
    // regional_points removed
  },
  "emails": [{
    "email_type": "Personal"  // ✅ Only here
  }],
  "contacts": [{
    "contact_type": "Personal"  // ✅ Only here
  }]
}
```

---

## Next Steps

1. **Update migrationmanager2 export service** to remove the 4 fields listed above
2. **Verify** if `py_test`, `py_date`, `py_field` columns exist in bansalcrm2 database
3. **Test** the export/import after changes
4. **Update bansalcrm2 import service** to ignore these fields if they appear (defensive coding)
