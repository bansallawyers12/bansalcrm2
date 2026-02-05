# Client Archive Fix - Applied

## Issue
When archiving clients/leads through the `archive_action` endpoint, the system was updating the wrong column (`is_archive` instead of `is_archived`) and not setting the required metadata fields (`archived_on`, `archived_by`). This caused:
- Clients to remain visible in the active list after "archiving"
- No record of when or who archived the client
- Inconsistent behavior compared to the working `delete_action` endpoint

## Root Cause
In `app/Http/Controllers/Admin/AdminController.php`, the `archiveAction()` method was:
1. Using **`is_archive`** (wrong column name for admins table)
2. Not setting **`archived_on`** or **`archived_by`** metadata
3. Treating all tables the same way (works for quotations, not for clients)

## Fix Applied
Updated `AdminController::archiveAction()` (lines 723-768) to:

### For `admins` table (clients/leads):
- Update **`is_archived`** = 1 (correct column name)
- Set **`archived_on`** = current date
- Set **`archived_by`** = current user ID
- Same behavior as the working `deleteAction()` method

### For other tables (quotations, etc.):
- Keep existing behavior (update `is_archive` column with status-based handling)

## Code Changes
**File:** `app/Http/Controllers/Admin/AdminController.php`  
**Method:** `archiveAction()`  
**Lines:** 723-768

```php
// Handle admins table (clients/leads) separately - use correct column names and metadata
if($requestData['table'] == 'admins'){
    // Archive clients/leads with proper metadata (same as deleteAction)
    $updateData = [
        'is_archived' => 1,
        'archived_on' => date('Y-m-d'),
        'archived_by' => Auth::user()->id
    ];
    $response = DB::table($requestData['table'])->where('id', $requestData['id'])->update($updateData);
    
    if($response)
    {
        $status = 1;
    }
    else
    {
        $message = Config::get('constants.server_error');
    }
}
else
{
    // For other tables (quotations, etc.) - use existing logic with 'is_archive' column
    $response = DB::table($requestData['table'])->where('id', $requestData['id'])->update(['is_archive' => $updated_status]);
    // ... rest of original logic for status-based $astatus ...
}
```

## Result
- ✅ Clients/leads now archive correctly through **both** endpoints (`/archive_action` and `/delete_action`)
- ✅ Proper metadata tracking (who archived, when archived)
- ✅ Consistent behavior across all archive entry points
- ✅ No migration needed (all columns already exist)
- ✅ Backward compatible (other tables like quotations still work as before)

## Testing
To verify the fix:
1. Archive a client from any screen that uses `archive_action` endpoint
2. Check that the client disappears from active clients list
3. Check that the client appears in the "Archived" tab
4. Verify "Archived By" and "Archived On" are populated correctly
5. Test unarchiving works as expected

## No Migration Required
All required database columns already exist:
- `admins.is_archived` - Used throughout the codebase
- `admins.archived_on` - Used in deleteAction and archived view
- `admins.archived_by` - Added by migration `2026_01_25_000001_add_archived_by_to_admins_table.php`

---
**Applied:** February 5, 2026  
**Author:** Development Team  
**Related Files:** 
- `app/Http/Controllers/Admin/AdminController.php`
