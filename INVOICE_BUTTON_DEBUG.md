# Invoice Save & Preview Button - Deep Debugging Report

## Issue Summary
The "Save & Preview" and "Save" buttons on the invoice forms are not submitting the form.

## Changes Made

### 1. Added Hidden Input Field
Added `<input type="hidden" name="btn" value="save">` to three invoice forms:
- `resources/views/Admin/invoice/general-invoice.blade.php` (line 360)
- `resources/views/Admin/invoice/edit-gen.blade.php` (line 416)
- `resources/views/Admin/invoice/edit.blade.php` (line 597)

**Location**: Placed immediately before the button group, matching the pattern in `commission-invoice.blade.php`

### 2. Cleared Laravel View Cache
Ran: `php artisan view:clear`

## How It Should Work

1. **Button Click**: User clicks "Save & Preview"
   ```html
   <button onclick="customValidate('invoiceform','savepreview')" type="button">Save & Preview</button>
   ```

2. **JavaScript Function Called**: `customValidate('invoiceform', 'savepreview')`
   - Location: `public/js/custom-form-validation.js` line 107
   - Shows loader: `$(".popuploader").show()`
   - Validates form fields with `data-valid` attribute
   - Sets hidden field: `$('input[name="btn"]').val(savetype)` (line 2711)
   - Submits form: `$("form[name="+formName+"]").submit()` (line 2713)

3. **Form Submission**: POST to `/admin/invoice/general-store`

4. **Controller Processing**: `InvoiceController@generalStore`
   - Line 733: Checks `if(@$requestData['btn'] == 'savepreview')`
   - If true: Redirect to `/admin/invoice/view/{id}` (preview)
   - If false: Redirect to `/admin/invoice/unpaid` (list)

## Current Status

### ✅ What's Working:
- Hidden input field is present in the HTML (visible in accessibility snapshot as textbox with name="save")
- Form structure is correct
- JavaScript files are loading without errors
- Form name attribute is correctly set to "invoiceform"

### ❌ What's NOT Working:
- Form is not submitting when button is clicked
- No POST request appears in network logs
- Page stays on same URL after button click

## Possible Causes to Investigate

### 1. JavaScript Validation Failing Silently
The `customValidate` function validates fields with `data-valid` attribute:
```php
{!! Form::date('invoice_date', date('Y-m-d'), array('class' => 'form-control', 'data-valid'=>'', ...))  !!}
```

**Check**: Are there validation errors being swallowed?

### 2. Loader Blocking
Line 109 shows: `$(".popuploader").show()`
Line 209 hides it on error: `$(".popuploader").hide()`

**Check**: Is the loader element present and properly implemented?

### 3. jQuery Selector Not Finding Elements
The function uses:
- `$("form[name="+formName+"]")` - to find the form
- `$('input[name="btn"]')` - to find the hidden input

**Check**: Are these selectors working in the browser context?

### 4. Event Handler Not Attached
The button uses inline `onclick` attribute.

**Check**: Is there a CSP (Content Security Policy) blocking inline JavaScript?

### 5. Form Submission Being Prevented
Something might be preventing the default form submission.

**Check**: Are there other event listeners on the form or buttons?

## Manual Testing Steps Required

Since automated browser testing is having issues, please manually test:

1. **Open Browser Developer Tools** (F12)
2. **Navigate to**: http://127.0.0.1:8000/admin/application/invoice/25802/2/3
3. **Open Console Tab**
4. **Test jQuery**:
   ```javascript
   $('form[name="invoiceform"]').length  // Should return 1
   $('input[name="btn"]').length  // Should return 1
   $('input[name="btn"]').val()  // Should return "save"
   ```

5. **Fill Form**: Enter amount (1000) and select Income Type (Income)

6. **Test Validation Function**:
   ```javascript
   customValidate('invoiceform', 'test')
   ```
   Watch console for any errors

7. **Click "Save & Preview"** button
   - Watch Network tab for POST request
   - Watch Console for JavaScript errors
   - Check if page redirects

8. **Check Hidden Field After Click**:
   ```javascript
   $('input[name="btn"]').val()  // Should return "savepreview" after clicking Save & Preview
   ```

## Files Modified
- ✅ resources/views/Admin/invoice/general-invoice.blade.php
- ✅ resources/views/Admin/invoice/edit-gen.blade.php  
- ✅ resources/views/Admin/invoice/edit.blade.php
- ✅ View cache cleared

## Next Steps
1. **Manual testing** required to identify exact point of failure
2. Check browser console for JavaScript errors during button click
3. Verify that `customValidate` function is defined and accessible
4. Check if there are any network/CORS issues preventing form submission
5. Verify Laravel routes are properly configured

## Related Files
- Controller: `app/Http/Controllers/Admin/InvoiceController.php`
- JavaScript: `public/js/custom-form-validation.js`
- Views: `resources/views/Admin/invoice/*.blade.php`
- Routes: `routes/web.php` line 477

