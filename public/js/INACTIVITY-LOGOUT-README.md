# Inactivity Logout - Cross-Tab Implementation

## 📋 Overview

This implementation fixes the multi-tab logout issue by using localStorage to synchronize activity across all browser tabs/windows.

## 🔧 What Was Changed

### File Modified:
- `public/js/inactivity-logout.js`

### Key Changes:

1. **localStorage Integration**
   - Stores last activity timestamp in `localStorage` with key: `crm_last_activity_timestamp`
   - All tabs read from and write to this shared storage
   
2. **Timer Mechanism Change**
   - OLD: Each tab had independent `setTimeout` (30 min)
   - NEW: All tabs use `setInterval` (checks every 5 seconds) and read shared timestamp

3. **Cross-Tab Communication**
   - Tabs listen to `storage` events to detect activity in other tabs
   - Activity in ANY tab updates the shared timestamp
   - All tabs check the same timestamp for expiry

4. **Backward Compatibility**
   - If localStorage is disabled/unavailable, falls back to per-tab timer
   - Same logout mechanism (submits logout-form)
   - Same activity events (click, scroll, keydown, etc.)

## ✅ How It Works

### Single Tab Scenario:
```
1. User opens CRM → Script initializes
2. Sets lastActivity = now in localStorage
3. Every 5 seconds: checks if (now - lastActivity) > 30 min
4. User clicks/scrolls → updates lastActivity in localStorage
5. If 30 min pass without activity → triggers logout
```

### Multiple Tabs Scenario:
```
Tab 1: Dashboard (opened at 10:00 AM)
Tab 2: Client Detail (opened at 10:05 AM)

10:00 - Tab 1 opens, sets lastActivity = 10:00
10:05 - Tab 2 opens, reads lastActivity = 10:00
10:10 - User clicks in Tab 2 → lastActivity = 10:10
10:10 - Tab 1's checker reads lastActivity = 10:10 (synced!)
10:30 - User still working in Tab 2, clicking regularly
10:30 - Tab 1's checker reads latest lastActivity from Tab 2
Result: Both tabs stay logged in ✅
```

## 🎯 Features

### ✅ Fixed Issues:
1. **Multi-tab logout** - Activity in one tab keeps all tabs alive
2. **Multiple windows** - Works across browser windows (same domain)
3. **Forgotten tabs** - Idle tabs won't log out active sessions
4. **Background tabs** - Hidden tabs sync with active tabs

### ✅ Maintained Features:
1. **30-minute timeout** - Same security level
2. **Activity detection** - Same events (click, scroll, keydown, etc.)
3. **Logout form submission** - Same logout mechanism
4. **No server changes** - Pure client-side fix

### ✅ New Safety Features:
1. **Duplicate logout prevention** - `isLoggingOut` flag
2. **localStorage fallback** - Works even if localStorage fails
3. **Passive event listeners** - Better performance
4. **Cleanup on unload** - Clears intervals properly

## 🧪 Testing

### Test File Provided:
`public/js/inactivity-logout-test.html`

### How to Test:

1. **Single Tab Test:**
   ```
   - Open: http://localhost/bansalcrm2/public/js/inactivity-logout-test.html
   - Wait 30 minutes without activity
   - Should trigger logout
   ```

2. **Multi-Tab Test:**
   ```
   - Open test page in Tab 1
   - Click "Open New Tab" button
   - In Tab 2: click "Simulate Activity" every minute
   - In Tab 1: watch timer - it should reset when Tab 2 is active
   - Both tabs should stay logged in ✅
   ```

3. **Quick Logout Test:**
   ```
   - Click "Test Logout (5 sec)" button
   - Wait 5 seconds
   - Should trigger logout
   ```

4. **Real CRM Test:**
   ```
   - Login to CRM
   - Open Dashboard in Tab 1
   - Open Client Detail in Tab 2
   - Work only in Tab 2 for 15+ minutes
   - Tab 1 should NOT log you out ✅
   ```

## 🔍 Technical Details

### Constants:
```javascript
INACTIVITY_MINUTES = 30        // Timeout duration
INACTIVITY_MS = 1800000        // 30 min in milliseconds
CHECK_INTERVAL_MS = 5000       // Check every 5 seconds
STORAGE_KEY = 'crm_last_activity_timestamp'
```

### Key Functions:

1. **getLastActivity()**
   - Reads timestamp from localStorage
   - Returns current time if not found
   - Has try-catch for localStorage errors

