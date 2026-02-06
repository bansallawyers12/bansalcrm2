# migrationmanager2: Marital Status Alignment Instructions

Apply these changes in **migrationmanager2** so marital status matches bansalcrm2: same field name (`marital_status`), same option set (Never Married, Engaged, Married, De Facto, Separated, Divorced, Widowed), no Single or Others. **No migration file** — code and dropdowns only.

---

## 1. Validation rules

Update the allowed marital status values everywhere they are validated.

### 1a. ClientsController

**File:** `app/Http/Controllers/CRM/ClientsController.php`

Find the line with `'marital_status' => 'nullable|in:Single,Married,...'` and replace with:

```php
'marital_status' => 'nullable|in:Never Married,Engaged,Married,De Facto,Separated,Divorced,Widowed',
```

### 1b. ClientPersonalDetailsController

**File:** `app/Http/Controllers/CRM/ClientPersonalDetailsController.php`

Find the rule like:
`'marital_status' => 'nullable|in:Single,Married,De Facto,Defacto,Divorced,Widowed,Separated'`

Replace with:

```php
'marital_status' => 'nullable|in:Never Married,Engaged,Married,De Facto,Separated,Divorced,Widowed',
```

Keep the existing logic that maps input `Defacto` to `De Facto` before save (so old forms still work until data is normalized).

### 1c. API ClientPortalController

**File:** `app/Http/Controllers/API/ClientPortalController.php`

Find the rule with `'marital_status' => 'sometimes|string|in:Single,Married,...'` and replace with:

```php
'marital_status' => 'sometimes|string|in:Never Married,Engaged,Married,De Facto,Separated,Divorced,Widowed',
```

---

## 2. Dropdowns (views)

Use this option set in every marital status dropdown: **Never Married, Engaged, Married, De Facto, Separated, Divorced, Widowed**. Use `value="De Facto"` (capital F). Remove Single and Others.

### 2a. Lead create

**File:** `resources/views/crm/leads/create.blade.php`

Find the `<select id="maritalStatus" name="marital_status">` block. Replace the options with:

```blade
<select id="maritalStatus" name="marital_status">
    <option value="">Select Marital Status</option>
    <option value="Never Married" {{ old('marital_status') == 'Never Married' ? 'selected' : '' }}>Never Married</option>
    <option value="Engaged" {{ old('marital_status') == 'Engaged' ? 'selected' : '' }}>Engaged</option>
    <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married</option>
    <option value="De Facto" {{ (old('marital_status') == 'Defacto' || old('marital_status') == 'De Facto') ? 'selected' : '' }}>De Facto</option>
    <option value="Separated" {{ old('marital_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
    <option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
    <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
</select>
```

### 2b. Lead edit

**File:** `resources/views/crm/leads/edit.blade.php`

Find the `<select id="maritalStatus" name="marital_status">` block. Replace the options with:

```blade
<select id="maritalStatus" name="marital_status">
    <option value="">Select Marital Status</option>
    <option value="Never Married" {{ $fetchedData->marital_status == 'Never Married' ? 'selected' : '' }}>Never Married</option>
    <option value="Engaged" {{ $fetchedData->marital_status == 'Engaged' ? 'selected' : '' }}>Engaged</option>
    <option value="Married" {{ $fetchedData->marital_status == 'Married' ? 'selected' : '' }}>Married</option>
    <option value="De Facto" {{ ($fetchedData->marital_status == 'Defacto' || $fetchedData->marital_status == 'De Facto') ? 'selected' : '' }}>De Facto</option>
    <option value="Separated" {{ $fetchedData->marital_status == 'Separated' ? 'selected' : '' }}>Separated</option>
    <option value="Divorced" {{ $fetchedData->marital_status == 'Divorced' ? 'selected' : '' }}>Divorced</option>
    <option value="Widowed" {{ $fetchedData->marital_status == 'Widowed' ? 'selected' : '' }}>Widowed</option>
</select>
```

### 2c. Client edit

**File:** `resources/views/crm/clients/edit.blade.php`

Find the marital status `<select id="maritalStatus" name="marital_status">` block. Replace the options with the same set as in 2b (using `$fetchedData->marital_status` for selected).

### 2d. Client detail info (inline edit)

**File:** `resources/views/crm/clients/client_detail_info.blade.php`

Find the marital status select. Ensure options are:

- Never Married, Engaged, Married, **De Facto** (value="De Facto"), Separated, Divorced, Widowed.
- Remove **Others** and **Single** if present.
- Change any "De facto" to **De Facto** for value and label.

---

## 3. Logic that checks marital status

Keep checks so they accept both **De Facto** and **Defacto** (existing DB may still have Defacto). Optionally use only `'De Facto'` once all data is normalized.

### 3a. ClientPersonalDetailsController

**File:** `app/Http/Controllers/CRM/ClientPersonalDetailsController.php`

- Where you have `if ($maritalStatus === 'Defacto') { $maritalStatus = 'De Facto'; }` — keep it so form input still normalizes.
- Where you check for partner (e.g. `in_array($client->marital_status, ['De Facto', 'Defacto'])`) — keep both for safety so existing Defacto/De facto rows still work.

### 3b. PointsService

**File:** `app/Services/PointsService.php`

- The check `in_array($client->marital_status, ['Married', 'De Facto'])` is already correct. No change needed.

### 3c. clients/edit.blade.php (partner section)

**File:** `resources/views/crm/clients/edit.blade.php`

- Keep `in_array($fetchedData->marital_status, ['Married', 'De Facto', 'Defacto'])` so existing Defacto/De facto rows still show the partner section.

---

## 4. Summary checklist

- [ ] Update validation in `ClientsController`, `ClientPersonalDetailsController`, and `API/ClientPortalController`.
- [ ] Update dropdowns in `leads/create.blade.php`, `leads/edit.blade.php`, `clients/edit.blade.php`, `client_detail_info.blade.php`.
- [ ] Keep Defacto in marital-status checks where you read from DB (so old data still works).

After this, both systems use the same **marital_status** field and option set: **Never Married, Engaged, Married, De Facto, Separated, Divorced, Widowed**.
