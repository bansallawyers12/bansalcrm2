# Bootstrap 5 + Vite + jQuery Conflict Fix

## Problem Summary

After upgrading to Bootstrap 5.3.3 and installing Vite, the application had multiple JavaScript conflicts:

1. **jQuery Loaded Twice**: Once globally from `public/js/jquery_min_latest.js` and once as an ES module via Vite
2. **Bootstrap Loaded Twice**: Old Bootstrap 4 from `public/js/bootstrap.bundle.min.js` AND Bootstrap 5 from Vite
3. **CSP Errors**: Content Security Policy blocking Vite dev server scripts
4. **FullCalendar Error**: jQuery not available when FullCalendar tried to initialize

## Root Cause

The application uses legacy jQuery plugins (FullCalendar, DataTables, Select2) that expect global jQuery (`$` and `jQuery` on window), but Vite was importing jQuery as an ES module, creating two separate instances that don't share the same context.

## Solution Applied

### 1. Fixed `resources/js/bootstrap.js`
**REMOVED** the jQuery import to avoid module/global conflicts:

```javascript
// BEFORE (WRONG - caused conflicts)
import jQuery from 'jquery';
window.$ = window.jQuery = window.jQuery || jQuery;

// AFTER (CORRECT - use only global jQuery)
// Verify global jQuery is available (loaded in <head> before Vite)
if (typeof window.jQuery === 'undefined' || typeof window.$ === 'undefined') {
    console.warn('Warning: Global jQuery not found...');
}
```

**WHY**: Legacy scripts (FullCalendar, DataTables, etc.) need the global jQuery that's loaded in `<head>`, not a module-scoped version.

### 2. Fixed `resources/views/layouts/admin.blade.php`
**REMOVED** duplicate Bootstrap to avoid conflicts:

```html
<!-- BEFORE (WRONG - loaded Bootstrap twice)
<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
-->

<!-- AFTER (CORRECT - use Bootstrap 5 from Vite) -->
<!-- Bootstrap 5 is loaded via Vite in resources/js/bootstrap.js -->
```

**WHY**: Bootstrap 5 is already loaded via Vite. Loading both versions causes conflicts and breaks functionality.

### 3. Fixed FullCalendar Loading
Ensured FullCalendar loads **after** jQuery is ready:

```javascript
// Dynamic loading with retry logic
function loadFullCalendarScript() {
    if (typeof window.jQuery !== 'undefined' || typeof window.$ !== 'undefined') {
        var fcScript = document.createElement('script');
        fcScript.src = '{{ asset('js/fullcalendar.min.js') }}';
        fcScript.async = false;
        document.body.appendChild(fcScript);
    } else {
        // Retry if jQuery not ready
        setTimeout(function() { loadFullCalendarScript(attempts + 1); }, 100);
    }
}
```

### 4. Fixed CSP Policy
Added `script-src-elem` directive to allow Vite dev server:

```html
<meta http-equiv="Content-Security-Policy" content="
    script-src 'self' 'unsafe-inline' 'unsafe-eval' https: http://localhost:5173 http://127.0.0.1:5173 http://[::1]:5173 ...;
    script-src-elem 'self' 'unsafe-inline' 'unsafe-eval' https: http://localhost:5173 http://127.0.0.1:5173 http://[::1]:5173 ...;
    ...
">
```

## Load Order (Correct)

1. **`<head>`**: jQuery loaded globally (`jquery_min_latest.js`)
2. **`<head>`**: Vite loads (`@vite(['resources/js/app.js'])`)
   - Bootstrap 5 (ES module)
   - Vue 3
   - Lodash, Popper
3. **`<body>`**: Legacy plugins (FullCalendar, DataTables, Select2)
4. **`<body>`**: Custom scripts (`scripts.js`, `custom.js`)

## Key Principles

1. **One jQuery Instance**: Use ONLY the global jQuery, not module imports
2. **One Bootstrap Version**: Use ONLY Bootstrap 5 from Vite, not the old bundle
3. **Load Order Matters**: Global jQuery → Vite (Bootstrap 5) → Legacy Plugins → Custom Scripts
4. **CSP Must Allow Vite**: Include `script-src-elem` for Vite dev server

## Next Steps

1. **Rebuild Vite Assets**:
   ```bash
   npm run dev
   # OR for production
   npm run build
   ```

2. **Clear Browser Cache**: Hard refresh (Ctrl+Shift+R / Cmd+Shift+R)

3. **Verify No Errors**: Check browser console for:
   - ✅ No "$ is not defined" errors
   - ✅ No FullCalendar errors
   - ✅ No CSP blocking errors

## Files Modified

- ✅ `resources/js/bootstrap.js` - Removed jQuery module import
- ✅ `resources/views/layouts/admin.blade.php` - Removed duplicate Bootstrap, updated CSP, fixed FullCalendar loading
- ✅ `resources/views/layouts/admin-login.blade.php` - Added jQuery before scripts
- ✅ `resources/views/layouts/agent-login.blade.php` - Added jQuery before scripts

## Testing Checklist

- [ ] Login page loads without console errors
- [ ] Admin dashboard loads without console errors
- [ ] FullCalendar displays correctly on reports pages
- [ ] DataTables work correctly
- [ ] Form validation works
- [ ] Bootstrap 5 components (modals, dropdowns, tooltips) work
- [ ] Vite HMR (Hot Module Replacement) works in dev mode

## Bootstrap 5 Notes

**Important**: Bootstrap 5 does NOT require jQuery. However, this application uses many legacy jQuery plugins, so:

- Keep jQuery loaded globally for legacy plugins
- Bootstrap 5 from Vite works independently (vanilla JS)
- Do NOT use jQuery methods on Bootstrap 5 components (use `bootstrap` object instead)

Example:
```javascript
// WRONG (Bootstrap 4 jQuery method)
$('#myModal').modal('show');

// CORRECT (Bootstrap 5 vanilla JS)
const myModal = new bootstrap.Modal(document.getElementById('myModal'));
myModal.show();
```

---

**Date**: January 2026  
**Laravel**: 11.x  
**Bootstrap**: 5.3.3  
**Vite**: 6.0.5  
**jQuery**: 3.7.1 (global, for legacy plugins)


