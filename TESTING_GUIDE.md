# Testing Guide - URL Restructure

## Pre-Testing Setup

### 1. Clear All Caches
```bash
cd c:\xampp\htdocs\bansalcrm2
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

### 2. Verify Routes Are Loaded
```bash
php artisan route:list
```

Look for:
- ✓ Routes at root level (dashboard, users, products, etc.)
- ✓ Login routes at both `/` and `/admin`
- ✓ AdminConsole routes at `/adminconsole/*`
- ✓ Client routes at `/clients/*`

## Manual Testing Checklist

### Login & Authentication (Critical)

#### Test 1: Root Login
1. Navigate to: `http://localhost/bansalcrm2/`
2. ✅ Should show login page
3. Enter credentials and login
4. ✅ Should redirect to `/dashboard`

#### Test 2: Admin Login
1. Logout
2. Navigate to: `http://localhost/bansalcrm2/admin`
3. ✅ Should show login page
4. Enter credentials and login
5. ✅ Should redirect to `/dashboard`

#### Test 3: Admin Login (with /login)
1. Logout
2. Navigate to: `http://localhost/bansalcrm2/admin/login`
3. ✅ Should show login page

#### Test 4: Logout
1. Click logout in navigation
2. ✅ Should redirect to login page
3. ✅ Should not be able to access dashboard without login

### Navigation Menu (Critical)

#### Test 5: Dashboard Link
1. Login
2. Click "Dashboard" in sidebar
3. ✅ Should load `/dashboard`
4. ✅ No 404 errors

#### Test 6: All Main Menu Links
Click each link and verify it works:
- ✅ Lead Manager → `/leads`
- ✅ Action → `/action`
- ✅ In Person → `/office-visits/waiting`
- ✅ Clients Manager → `/clients`
- ✅ Partners Manager → `/partners`
- ✅ Agents Manager → `/agents/active`
- ✅ Applications Manager → `/applications`
- ✅ Services → `/services`
- ✅ Products Manager → `/products`

#### Test 7: Accounts Dropdown
- ✅ Invoices → `/invoice/unpaid`
- ✅ Payment → `/account/payment`
- ✅ Invoice Schedule → `/invoice/invoiceschedules`
- ✅ Income Sharing → `/account/payableunpaid`

#### Test 8: Reports Dropdown
- ✅ Client Report → Check URL
- ✅ Applications Report → Check URL
- ✅ Invoice Report → Check URL

### CRUD Operations (Critical)

#### Test 9: Create Client
1. Go to Clients → `/clients`
2. Click "Create Client"
3. ✅ Should load `/clients/create`
4. Fill form and submit
5. ✅ Should save successfully
6. ✅ No 404 errors in console

#### Test 10: View Client Details
1. Click on a client
2. ✅ Should load `/clients/detail/{id}`
3. ✅ Page loads completely
4. ✅ No 404 errors in console

#### Test 11: Edit Client
1. From client detail, click edit
2. ✅ Should load `/clients/edit/{id}`
3. Make changes and save
4. ✅ Should save successfully

#### Test 12: Create Lead
1. Go to Leads → `/leads`
2. Click "Create Lead"
3. ✅ Should load `/leads/create`
4. Fill form and submit
5. ✅ Should save successfully

### AJAX Functionality (Critical)

#### Test 13: Add Note (Client Detail Page)
1. Go to client detail page
2. Add a note
3. ✅ Note saves successfully
4. ✅ No 404 errors in console
5. Check Network tab: ✅ POST to `/create-note` succeeds

#### Test 14: View Activities (Client Detail Page)
1. On client detail page
2. Check activities tab
3. ✅ Activities load
4. Check Network tab: ✅ GET to `/get-activities` succeeds

#### Test 15: Document Upload
1. On client detail page
2. Upload a document
3. ✅ Document uploads successfully
4. Check Network tab: ✅ POST to `/upload-document` succeeds

#### Test 16: Delete Action
1. Try to delete something (note, document, etc.)
2. ✅ Deletion works
3. Check Network tab: ✅ POST to `/delete_action` or similar succeeds

### Search Functionality

#### Test 17: Global Search
1. Use search bar in header
2. Search for a client name
3. ✅ Results appear
4. Click a result
5. ✅ Navigates to correct page (e.g., `/clients/detail/{id}`)

### Forms & Submissions

#### Test 18: Create Product
1. Go to Products → `/products`
2. Click "Create Product"
3. Fill form and submit
4. ✅ Saves successfully
5. ✅ Redirects correctly

#### Test 19: Create Partner
1. Go to Partners → `/partners`
2. Click "Create Partner"
3. Fill form and submit
4. ✅ Saves successfully

### AdminConsole (Verify Preserved)

#### Test 20: AdminConsole Access
1. Click profile → "Admin Console"
2. ✅ Should go to `/adminconsole/product-type` or similar
3. ✅ AdminConsole loads correctly
4. Try a few pages:
   - ✅ Product Type → `/adminconsole/product-type`
   - ✅ Workflow → `/adminconsole/workflow`
   - ✅ Tags → `/adminconsole/tags`

### Browser Console Checks

#### Test 21: Check for Errors
1. Open browser DevTools (F12)
2. Go to Console tab
3. Navigate through 5-10 different pages
4. ✅ No 404 errors
5. ✅ No "route not found" errors
6. ✅ No JavaScript errors related to URLs

#### Test 22: Check Network Tab
1. Open Network tab in DevTools
2. Perform actions (load pages, submit forms, upload files)
3. ✅ All requests return 200 status
4. ✅ No 404 errors
5. ✅ No 500 errors

## Browser Testing

Test in multiple browsers if possible:
- ✅ Chrome
- ✅ Firefox
- ✅ Edge

## Common Issues & Solutions

### Issue: Page shows 404
**Check:**
1. Route exists: `php artisan route:list | findstr "route-name"`
2. Cache cleared: `php artisan route:clear`

### Issue: AJAX calls fail with 404
**Check:**
1. Browser console for exact URL
2. Verify URL doesn't have `/admin/` prefix
3. Check if route exists

### Issue: Form submission fails
**Check:**
1. CSRF token present in form
2. Action URL correct
3. Method correct (GET/POST)

### Issue: Redirect loops on login
**Check:**
1. Middleware redirects to `route('admin.login')`
2. admin.login route exists

## Final Verification

After completing all tests:

### 1. Run Verification Script
```bash
php verify_changes.php
```

Should show: ✅ All verifications passed!

### 2. Check Laravel Logs
```bash
# Check for errors
cat storage/logs/laravel.log | grep -i error
```

Should be empty or only old errors.

### 3. Performance Check
- Pages load reasonably fast
- No excessive database queries
- No memory issues

## Sign-Off Checklist

Before marking as complete:

- [ ] All login paths work (/, /admin, /admin/login)
- [ ] All navigation links work
- [ ] All CRUD operations work
- [ ] All AJAX calls work
- [ ] Forms submit correctly
- [ ] File uploads work
- [ ] Search functionality works
- [ ] AdminConsole works
- [ ] No 404 errors in console
- [ ] No JavaScript errors
- [ ] Verification script passes
- [ ] Laravel logs clean

## Rollback Plan

If critical issues found:

### Using Git
```bash
git checkout routes/web.php
git checkout bootstrap/app.php
git checkout resources/views/Elements/Admin/
git checkout public/js/modern-search.js
git checkout public/js/pages/admin/client-detail.js
```

### Manual Restore
Restore from backup if created:
```bash
cp backup_before_url_update/routes/web.php routes/web.php
# ... etc
```

Then clear caches:
```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

