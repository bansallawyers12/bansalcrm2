# Step-by-Step Upgrade Implementation Guide

## Quick Start: Recommended Path (Safest)

This guide follows the **Immediate (Low Risk)** approach from the main plan.

---

## Step 1: Create Backup Branch

```bash
cd C:\xampp\htdocs\bansalcrm2
git status
git checkout -b upgrade/modernize-build-tools
git add .
git commit -m "Backup: Before npm package modernization"
```

---

## Step 2: Clean Install Environment

```bash
# Remove old dependencies
Remove-Item -Recurse -Force node_modules
Remove-Item -Force package-lock.json
```

---

## Step 3: Update package.json

Replace your current `package.json` with this updated version:

```json
{
    "private": true,
    "scripts": {
        "dev": "npm run development",
        "development": "mix",
        "watch": "mix watch",
        "watch-poll": "mix watch -- --watch-options-poll=1000",
        "hot": "mix watch --hot",
        "prod": "npm run production",
        "production": "mix --production"
    },
    "devDependencies": {
        "axios": "^1.7.0",
        "bootstrap": "^4.6.2",
        "cross-env": "^7.0.3",
        "jquery": "^3.7.1",
        "laravel-mix": "^6.0.49",
        "lodash": "^4.17.21",
        "popper.js": "^1.16.1",
        "sass": "^1.77.0",
        "sass-loader": "^13.3.2",
        "vue": "^2.7.16",
        "vue-template-compiler": "^2.7.16"
    }
}
```

**Key Changes:**
- ✅ Laravel Mix: v2 → v6
- ✅ Axios: v0.18 → v1.7.0
- ✅ jQuery: v3.2 → v3.7.1
- ✅ Added `sass` (replaces node-sass)
- ✅ Added `sass-loader` (required for Mix v6)
- ⚠️ **Keeping Vue 2** for now (safer, upgrade later)
- ⚠️ **Keeping Bootstrap 4** for now (safer, upgrade later)

---

## Step 4: Install Dependencies

```bash
npm install
```

If you encounter peer dependency warnings, you can use:
```bash
npm install --legacy-peer-deps
```

---

## Step 5: Update webpack.mix.js

Update your `webpack.mix.js`:

```javascript
const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
   .vue()
   .sass('resources/sass/app.scss', 'public/css')
   .options({
       processCssUrls: false
   });

// Enable source maps in development
if (!mix.inProduction()) {
    mix.sourceMaps();
}
```

**Key Changes:**
- Added `.vue()` for Vue support
- Added `.options()` to prevent CSS URL processing issues
- Added source maps for development

---

## Step 6: Update resources/js/bootstrap.js

Update Popper.js import (if needed):

```javascript
window._ = require('lodash');

// Popper.js for Bootstrap 4
try {
    window.Popper = require('popper.js').default;
} catch (e) {
    console.warn('Popper.js not loaded');
}

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');
    require('bootstrap');
} catch (e) {
    console.error('Bootstrap failed to load:', e);
}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
```

---

## Step 7: Test the Build

```bash
# Development build
npm run dev

# If successful, try production build
npm run production
```

---

## Step 8: Fix Any Errors

### Common Issues:

#### Issue 1: "Cannot find module 'sass'"
**Solution:** Make sure `sass` is in devDependencies and run `npm install` again

#### Issue 2: "Vue loader errors"
**Solution:** Ensure `vue-loader` and `vue-template-compiler` versions match Vue version

#### Issue 3: "PostCSS errors"
**Solution:** May need to add PostCSS config or update autoprefixer

#### Issue 4: "Path resolution errors"
**Solution:** Check that file paths in `webpack.mix.js` are correct

---

## Step 9: Verify Frontend

1. Clear Laravel cache:
   ```bash
   php artisan cache:clear
   php artisan view:clear
   php artisan config:clear
   ```

2. Test in browser:
   - Open your application
   - Check browser console for errors
   - Test JavaScript functionality
   - Test forms and AJAX calls
   - Test Vue components (if any)

---

## Step 10: Run Security Audit

```bash
npm audit
npm audit fix
```

---

## Alternative: If You Want Vue 3

If you want to upgrade Vue to v3 (after successful build tools upgrade):

### Update package.json:
```json
"vue": "^3.5.0",
"vue-loader": "^17.3.1"
```

Remove:
```json
"vue-template-compiler": "^2.7.16"  // Not needed in Vue 3
```

### Update resources/js/app.js:
```javascript
import { createApp } from 'vue';
import ExampleComponent from './components/ExampleComponent.vue';

require('./bootstrap');

const app = createApp({
    components: {
        ExampleComponent
    }
});

app.mount('#app');
```

### Update ExampleComponent.vue:
Vue 3 is mostly compatible, but check:
- Template syntax (usually fine)
- Script setup (optional, can use Options API)
- Lifecycle hooks (mounted, etc. - same names)

---

## Rollback Instructions

If something goes wrong:

```bash
# Switch back to main branch
git checkout main

# Or restore from backup
git checkout upgrade/modernize-build-tools
git checkout HEAD~1 -- package.json package-lock.json
npm install --ignore-scripts
```

---

## Success Criteria

✅ Build completes without errors
✅ `public/js/app.js` is generated
✅ `public/css/app.css` is generated
✅ No console errors in browser
✅ All JavaScript functionality works
✅ Forms submit correctly
✅ AJAX calls work
✅ Vue components render (if used)

---

## Next Steps (After Successful Upgrade)

1. **Upgrade Vue to v3** (if desired)
2. **Upgrade Bootstrap to v5** (separate project, many view changes)
3. **Consider Vite** (Laravel 11+ uses Vite instead of Mix)
4. **Update other dependencies** gradually
5. **Set up automated testing** for frontend

---

## Support & Troubleshooting

### Build Fails
- Check Node.js version (should be v18+)
- Clear npm cache: `npm cache clean --force`
- Delete `node_modules` and `package-lock.json`, reinstall

### Runtime Errors
- Check browser console
- Verify asset paths in views
- Check Laravel mix manifest: `public/mix-manifest.json`

### Performance Issues
- Run production build: `npm run production`
- Enable asset versioning in Laravel Mix
- Consider code splitting for large apps