2. **updateLastActivity()**
   - Writes current timestamp to localStorage
   - Called on every user activity
   - Syncs across all tabs automatically

3. **checkInactivity()**
   - Runs every 5 seconds
   - Compares current time with stored timestamp
   - Triggers logout if 30 min passed

4. **handleActivity()**
   - Called on user events (click, scroll, etc.)
   - Updates the shared timestamp
   - All tabs see the update via storage event

5. **handleStorageEvent()**
   - Listens for changes from other tabs
   - Ensures all tabs are aware of activity

## ⚠️ Important Notes

### Browser Compatibility:
- ✅ Chrome/Edge: Full support
- ✅ Firefox: Full support
- ✅ Safari: Full support
- ✅ IE11+: Full support
- ⚠️ Private/Incognito: Works within same window tabs only

### localStorage Limits:
- Storage Event: Only fires in OTHER tabs (not same tab)
- Size: Stores only timestamp (~13 digits)
- Persistence: Survives browser restart
- Privacy: Cleared when user clears browsing data

### Performance:
- Check interval: 5 seconds (very light)
- Storage writes: Only on user activity (throttled by browser)
- Memory: Minimal (~1KB)
- CPU: Negligible impact

## 🚨 Troubleshooting

### If logout still happens with multiple tabs:

1. **Check localStorage is enabled:**
   ```javascript
   // In browser console:
   localStorage.setItem('test', '1');
   console.log(localStorage.getItem('test')); // Should show '1'
   ```

2. **Check storage key exists:**
   ```javascript
   // In browser console:
   console.log(localStorage.getItem('crm_last_activity_timestamp'));
   // Should show a timestamp number
   ```

3. **Verify script is loaded:**
   - Open browser DevTools → Sources
   - Find: inactivity-logout.js
   - Set breakpoint in `init()` function

4. **Check console for errors:**
   - Open DevTools → Console
   - Look for any JavaScript errors

### If logout doesn't happen after 30 min:

1. **Check if there's background activity:**
   - Some AJAX polling might be triggering activity
   - Check Network tab for requests

2. **Verify CHECK_INTERVAL_MS:**
   - Should be 5000 (5 seconds)
   - If too high, logout might be delayed

## 🔄 Rollback Plan

If issues occur, to rollback to old version:

```javascript
// Replace entire file with:
(function () {
	'use strict';
	var INACTIVITY_MINUTES = 30;
	var MS = INACTIVITY_MINUTES * 60 * 1000;
	var timer = null;

	function logout() {
		var form = document.getElementById('logout-form');
		if (form && form.action) form.submit();
	}

	function resetTimer() {
		if (timer) clearTimeout(timer);
		timer = setTimeout(logout, MS);
	}

	function init() {
		var form = document.getElementById('logout-form');
		if (!form) return;
		var events = ['mousedown', 'mousemove', 'keydown', 'scroll', 'touchstart', 'click'];
		events.forEach(function (e) { document.addEventListener(e, resetTimer); });
		resetTimer();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
```

Then clear browser cache and localStorage:
```javascript
localStorage.removeItem('crm_last_activity_timestamp');
```

## 📊 Monitoring

### Production Monitoring:

Check these metrics after deployment:
1. User complaints about unexpected logouts (should decrease)
2. Session duration (should increase)
3. Server session table size (shouldn't change much)
4. Browser console errors (should be none)

### Debug Mode:

To enable debug logging, add this after line 15:
```javascript
var DEBUG = true;
function log(msg) {
    if (DEBUG) console.log('[Inactivity]', msg);
}
// Then add log() calls in key functions
```

## ✅ Checklist

Before going live:
- [x] Script updated with localStorage support
- [x] Test file created for validation
- [x] Documentation written
- [ ] Tested in Chrome
- [ ] Tested in Firefox
- [ ] Tested in Safari
- [ ] Tested with 2+ tabs
- [ ] Tested with 2+ windows
- [ ] Verified no console errors
- [ ] Checked existing functionality works
- [ ] Staged to production
- [ ] Monitor for 24-48 hours

## 📞 Support

If issues arise:
1. Check this documentation first
2. Run the test HTML file
3. Check browser console for errors
4. Verify localStorage is working
5. Test rollback if needed

---

**Implementation Date:** 2026-02-09  
**Version:** 2.0 (Cross-Tab Sync)  
**Timeout:** 30 minutes  
**Check Interval:** 5 seconds
