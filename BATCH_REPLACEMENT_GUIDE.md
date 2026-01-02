# Batch Replacement Guide for Bootstrap 5 Migration

## Quick Find & Replace Instructions

Use your IDE's Find & Replace (Ctrl+H / Cmd+H) with the following patterns:

---

## 1. Input-Group-Prepend Fix

### Method 1: Two-Step Replacement (Safest)

**Step 1: Replace opening tag**
- **Find**: `<div class="input-group-prepend">`
- **Replace**: (leave empty - just delete)
- **Scope**: All files in `resources/views/`

**Step 2: Replace div wrapper with span**
- **Find**: `<div class="input-group-text">`
- **Replace**: `<span class="input-group-text">`
- **Scope**: Same files

**Step 3: Fix closing tags**
- **Find**: `</div></div>` (look for double closing divs where input-group-text was)
- **Replace**: `</span>`
- **Scope**: Same files
- **Note**: Be careful - only replace where it's closing input-group-text, not other nested divs

### Method 2: Manual Pattern (More Control)

Search for this exact pattern:
```
<div class="input-group">
    <div class="input-group-prepend">
        <div class="input-group-text">
```

Replace with:
```
<div class="input-group">
    <span class="input-group-text">
```

Then find:
```
        </div>
    </div>
```

Where it closes the input-group-text/prepend structure, replace with:
```
    </span>
```

---

## 2. Modal Close Button Fix

### Find & Replace Pattern

**Find** (multiline search enabled):
```
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
```

**Replace**:
```
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
```

**Or** if spacing varies, use regex:

**Find** (regex enabled):
```
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">[\s\S]*?<span aria-hidden="true">&times;</span>[\s\S]*?</button>
```

**Replace**:
```
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
```

---

## 3. Files That Still Need Updates

### Input-Group-Prepend (84 remaining instances):

High Priority:
- `Admin/clients/addclientmodal.blade.php` - 13 remaining
- `Admin/partners/addpartnermodal.blade.php` - 17 instances
- `Agent/clients/addclientmodal.blade.php` - 17 instances
- `Admin/products/addproductmodal.blade.php` - 11 instances

Medium Priority:
- `Admin/invoice/*.blade.php` - ~10 instances
- `Admin/agents/*.blade.php` - ~2 instances
- `Admin/partners/detail.blade.php` - 2 instances

### Modal Close Buttons (213 remaining instances):

High Priority:
- `Admin/clients/addclientmodal.blade.php` - 30 instances
- `Admin/partners/addpartnermodal.blade.php` - 25 instances
- `Agent/clients/addclientmodal.blade.php` - 23 instances
- `Admin/products/addproductmodal.blade.php` - 15 instances
- `Admin/clients/detail.blade.php` - 12 instances
- `Agent/clients/detail.blade.php` - 8 instances

---

## 4. Verification Steps

After batch replacement:

1. **Test input groups visually** - Icons should align properly
2. **Test modal close buttons** - Should show X icon and close modals
3. **Check browser console** - No JavaScript errors
4. **Visual inspection** - Run through a few pages to verify layout

---

## 5. IDE-Specific Tips

### VS Code / Cursor:
1. Open Find & Replace (Ctrl+H)
2. Click ".*" to enable regex
3. Use patterns above
4. Click folder icon to scope to `resources/views/`
5. Review matches before replacing

### PHPStorm:
1. Ctrl+Shift+R (Replace in Path)
2. Enable regex (.* button)
3. Scope: `resources/views/**/*.blade.php`
4. Use patterns above

---

## ⚠️ Important Notes

1. **Backup first** - Consider committing current changes before batch replacement
2. **Review matches** - Check a few matches before replacing all
3. **Test incrementally** - Replace in one file/folder at a time
4. **Input-group** - Be careful not to break nested structures
5. **Modal buttons** - Some might have different spacing/formatting

---

**Last Updated**: January 2026

