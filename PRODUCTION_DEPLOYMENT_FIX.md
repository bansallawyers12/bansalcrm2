# Production Deployment Fix for Issue #10

## Problem
The error "Unable to locate file in Vite manifest: resources/js/pages/admin/account.js" occurs in production because the Vite assets need to be rebuilt.

## Root Cause
1. **Conflicting Script Sections** (FIXED): `payablepaid.blade.php` had both `@push('scripts')` and `@section('scripts')` which caused code to display as text
2. **Missing Vite Manifest**: Production server doesn't have the compiled Vite assets

## Files Fixed
- `resources/views/Admin/account/payablepaid.blade.php` - Removed conflicting `@section('scripts')`, consolidated into `@push('scripts')`

## Production Deployment Steps

### CRITICAL: Run these commands on your production server

```bash
# Navigate to your project directory
cd /path/to/bansalcrm2

# Install/update Node dependencies
npm install

# Build Vite assets for production
npm run build

# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Verify the Build
After running `npm run build`, check that these files exist:
- `public/build/manifest.json` - Should contain an entry for `resources/js/pages/admin/account.js`
- `public/build/assets/` - Should contain the compiled JS files

## Why This Works Locally But Not in Production

**Local Environment:**
- Uses Vite development server (`npm run dev`)
- Assets are served on-the-fly without needing to be in the manifest
- Hot module replacement (HMR) enabled

**Production Environment:**
- Uses pre-compiled assets from `npm run build`
- Assets must exist in `public/build/manifest.json`
- No dev server, so missing files cause "Unable to locate file" errors

## Verification Steps

After deployment, verify these pages work correctly:
1. Navigate to **Accounts → Payment** tab
2. Navigate to **Accounts → Income Sharing**
3. Check that:
   - No raw code is displayed
   - Email modal works properly
   - Payment forms function correctly
   - No console errors about missing Vite files

## Prevention for Future Deployments

Add to your deployment script:
```bash
#!/bin/bash
# deployment.sh

# Pull latest code
git pull origin master

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install

# Build assets
npm run build

# Clear and rebuild caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force
```

## Troubleshooting

### If npm run build fails:
```bash
# Check Node version (should be 16+ for Vite)
node --version

# Clear npm cache
npm cache clean --force
rm -rf node_modules
rm package-lock.json
npm install
npm run build
```

### If still showing "Unable to locate file" error:
1. Check file permissions on `public/build/` directory
2. Verify web server can read files in `public/build/`
3. Check that `.env` has `APP_ENV=production`
4. Verify `vite.config.js` includes the account.js file (it does - line 14)

### If code is still showing as text:
1. Clear browser cache
2. Run `php artisan view:clear` on server
3. Check that `payablepaid.blade.php` doesn't have conflicting script sections

## Summary
- **Code fix**: Removed conflicting blade directives in `payablepaid.blade.php`
- **Production fix**: Must run `npm run build` to compile Vite assets
- **Long-term**: Include `npm run build` in deployment pipeline
