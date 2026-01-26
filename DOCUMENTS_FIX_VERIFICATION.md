# Verification Queries for Documents Fix

## 1. Check if General Category Exists
```sql
SELECT id, name, is_default, status 
FROM document_categories 
WHERE name = 'General' AND is_default = 1;
```

## 2. Count Documents by Category Assignment Status
```sql
-- Should return 0 if all documents have categories
SELECT COUNT(*) as documents_without_category
FROM documents 
WHERE type = 'client' 
  AND doc_type = 'documents' 
  AND not_used_doc IS NULL 
  AND category_id IS NULL;
```

## 3. Check Specific User (MANJ231133395) Documents
```sql
-- Get user's ID first
SELECT id, unique_id, first_name, last_name 
FROM admins 
WHERE unique_id = 'MANJ231133395';

-- Then check their documents (replace XXX with actual client_id from above)
SELECT 
    d.id,
    d.file_name,
    d.doc_type,
    d.category_id,
    dc.name as category_name,
    d.type,
    d.not_used_doc,
    d.created_at,
    d.updated_at
FROM documents d
LEFT JOIN document_categories dc ON d.category_id = dc.id
WHERE d.client_id = XXX  -- Replace with actual client ID
  AND d.type = 'client'
  AND d.not_used_doc IS NULL
  AND (d.doc_type = 'documents' OR d.doc_type IS NULL OR d.doc_type = '')
ORDER BY d.updated_at DESC;
```

## 4. Verify Document Type Distribution
```sql
-- Should only show 'documents', NULL, or '' for Documents tab
SELECT 
    doc_type,
    COUNT(*) as count,
    COUNT(CASE WHEN category_id IS NULL THEN 1 END) as without_category,
    COUNT(CASE WHEN category_id IS NOT NULL THEN 1 END) as with_category
FROM documents 
WHERE type = 'client' 
  AND not_used_doc IS NULL
GROUP BY doc_type
ORDER BY count DESC;
```

## 5. Check General Category Document Count
```sql
-- Should show total documents in General category
SELECT 
    dc.id,
    dc.name,
    COUNT(d.id) as document_count
FROM document_categories dc
LEFT JOIN documents d ON dc.id = d.category_id 
    AND d.not_used_doc IS NULL 
    AND d.type = 'client'
    AND (d.doc_type = 'documents' OR d.doc_type IS NULL OR d.doc_type = '')
WHERE dc.name = 'General' 
  AND dc.is_default = 1
GROUP BY dc.id, dc.name;
```

## 6. Find Documents That Might Still Be Missing
```sql
-- These documents might need manual review
SELECT 
    d.id,
    d.client_id,
    d.file_name,
    d.doc_type,
    d.category_id,
    d.type,
    d.created_at
FROM documents d
WHERE d.type = 'client'
  AND d.not_used_doc IS NULL
  AND d.doc_type = 'documents'
  AND d.category_id IS NULL
LIMIT 100;
```

## Expected Results After Fix

1. ✅ Query 2 should return `0` (no documents without category)
2. ✅ Query 3 should show all user's documents with `category_name = 'General'`
3. ✅ Query 4 should show most documents have `with_category > 0`
4. ✅ Query 5 should show large number of documents (e.g., 58162)
5. ✅ Query 6 should return 0 rows

## Testing in Application

### Test 1: View Documents Tab
1. Log in to the application
2. Navigate to client MANJ231133395
3. Click on "Documents" tab
4. Click on "General" category
5. ✅ All previously uploaded documents should be visible

### Test 2: Upload New Document
1. In Documents tab, click "Add Checklist"
2. Enter a checklist name (e.g., "Test Document")
3. Upload a file
4. ✅ Document should automatically appear in "General" category

### Test 3: Document Count
1. In Documents tab, check the count next to "General" category
2. ✅ Count should match the number of documents visible

### Test 4: Switch Categories
1. Create a new category (e.g., "Passports")
2. ✅ Should start with 0 documents
3. Upload a document to new category
4. ✅ Should show in new category, not in General

### Test 5: Education/Migration Tabs
1. Go to "Education Documents" tab
2. ✅ Should NOT show regular documents (doc_type='documents')
3. Go to "Migration Documents" tab
4. ✅ Should NOT show regular documents (doc_type='documents')

## Rollback Plan (if needed)

If issues arise, the changes can be rolled back:

```bash
# Rollback migration
php artisan migrate:rollback --step=1

# Or manually in database:
UPDATE documents 
SET category_id = NULL 
WHERE category_id IN (
    SELECT id FROM document_categories WHERE name = 'General' AND is_default = 1
);
```

Then restore the previous versions of the 3 modified PHP files.

---

**Note:** The migration assigned **58,162 documents** to categories. This number should match the result of Query 5 above.
