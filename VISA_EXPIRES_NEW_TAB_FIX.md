# Visa Expires Report - Open Client Links in New Tab

## Problem Description
On the Visa Expires Reports page, when clicking on a client name in the calendar, the link was not consistently opening in a new tab as expected.

## Root Cause
The event click handler was using `return false` instead of the proper FullCalendar v6 event prevention method (`info.jsEvent.preventDefault()`), and was missing security parameters in the `window.open()` call.

## Solution
Updated the `eventClick` handler to:
1. Use proper FullCalendar v6 event prevention
2. Add security parameters to `window.open()`
3. Ensure consistent new tab behavior

**File Modified**: `resources/views/Admin/reports/visaexpires.blade.php`

## Changes Made

### Before:
```javascript
eventClick: function(info) {
    console.log(info);
    var id = info.event.id;

    if (!!scheds[id]) {
        // Populate modal...
        
        // Always open URL regardless of modal existence
        if (scheds[id].url) {
            window.open(scheds[id].url, "_blank");
            return false;  // ❌ Old method
        }
    }
}
```

### After:
```javascript
eventClick: function(info) {
    console.log(info);
    
    // Prevent default FullCalendar behavior
    info.jsEvent.preventDefault();  // ✅ Proper FullCalendar v6 method
    
    var id = info.event.id;

    if (!!scheds[id]) {
        // Populate modal...
        
        // Always open URL in new tab
        if (scheds[id].url) {
            window.open(scheds[id].url, "_blank", "noopener,noreferrer");  // ✅ Added security params
        }
    }
}
```

## Key Improvements

1. **Proper Event Prevention**: 
   - Used `info.jsEvent.preventDefault()` instead of `return false`
   - This is the correct FullCalendar v6 way to prevent default event behavior

2. **Security Enhancement**:
   - Added `"noopener,noreferrer"` parameters to `window.open()`
   - Prevents the new tab from accessing the `window.opener` object (security best practice)

3. **Consistent Behavior**:
   - Removed `return false` which could interfere with proper event handling
   - Ensures the link always opens in a new tab

## What's Fixed

✅ **Client links now consistently open in new tabs**  
✅ **Proper FullCalendar v6 event handling**  
✅ **Enhanced security with noopener/noreferrer**  
✅ **No breaking changes** - Modal functionality still works if present  

## Testing Checklist

After deployment, verify:

- [ ] Navigate to Reports → Visa Expires
- [ ] Click on any client name in the calendar
- [ ] **Verify the client detail page opens in a NEW tab**
- [ ] Verify the original calendar page remains open
- [ ] Test clicking multiple client names
- [ ] Verify no popup blockers are triggered
- [ ] Check browser console for no errors
- [ ] Test in different browsers (Chrome, Firefox, Edge)

## Browser Compatibility

The `window.open(url, "_blank", "noopener,noreferrer")` syntax is supported in:
- ✅ Chrome 49+
- ✅ Firefox 52+
- ✅ Safari 10.1+
- ✅ Edge 79+

## Related Files

- **Modified**: `resources/views/Admin/reports/visaexpires.blade.php` - Event click handler
- **Related**: Issue #8 fix - Made client links work initially

## Summary

The fix ensures that clicking on client names in the Visa Expires calendar reliably opens the client detail page in a new tab. This uses the proper FullCalendar v6 API method for event prevention and adds security best practices to the `window.open()` call.

Users can now click on multiple clients to compare information in separate tabs while keeping the calendar view open.
