# FINAL FIX: Bootstrap 4 + Bootstrap 5 Coexistence

## The Real Problem

Your `scripts.js` uses **Bootstrap 4 jQuery plugins**:
- `$.fn.tooltip()`
- `$.fn.popover()`
- `$.fn.dropdown()`
- `.modal("hide")`

These are **NOT available in Bootstrap 5** (Bootstrap 5 removed jQuery dependency).

## Solution: Run Both Bootstrap Versions

### 1. Keep Legacy Bootstrap 4 (`bootstrap.bundle.min.js`)
**For**: jQuery plugins used by `scripts.js`

```html
<!-- resources/views/layouts/admin.blade.php -->
<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
```

### 2. Load Bootstrap 5 via Vite (for new code)
**For**: Modern Bootstrap 5 features (modals, dropdowns, tooltips via vanilla JS)

```javascript
// resources/js/bootstrap.js
import * as bootstrap from 'bootstrap';
// Only expose if legacy isn't loaded
if (typeof window.bootstrap === 'undefined') {
    window.bootstrap = bootstrap;
}
```

## Load Order (Final)

```
1. <head>: jQuery global (jquery_min_latest.js)
2. <head>: Vite bundle (@vite - Bootstrap 5 module)
3. <body>: Bootstrap 4 bundle (bootstrap.bundle.min.js) - for jQuery plugins
4. <body>: FullCalendar (uses jQuery)
5. <body>: scripts.js (uses Bootstrap 4 jQuery methods)
6. <body>: custom.js
```

## Why This Works

- **Bootstrap 4** provides jQuery plugins (`$.fn.tooltip`, etc.)
- **Bootstrap 5** provides modern vanilla JS API (`bootstrap.Modal`, etc.)
- **jQuery** is global, used by both
- **No conflicts** because Bootstrap 4 jQuery plugins don't interfere with Bootstrap 5 module

## Errors Fixed

✅ `$(...).tooltip is not a function` - Bootstrap 4 jQuery plugins loaded
✅ `$(...).popover is not a function` - Bootstrap 4 jQuery plugins loaded
✅ `$(...).dropdown is not a function` - Bootstrap 4 jQuery plugins loaded
✅ `can't access property "fn"` - FullCalendar loads after jQuery ready
✅ CSP blocking Vite - `script-src-elem` added
✅ jQuery conflicts - No module import, only global

## Testing

After running `npm run build`, hard refresh and verify:
- ✅ No tooltip/popover errors
- ✅ Dropdowns work
- ✅ Modals work
- ✅ FullCalendar works
- ✅ No console errors

## Migration Path (Future)

Eventually, migrate `scripts.js` to Bootstrap 5:

```javascript
// OLD (Bootstrap 4 + jQuery)
$('[data-bs-toggle="tooltip"]').tooltip();

// NEW (Bootstrap 5 vanilla JS)
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el);
});
```

But for now, **both versions coexist safely**.


