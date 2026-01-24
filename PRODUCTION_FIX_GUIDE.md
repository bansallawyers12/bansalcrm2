# PRODUCTION SEQUENCE FIX - DEPLOYMENT GUIDE

## Problem
Bulk document uploads on production are failing with:
```
SQLSTATE[23505]: Unique violation: 7 ERROR: duplicate key value violates unique constraint "documents_pkey"
```

This is because PostgreSQL sequences are out of sync with existing data.

## Affected Tables
1. documents
2. account_client_receipts
3. activities_logs
4. application_activities_logs
5. mail_reports
6. notes

---

## SOLUTION OPTIONS

### Option 1: Run Migration (RECOMMENDED for long-term) ✅

**Advantages:**
- ✅ Clean, tracked in version control
- ✅ Can be deployed to multiple environments
- ✅ Reversible with rollback
- ✅ Professional approach

**Steps:**

1. **On Local:**
   ```bash
   cd /path/to/bansalcrm2
   git add database/migrations/2026_01_24_155620_create_and_sync_sequences_for_five_tables.php
   git commit -m "Fix: Create and sync sequences for 5 tables to prevent duplicate key errors"
   git push origin master
   ```

2. **On Production Server:**
   ```bash
   cd /path/to/bansalcrm2
   git pull origin master
   php artisan migrate
   ```

3. **Verify:**
   ```bash
   php artisan migrate:status
   ```
   
   You should see the new migration as "Ran"

---

### Option 2: Run PHP Script (IMMEDIATE FIX) ⚡

**Advantages:**
- ✅ Immediate fix without code deployment
- ✅ Works even if git/deployment is blocked
- ✅ Detailed output for verification

**Steps:**

1. **Upload the script to production server:**
   - Upload `fix_production_sequences.php` to your production root directory

2. **SSH to production and run:**
   ```bash
   cd /path/to/bansalcrm2
   php fix_production_sequences.php
   ```

3. **Expected Output:**
   ```
   ========================================================================
   PRODUCTION SEQUENCE FIX SCRIPT
   ========================================================================
   Database: bansalcrm_pg
   Started: 2026-01-24 16:00:00
   ========================================================================

   Processing: documents
     Max ID: 207003
     Current sequence: 196866
     ✓ Sequence synced from 196866 to 207003
     Next ID will be: 207004
     ✓ SUCCESS

   Processing: account_client_receipts
     Max ID: 3016
     Creating sequence...
     ✓ Sequence created (starts at 3017)
     Next ID will be: 3018
     ✓ SUCCESS

   [... continues for all tables ...]

   ========================================================================
   SUMMARY
   ========================================================================
   Tables processed: 6
   Successfully fixed: 6
   Errors: 0
   ========================================================================

   ✓ ALL SEQUENCES FIXED!
   ✓ Bulk uploads should now work without duplicate key errors.
   ```

4. **Clean up (optional):**
   ```bash
   rm fix_production_sequences.php
   ```

---

### Option 3: Run SQL Script (DIRECT DATABASE ACCESS) 🔧

**Advantages:**
- ✅ No file upload needed
- ✅ Can be run by DBA directly on database
- ✅ Works even if application is down

**Steps:**

1. **Connect to PostgreSQL:**
   ```bash
   psql -U postgres -d bansalcrm_pg
   ```

2. **Copy and paste the SQL from `fix_production_sequences.sql`**

3. **Verify output shows all sequences synced**

---

## Which Option Should You Choose?

| Scenario | Recommended Option |
|----------|-------------------|
| Normal deployment process available | **Option 1** (Migration) |
| Need immediate fix, can access server | **Option 2** (PHP Script) |
| Only DBA has access, no code deploy | **Option 3** (SQL Script) |
| Want both immediate + long-term fix | **Option 2 NOW** + **Option 1 later** |

---

## After Fixing

1. **Test bulk upload** on production
2. **Verify no duplicate key errors**
3. **Monitor logs** for any issues
4. **Consider running on staging** if you have one

---

## Prevention for Future

✅ Always run migrations on production after deployment
✅ If importing data directly, always sync sequences after
✅ Use Laravel's `DB::table()->insert()` instead of raw SQL when possible
✅ Monitor for sequence drift regularly

---

## Need Help?

If errors occur, check:
- PostgreSQL version (should be 9.5+)
- User permissions (need CREATE SEQUENCE permission)
- Table locks (ensure no long-running transactions)
- Disk space (sequences need minimal space but check anyway)

---

## Files Included

- `fix_production_sequences.php` - PHP script for immediate fix
- `fix_production_sequences.sql` - SQL script for direct database access
- `database/migrations/2026_01_24_155620_create_and_sync_sequences_for_five_tables.php` - Laravel migration

---

**Last Updated:** 2026-01-24
**Created By:** AI Assistant
**Tested On:** Local development environment ✅
