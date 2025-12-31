# TinyMCE Conditional Loading Implementation

## Overview
TinyMCE scripts are now loaded conditionally using Laravel's `@stack` and `@push` directives. This improves performance by only loading the editor (~200KB+) on pages that actually need it.

## Implementation Details

### Layout Files
All three layout files have been updated to use `@stack('tinymce-scripts')` instead of directly loading TinyMCE:

- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/adminnew.blade.php`
- `resources/views/layouts/agent.blade.php`

### TinyMCE Partial
A reusable partial has been created:
- `resources/views/partials/tinymce.blade.php`

This partial contains all TinyMCE scripts and can be included on pages that need the editor.

## How to Use

### On Pages That Need TinyMCE

Add this code block before the final `@endsection` in your Blade file:

```php
@push('tinymce-scripts')
@include('partials.tinymce')
@endpush
```

### Example Implementation

```php
@extends('layouts.admin')
@section('title', 'My Page')

@section('content')
<!-- Your page content -->
<textarea class="summernote-simple" name="description"></textarea>
@endsection

@push('tinymce-scripts')
@include('partials.tinymce')
@endpush
```

### If You Have a @section('scripts')

If your page already has a `@section('scripts')`, add the push directive within that section:

```php
@section('scripts')
<script>
    // Your other scripts
</script>

@push('tinymce-scripts')
@include('partials.tinymce')
@endpush

@endsection
```

## Files Already Updated

The following files have been updated to use conditional TinyMCE loading:

- `resources/views/AdminConsole/emails/create.blade.php`
- `resources/views/AdminConsole/emails/edit.blade.php`
- `resources/views/AdminConsole/crmemailtemplate/create.blade.php`
- `resources/views/AdminConsole/crmemailtemplate/edit.blade.php`
- `resources/views/Admin/action/index.blade.php`
- `resources/views/Admin/action/completed.blade.php`
- `resources/views/Admin/action/assign_to_me.blade.php`
- `resources/views/Admin/action/assigned_by_me.blade.php`
- `resources/views/Admin/clients/detail.blade.php`
- `resources/views/Admin/partners/detail.blade.php`
- `resources/views/Admin/officevisits/waiting.blade.php`

## Files That May Still Need Updates

The following files contain TinyMCE classes and may need the `@push` directive added:

### Admin Views
- `resources/views/Admin/products/*.blade.php` (multiple files)
- `resources/views/Admin/clients/addclientmodal.blade.php`
- `resources/views/Admin/clients/editclientmodal.blade.php`
- `resources/views/Admin/agents/addagentmodal.blade.php`
- `resources/views/Admin/agents/editagentmodal.blade.php`
- `resources/views/Admin/partners/addpartnermodal.blade.php`
- `resources/views/Admin/partners/editpartnermodal.blade.php`
- `resources/views/Admin/officevisits/archived.blade.php`
- `resources/views/Admin/officevisits/completed.blade.php`
- `resources/views/Admin/officevisits/attending.blade.php`
- `resources/views/Admin/officevisits/index.blade.php`
- `resources/views/Admin/leads/editnotemodal.blade.php`

### Agent Views
- `resources/views/Agent/clients/*.blade.php` (multiple files)

### Note on Modals
If modals with TinyMCE are included via `@include` in pages that already have the `@push` directive, they should work correctly. However, if a modal is loaded via AJAX or shown independently, ensure the parent page has the TinyMCE scripts pushed.

## Benefits

1. **Performance**: Reduces page load time on pages that don't need the editor
2. **Bandwidth**: Saves ~200KB+ on each page load for pages without editors
3. **Maintainability**: Centralized script management through partial
4. **Flexibility**: Easy to add/remove TinyMCE on specific pages

## Testing Checklist

When adding TinyMCE to a new page:

1. ✅ Add `@push('tinymce-scripts')` directive
2. ✅ Include the partial: `@include('partials.tinymce')`
3. ✅ Test that the editor initializes correctly
4. ✅ Verify form submission works with editor content
5. ✅ Check browser console for any errors

## Troubleshooting

### Editor Not Appearing
- Ensure `@push('tinymce-scripts')` is added before `@endsection`
- Check that textarea has the correct class: `summernote-simple`, `summernote`, `tinymce-simple`, `tinymce-full`, or `id="editor1"`
- Verify the partial file exists at `resources/views/partials/tinymce.blade.php`

### Script Conflicts
- Ensure TinyMCE scripts are loaded after jQuery (they should be, as layouts load jQuery first)
- Check browser console for JavaScript errors

## Maintenance Notes

- The TinyMCE configuration is in `public/js/tinymce-init.js`
- Compatibility layer for Summernote/CKEditor is in `public/js/tinymce-summernote-compat.js`
- All TinyMCE scripts are now centralized in the partial for easy updates



