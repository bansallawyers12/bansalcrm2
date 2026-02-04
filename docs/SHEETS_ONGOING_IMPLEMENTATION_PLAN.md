# Plan: New "Sheets" Tab — Ongoing Worksheet (REVISED)

**Purpose:** Add a new tab named **"Sheets"** (or "Ongoing Sheet") that displays the Ongoing-style worksheet from the provided design: CRM Reference, Client Name, DOB, Payment Received, Institute, Visa Expiry Date, Visa Category, Current Status, with monthly grouping and row highlighting.

**Scope:** Plan only — no code changes applied until you approve.

**Reference:** Worksheet structure from provided image (light blue header, monthly separators Dec-25 / Jan-26, green/yellow/blue row highlighting).

**Status:** DEEP REVIEWED — Updated with verified data sources from bansalcrm2 codebase analysis.

---

## 1. Worksheet Structure (from image)

### 1.1 Column definitions

| Column | Header (exact) | Description | Data source ✓ VERIFIED |
|--------|----------------|-------------|------------------------|
| A | CRM Reference | Unique client ID (e.g. KOMA240235925) | ✓ `admins.client_id` |
| B | Client Name | Full name | ✓ `admins.first_name`, `admins.last_name` |
| C | Date of Birth | DD/MM/YYYY | ✓ `admins.dob` |
| D | Payment Received | Dollar amount or note (e.g. $1,000, "(Deferment)", "VOE Client") | `account_client_receipts.deposit_amount` SUM by client_id + optional notes |
| E | Institute | Institution name (e.g. MEIC/TORRENS, Peach, ASOC) | ✓ **PRIMARY:** `applications.partner_id` → `partners.partner_name`<br>**FALLBACK:** `client_service_takens.edu_college` |
| F | Visa Expiry Date | DD/MM/YYYY, optional suffix e.g. "(HE)" or "BV - A" | ✓ `admins.visaexpiry` (display with `admins.visa_opt` suffix if present) |
| G | Visa Category | e.g. STUDENT, BV - A, ART- BVA, 485 | ✓ `admins.visa_type` + `admins.visa_opt` (concatenated) |
| H | Current Status | Date-prefixed notes (e.g. "04/02: Waiting for the Signed LOF & Payment") | **NEW FIELD REQUIRED** — see §2.2 |

### 1.2 Monthly grouping

- **Separator rows:** One row per month spanning columns D–H with label "Dec-25", "Jan-26", etc.
- **Grouping key:** Need a single date per client row to assign month. Options:
  - **Option A:** Visa expiry date (month of `visaexpiry`) — rows grouped by visa expiry month.
  - **Option B:** Payment month (month of latest payment or key payment).
  - **Option C:** Explicit "sheet period" or "listing month" stored per client (new field).
- **Recommendation:** Option A (visa expiry month) unless business prefers another; document choice before implementation.

### 1.3 Row highlighting rules

- **Green:** e.g. "completed" or "active" (e.g. payment received, no action needed).
- **Yellow:** e.g. "pending" or "attention required" (e.g. waiting for LOF, payment, etc.).
- **Blue:** e.g. other / informational.

Implementation approach:

- **Option A:** New column `row_highlight` on client or on a new reference table: `green` | `yellow` | `blue` | `none`.
- **Option B:** Derive from existing status (e.g. payment status, or a status derived from Current Status text).
- **Option C:** Derive from "Current Status" text (e.g. contains "Waiting" → yellow, contains "payment" / "LOF" → yellow, else green/blue).

Recommendation: **Option A** for clear control; Option C as fallback if no schema change desired initially.

---

## 2. Data sources and gaps ✓ VERIFIED

### 2.1 Already exists in bansalcrm2 (✓ confirmed by code analysis)

- **CRM Reference:** ✓ `admins.client_id` (verified in use throughout codebase)
- **Client Name:** ✓ `admins.first_name`, `admins.last_name` (verified)
- **Date of Birth:** ✓ `admins.dob` (date field; display as DD/MM/YYYY — pattern already used in detail.blade.php line 198)
- **Visa Expiry Date:** ✓ `admins.visaexpiry` (date field; display as DD/MM/YYYY — pattern in detail.blade.php line 222)
  - **Suffix/Option:** ✓ `admins.visa_opt` (e.g. "BV - A", "(HE)") — verified in detail.blade.php lines 211-213
- **Visa Category:** ✓ `admins.visa_type` (e.g. "STUDENT") + `admins.visa_opt` — verified in detail.blade.php line 209
- **Payment data:** ✓ `account_client_receipts` table exists:
  - `deposit_amount` (amount field)
  - `receipt_type` (type: 1 = deposit, 2 = ?, 3 = ?)
  - Aggregation pattern exists in ClientReceiptController line 144: `sum('deposit_amount')` by `client_id` where `receipt_type = 1`
- **Institute (College/Partner):** ✓ **TWO SOURCES:**
  1. **PRIMARY:** `applications` table → `applications.partner_id` → `partners.partner_name` (verified in PartnersController lines 1735-1738)
  2. **FALLBACK:** `client_service_takens.edu_college` (verified in clientServiceTaken.php line 10)
  - **Join logic:** Get latest active application per client (`applications.client_id` + `applications.partner_id` → `partners.partner_name`)

### 2.2 NEW FIELDS REQUIRED (must be added)

#### 2.2.1 Current Status

**Problem:** No existing field for date-prefixed status notes (e.g. "04/02: Waiting for the Signed LOF & Payment").

**Solution:** Add `current_status` field (text, nullable) to store these notes.

**Storage options:**
- **OPTION A (Recommended):** New table `client_ongoing_references` (see §3.1)
- **OPTION B:** Add column directly to `admins` table if this is the only use case

**Reason for Option A:** Keeps ongoing-specific data separate; allows future expansion (e.g. multiple periods, archiving); follows same pattern as migrationmanager2 sheets (ART, EOI use separate reference tables).

#### 2.2.2 Payment Display Note (Optional but recommended)

**Problem:** Need to show text like "(Deferment)", "VOE Client" instead of or alongside payment amount.

**Solution:** Add `payment_display_note` field (string, nullable) to override or supplement the computed payment amount.

**Storage:** Same as Current Status (Option A: `client_ongoing_references` table; Option B: `admins` table).

#### 2.2.3 Row Highlight Color

**Problem:** Need to store which rows are green/yellow/blue for highlighting.

**Solution:** Add `row_highlight` field (enum or string: 'green'|'yellow'|'blue'|'none', nullable, default 'none').

**Storage:** Same as Current Status (Option A: `client_ongoing_references` table; Option B: `admins` table).

#### 2.2.4 Monthly Grouping Date (Clarification needed)

**Current understanding:** Rows grouped by visa expiry month (from `admins.visaexpiry`).

**Alternatives if visa expiry month not correct:**
- Latest payment month (from `account_client_receipts.created_at`)
- Explicit "listing_period" field (YYYY-MM format)

**→ DECISION REQUIRED before implementation:** Confirm which date drives monthly grouping.

---

## 3. Proposed schema ✓ REVISED

### 3.1 RECOMMENDED: New table `client_ongoing_references`

**Rationale:** Follows migrationmanager2 pattern (ART uses `client_art_references`, EOI uses `client_eoi_references`); keeps ongoing-specific data isolated; allows future expansion.

**Purpose:** One row per client for Ongoing sheet; stores status, notes, and display overrides.

#### Schema

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|--------|
| id | bigint UNSIGNED PK AUTO_INCREMENT | No | | Primary key |
| client_id | bigint UNSIGNED | No | | FK → admins.id |
| current_status | text | Yes | NULL | Free text, date-prefixed (e.g. "04/02: Waiting for LOF & Payment") |
| payment_display_note | string(100) | Yes | NULL | Override for payment column (e.g. "Deferment", "VOE Client"); if null, show computed sum |
| row_highlight | enum('green','yellow','blue','none') | No | 'none' | Visual highlight color for row |
| listing_period | char(7) | Yes | NULL | Optional: YYYY-MM format if we support multiple periods per client (e.g. "2025-12") |
| institute_override | string(255) | Yes | NULL | Optional: Manual institute if not from applications/service_takens |
| visa_category_override | string(50) | Yes | NULL | Optional: Manual visa category if not from admins.visa_type |
| notes | text | Yes | NULL | Optional: Internal notes (not shown in sheet) |
| created_by | bigint UNSIGNED | Yes | NULL | FK → admins.id (admin who created) |
| updated_by | bigint UNSIGNED | Yes | NULL | FK → admins.id (admin who last updated) |
| created_at | timestamp | No | CURRENT_TIMESTAMP | Auto |
| updated_at | timestamp | No | CURRENT_TIMESTAMP ON UPDATE | Auto |

#### Indexes

- **Unique:** `client_id` (one row per client for now; remove unique if supporting multiple periods later)
- **Non-unique:** `row_highlight`, `listing_period` (for filtering/sorting)

#### Foreign Keys

- `client_id` → `admins(id)` ON DELETE CASCADE
- `created_by` → `admins(id)` ON DELETE SET NULL
- `updated_by` → `admins(id)` ON DELETE SET NULL

---

### 3.2 ALTERNATIVE: Columns on `admins` table (simpler but less flexible)

If you prefer NOT to create a separate table initially, add these columns to `admins`:

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|--------|
| ongoing_current_status | text | Yes | NULL | Date-prefixed status notes |
| ongoing_payment_note | string(100) | Yes | NULL | e.g. "(Deferment)", "VOE Client" |
| ongoing_row_highlight | enum('green','yellow','blue','none') | Yes | 'none' | Row color |

**Pros:** No new table; simpler schema.  
**Cons:** Pollutes `admins` table with sheet-specific data; harder to extend later (e.g. history, multiple periods); doesn't follow migrationmanager2 pattern.

**→ RECOMMENDATION:** Use Option 3.1 (new table) for consistency and future-proofing.

---

### 3.3 Additional considerations ✓ VERIFIED

- **Institute:** Derived from existing data (no new column needed):
  - Query latest application: `applications.client_id` JOIN `partners.partner_name` via `applications.partner_id`
  - Fallback: `client_service_takens.edu_college` if no applications
  - Override: Use `institute_override` from `client_ongoing_references` if present (optional)

- **Visa Category:** Derived from existing `admins` columns (no new column needed):
  - Concatenate `admins.visa_type` + `admins.visa_opt` (e.g. "STUDENT" + "BV - A" → "STUDENT BV - A")
  - Override: Use `visa_category_override` from `client_ongoing_references` if present (optional)

- **Payment Received:** Computed at query time (no new column needed):
  - SUM `account_client_receipts.deposit_amount` WHERE `client_id = X` AND `receipt_type = 1`
  - Override: Show `payment_display_note` if present (e.g. "(Deferment)", "VOE Client")

- **Visa Expiry Suffix:** Derived from existing `admins.visa_opt` (no new column needed):
  - Display: `admins.visaexpiry` (DD/MM/YYYY) + `admins.visa_opt` if present (e.g. "30/08/2026(HE)")

**→ RESULT:** Only NEW fields needed are: `current_status`, `payment_display_note`, `row_highlight` (+ optional `listing_period`, overrides, notes).

---

## 4. Routes and navigation ✓ DETAILED

### 4.1 Routes — Add to `routes/clients.php` (or `routes/web.php`)

**Location:** `routes/clients.php` (if it exists) or `routes/web.php`

**Middleware:** `auth:admin` (add to route group if not already present)

```php
// routes/clients.php (or web.php within auth:admin middleware group)

// Ongoing Sheet Routes
Route::get('/clients/sheets/ongoing', [
    \App\Http\Controllers\Admin\OngoingSheetController::class, 
    'index'
])->name('clients.sheets.ongoing');

Route::get('/clients/sheets/ongoing/insights', [
    \App\Http\Controllers\Admin\OngoingSheetController::class, 
    'insights'
])->name('clients.sheets.ongoing.insights');

// Optional: CRUD operations for ongoing references
Route::post('/clients/sheets/ongoing/{clientId}/update', [
    \App\Http\Controllers\Admin\OngoingSheetController::class, 
    'updateReference'
])->name('clients.sheets.ongoing.update');
```

### 4.2 Navigation — Where to add the link

**IMPORTANT:** bansalcrm2 does NOT have a separate CRM client detail header like migrationmanager2. The main navigation is in:
- **`resources/views/Elements/Admin/header.blade.php`** (global admin header)
- **OR** `resources/views/layouts/admin.blade.php` (sidebar navigation if present)

**Options for adding the link:**

#### Option A: Add to main navigation dropdown (Clients section)

Find the Clients icon dropdown in the header and add:

```blade
{{-- In the Clients dropdown (search for "icon-dropdown" with Clients icon) --}}
<div class="icon-dropdown js-dropdown">
    <a href="{{route('clients.index')}}" class="icon-btn" title="Clients">
        <i class="fas fa-users"></i>
    </a>
    <div class="icon-dropdown-menu">
        <a class="dropdown-item" href="{{route('clients.index')}}">
            <i class="fas fa-list mr-2"></i> Client List
        </a>
        <a class="dropdown-item" href="{{route('clients.clientsmatterslist')}}">
            <i class="fas fa-folder-open mr-2"></i> Matter List
        </a>
        {{-- NEW: Add Ongoing Sheet --}}
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{route('clients.sheets.ongoing')}}">
            <i class="fas fa-clipboard-list mr-2"></i> Ongoing Sheet
        </a>
        {{-- END NEW --}}
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{route('leads.index')}}">
            <i class="fas fa-list-alt mr-2"></i> Lead List
        </a>
        <a class="dropdown-item" href="{{route('leads.create')}}">
            <i class="fas fa-plus-circle mr-2"></i> Add Lead
        </a>
    </div>
</div>
```

#### Option B: Add as separate icon button (if you want a dedicated "Sheets" icon)

```blade
{{-- Add new Sheets icon button in topbar-left --}}
<div class="icon-dropdown js-dropdown">
    <a href="{{route('clients.sheets.ongoing')}}" class="icon-btn" title="Sheets">
        <i class="fas fa-clipboard-list"></i>
    </a>
    {{-- Optional: Dropdown for multiple sheets in future --}}
    <div class="icon-dropdown-menu">
        <a class="dropdown-item" href="{{route('clients.sheets.ongoing')}}">
            <i class="fas fa-clipboard-list mr-2"></i> Ongoing Sheet
        </a>
        {{-- Future: Add ART, EOI, TR sheets here --}}
    </div>
</div>
```

**→ RECOMMENDATION:** Option A (add to Clients dropdown) for now; migrate to Option B if multiple sheets are added later.

---

## 5. Controller behaviour ✓ DETAILED

### 5.1 Controller location and structure

- **File:** `app/Http/Controllers/Admin/OngoingSheetController.php` (or `app/Http/Controllers/CRM/OngoingSheetController.php` if following migrationmanager2 pattern)
- **Namespace:** `App\Http\Controllers\Admin` (or `\CRM`)
- **Extends:** `Controller`
- **Traits:** None required initially (no authorization trait like migrationmanager2's `ClientAuthorization` exists in bansalcrm2)
- **Middleware:** `auth:admin` in constructor

### 5.2 `index()` method — List view

```php
public function index(Request $request)
{
    // 1. Authorization (optional - add if module permissions exist)
    // if (!$this->hasModuleAccess('20')) { abort(403); }
    
    // 2. Pagination
    $perPage = (int) $request->get('per_page', 50);
    $allowedPerPage = [10, 25, 50, 100, 200];
    if (!in_array($perPage, $allowedPerPage, true)) {
        $perPage = 50;
    }
    
    // 3. Base query
    $query = $this->buildBaseQuery($request);
    
    // 4. Apply filters
    $query = $this->applyFilters($query, $request);
    
    // 5. Apply sorting (by visa expiry month for grouping)
    $query = $this->applySorting($query, $request);
    
    // 6. Get rows
    $rows = $query->paginate($perPage)->appends($request->except('page'));
    
    // 7. Compute monthly grouping
    $rows->getCollection()->transform(function ($row) {
        $row->visa_expiry_month = $row->visaexpiry 
            ? \Carbon\Carbon::parse($row->visaexpiry)->format('M-y') 
            : 'No Expiry';
        return $row;
    });
    
    // 8. Dropdown data for filters
    $offices = \App\Models\Branch::orderBy('office_name')->get(['id', 'office_name']);
    $activeFilterCount = $this->countActiveFilters($request);
    
    // 9. Return view
    return view('Admin.sheets.ongoing', compact(
        'rows', 
        'perPage', 
        'activeFilterCount', 
        'offices'
    ));
}
```

### 5.3 `buildBaseQuery()` protected method

```php
protected function buildBaseQuery(Request $request)
{
    $query = \App\Models\Admin::query()
        ->select([
            'admins.id as client_id',
            'admins.client_id as crm_ref',
            'admins.first_name',
            'admins.last_name',
            'admins.dob',
            'admins.visaexpiry',
            'admins.visa_type',
            'admins.visa_opt',
            'admins.office_id',
            // Ongoing reference data (LEFT JOIN)
            'ongoing.current_status',
            'ongoing.payment_display_note',
            'ongoing.row_highlight',
            'ongoing.institute_override',
            'ongoing.visa_category_override',
            // Payment sum (subquery or JOIN)
            DB::raw('(SELECT COALESCE(SUM(deposit_amount), 0) 
                     FROM account_client_receipts 
                     WHERE client_id = admins.id 
                     AND receipt_type = 1) as total_payment'),
            // Institute from latest application (subquery or JOIN)
            DB::raw('(SELECT partners.partner_name 
                     FROM applications 
                     LEFT JOIN partners ON applications.partner_id = partners.id 
                     WHERE applications.client_id = admins.id 
                     ORDER BY applications.id DESC 
                     LIMIT 1) as partner_name'),
            // Fallback institute from service_takens
            DB::raw('(SELECT edu_college 
                     FROM client_service_takens 
                     WHERE client_id = admins.id 
                     ORDER BY id DESC 
                     LIMIT 1) as service_college')
        ])
        ->leftJoin('client_ongoing_references as ongoing', 'ongoing.client_id', '=', 'admins.id')
        ->where('admins.role', 7) // Clients only
        ->where('admins.is_archived', 0)
        ->whereNull('admins.is_deleted');
    
    return $query;
}
```

### 5.4 `applyFilters()` protected method

```php
protected function applyFilters($query, Request $request)
{
    // Office filter
    if ($request->filled('office')) {
        $offices = is_array($request->input('office')) 
            ? $request->input('office') 
            : [$request->input('office')];
        $query->whereIn('admins.office_id', $offices);
    }
    
    // Visa expiry date range
    if ($request->filled('visa_expiry_from')) {
        $query->whereDate('admins.visaexpiry', '>=', 
            \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('visa_expiry_from')));
    }
    if ($request->filled('visa_expiry_to')) {
        $query->whereDate('admins.visaexpiry', '<=', 
            \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('visa_expiry_to')));
    }
    
    // Row highlight filter
    if ($request->filled('highlight')) {
        $query->where('ongoing.row_highlight', $request->input('highlight'));
    }
    
    // Search (name, CRM ref)
    if ($request->filled('search')) {
        $search = '%' . strtolower($request->input('search')) . '%';
        $query->where(function ($q) use ($search) {
            $q->whereRaw('LOWER(admins.first_name) LIKE ?', [$search])
              ->orWhereRaw('LOWER(admins.last_name) LIKE ?', [$search])
              ->orWhereRaw('LOWER(admins.client_id) LIKE ?', [$search]);
        });
    }
    
    return $query;
}
```

### 5.5 `applySorting()` protected method

```php
protected function applySorting($query, Request $request)
{
    // Primary sort: by visa expiry month for grouping
    $query->orderByRaw('EXTRACT(YEAR FROM admins.visaexpiry) DESC, 
                        EXTRACT(MONTH FROM admins.visaexpiry) DESC');
    
    // Secondary sort: by CRM ref or name within month
    $query->orderBy('admins.client_id', 'asc');
    
    return $query;
}
```

### 5.6 `insights()` method (optional)

```php
public function insights(Request $request)
{
    $baseQuery = $this->buildBaseQuery($request);
    $baseQuery = $this->applyFilters($baseQuery, $request);
    $allRecords = $baseQuery->get();
    
    $insights = [
        'total_clients' => $allRecords->count(),
        'by_highlight' => $allRecords->groupBy('row_highlight')->map->count(),
        'by_visa_expiry_month' => $allRecords->groupBy('visa_expiry_month')->map->count(),
        'total_payments' => $allRecords->sum('total_payment'),
    ];
    
    $activeFilterCount = $this->countActiveFilters($request);
    
    return view('Admin.sheets.ongoing-insights', compact('insights', 'activeFilterCount'));
}
```

### 5.7 `countActiveFilters()` helper

```php
protected function countActiveFilters(Request $request)
{
    $filters = ['office', 'visa_expiry_from', 'visa_expiry_to', 'highlight', 'search'];
    $count = 0;
    foreach ($filters as $filter) {
        if ($request->filled($filter)) {
            $count++;
        }
    }
    return $count;
}
```

---

## 6. View structure ✓ DETAILED

### 6.1 View file location

**File:** `resources/views/Admin/sheets/ongoing.blade.php`  
**Layout:** `@extends('layouts.admin')` (existing bansalcrm2 admin layout)

### 6.2 List view structure

```blade
@extends('layouts.admin')
@section('title', 'Ongoing Sheet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
<style>
    /* Header row - light blue */
    .ongoing-sheet-header {
        background: linear-gradient(135deg, #cfe2ff 0%, #b8daff 100%);
        font-weight: 600;
        text-align: center;
        border: 1px solid #9ec5fe;
    }
    
    /* Month separator rows */
    .month-separator {
        background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
        font-weight: 700;
        text-align: center;
        font-size: 1.1rem;
        border: 2px solid #ffc107;
    }
    
    /* Row highlighting */
    tr.row-green {
        background-color: #d1f2eb !important;
    }
    tr.row-yellow {
        background-color: #fff3cd !important;
    }
    tr.row-blue {
        background-color: #cfe2ff !important;
    }
    tr.row-none {
        background-color: #ffffff !important;
    }
    
    /* Hover effect */
    tbody tr:not(.month-separator):hover {
        opacity: 0.85;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            
            {{-- Top Bar: Title + Back Button --}}
            <div class="card-header">
                <h4><i class="fas fa-clipboard-list"></i> Ongoing Sheet</h4>
                <div class="card-header-action">
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Clients
                    </a>
                </div>
            </div>
            
            {{-- Filter Bar --}}
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button class="btn btn-primary" data-toggle="collapse" data-target="#filterPanel">
                            <i class="fas fa-filter"></i> Filters
                            @if($activeFilterCount > 0)
                                <span class="badge badge-light">{{ $activeFilterCount }}</span>
                            @endif
                        </button>
                        
                        @if($activeFilterCount > 0)
                            <a href="{{ route('clients.sheets.ongoing') }}" class="btn btn-secondary">
                                Clear Filters
                            </a>
                        @endif
                        
                        <select name="per_page" class="form-control" style="width: auto;" 
                                onchange="window.location.href='{{ route('clients.sheets.ongoing') }}?per_page=' + this.value;">
                            @foreach([10, 25, 50, 100, 200] as $option)
                                <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
                                    {{ $option }} per page
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- Filter Panel (Collapsible) --}}
                    <div class="collapse {{ $activeFilterCount > 0 ? 'show' : '' }}" id="filterPanel">
                        <form method="get" action="{{ route('clients.sheets.ongoing') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Office</label>
                                    <select name="office[]" class="form-control select2" multiple>
                                        @foreach($offices as $office)
                                            <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <label>Visa Expiry From</label>
                                    <input type="text" name="visa_expiry_from" class="form-control datepicker" 
                                           placeholder="DD/MM/YYYY" value="{{ request('visa_expiry_from') }}">
                                </div>
                                
                                <div class="col-md-3">
                                    <label>Visa Expiry To</label>
                                    <input type="text" name="visa_expiry_to" class="form-control datepicker" 
                                           placeholder="DD/MM/YYYY" value="{{ request('visa_expiry_to') }}">
                                </div>
                                
                                <div class="col-md-3">
                                    <label>Row Highlight</label>
                                    <select name="highlight" class="form-control">
                                        <option value="">All</option>
                                        <option value="green">Green</option>
                                        <option value="yellow">Yellow</option>
                                        <option value="blue">Blue</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-12 mt-3">
                                    <label>Search (Name, CRM Ref)</label>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search..." value="{{ request('search') }}">
                                </div>
                                
                                <div class="col-md-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                    <a href="{{ route('clients.sheets.ongoing') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            {{-- Table --}}
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr class="ongoing-sheet-header">
                                    <th>CRM Reference</th>
                                    <th>Client Name</th>
                                    <th>Date of Birth</th>
                                    <th>Payment Received</th>
                                    <th>Institute</th>
                                    <th>Visa Expiry Date</th>
                                    <th>Visa Category</th>
                                    <th>Current Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($rows->isEmpty())
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                                            <p class="mb-0">No ongoing records found.</p>
                                        </td>
                                    </tr>
                                @else
                                    <?php $currentMonth = null; ?>
                                    @foreach($rows as $row)
                                        {{-- Month separator row --}}
                                        @if($currentMonth !== $row->visa_expiry_month)
                                            <?php $currentMonth = $row->visa_expiry_month; ?>
                                            <tr class="month-separator">
                                                <td colspan="8">{{ $currentMonth }}</td>
                                            </tr>
                                        @endif
                                        
                                        {{-- Data row --}}
                                        <tr class="row-{{ $row->row_highlight ?? 'none' }}" 
                                            onclick="window.location.href='{{ route('clients.detail', ['id' => base64_encode(convert_uuencode($row->client_id))]) }}'">
                                            <td>{{ $row->crm_ref }}</td>
                                            <td>{{ $row->first_name }} {{ $row->last_name }}</td>
                                            <td>{{ $row->dob ? \Carbon\Carbon::parse($row->dob)->format('d/m/Y') : '—' }}</td>
                                            <td>
                                                @if($row->payment_display_note)
                                                    {{ $row->payment_display_note }}
                                                @else
                                                    ${{ number_format($row->total_payment, 2) }}
                                                @endif
                                            </td>
                                            <td>{{ $row->institute_override ?? $row->partner_name ?? $row->service_college ?? '—' }}</td>
                                            <td>
                                                @if($row->visaexpiry)
                                                    {{ \Carbon\Carbon::parse($row->visaexpiry)->format('d/m/Y') }}
                                                    @if($row->visa_opt)
                                                        <span class="text-muted">({{ $row->visa_opt }})</span>
                                                    @endif
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>{{ $row->visa_category_override ?? trim(($row->visa_type ?? '') . ' ' . ($row->visa_opt ?? '')) }}</td>
                                            <td style="max-width: 300px; white-space: pre-wrap;">{{ $row->current_status ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    <div class="card-footer text-right">
                        {{ $rows->links() }}
                    </div>
                </div>
            </div>
            
        </div>
    </section>
</div>
@endsection
```

### 6.3 Insights view (optional)

**File:** `resources/views/Admin/sheets/ongoing-insights.blade.php`

Similar to list view but shows:
- Insight cards: Total clients, by highlight color, by visa expiry month, total payments
- Optional bar chart for visa expiry months
- Optional pie chart for row_highlight distribution

---

## 7. Implementation order ✓ COMPREHENSIVE CHECKLIST

### Phase 1: Database Schema (30 min)

- [ ] **Create migration:** `database/migrations/YYYY_MM_DD_HHMMSS_create_client_ongoing_references_table.php`
  - [ ] Table: `client_ongoing_references` with columns from §3.1
  - [ ] Unique index on `client_id`
  - [ ] Foreign keys: `client_id`, `created_by`, `updated_by`
  - [ ] Add `down()` method for rollback
- [ ] **Run migration:** `php artisan migrate`
- [ ] **Verify:** Check table exists in database

### Phase 2: Model (15 min)

- [ ] **Create model:** `app/Models/ClientOngoingReference.php`
  - [ ] `protected $table = 'client_ongoing_references';`
  - [ ] `protected $fillable` = all columns except id, timestamps
  - [ ] `protected $casts` = timestamps
  - [ ] Relationship: `belongsTo(Admin::class, 'client_id')`
  - [ ] Relationships: `creator()`, `updater()`
  - [ ] Optional: Boot method for auto-setting `created_by`, `updated_by`

### Phase 3: Routes (5 min)

- [ ] **Add routes** to `routes/clients.php` (or `routes/web.php`):
  - [ ] `GET /clients/sheets/ongoing` → `OngoingSheetController@index`
  - [ ] Optional: `GET /clients/sheets/ongoing/insights` → `insights`
- [ ] **Verify:** `php artisan route:list | grep ongoing`

### Phase 4: Controller (45 min)

- [ ] **Create controller:** `app/Http/Controllers/Admin/OngoingSheetController.php`
  - [ ] Constructor with `auth:admin` middleware
  - [ ] `index()` method (see §5.2)
  - [ ] `buildBaseQuery()` method (see §5.3) — includes LEFT JOIN to ongoing references, payment subquery, institute subquery
  - [ ] `applyFilters()` method (see §5.4) — office, visa expiry range, highlight, search
  - [ ] `applySorting()` method (see §5.5) — by visa expiry month, then CRM ref
  - [ ] `countActiveFilters()` helper
  - [ ] Optional: `insights()` method (see §5.6)
  - [ ] Optional: `updateReference()` method for editing ongoing data

### Phase 5: Views (60 min)

- [ ] **Create directory:** `resources/views/Admin/sheets/` (if not exists)
- [ ] **Create list view:** `resources/views/Admin/sheets/ongoing.blade.php`
  - [ ] Extend `layouts.admin`
  - [ ] Top bar: Title, Back button
  - [ ] Filter bar: Filters button, active count badge, clear link, per-page selector
  - [ ] Filter panel: Office, visa expiry from/to, highlight, search
  - [ ] Table: Header row (light blue), month separator rows, data rows with highlight classes
  - [ ] CSS: `.ongoing-sheet-header`, `.month-separator`, `.row-green`, `.row-yellow`, `.row-blue`
  - [ ] Empty state: "No ongoing records found"
  - [ ] Pagination
- [ ] **Optional:** Create insights view `ongoing-insights.blade.php`

### Phase 6: Navigation (10 min)

- [ ] **Locate header file:** `resources/views/Elements/Admin/header.blade.php`
- [ ] **Add link** to Clients dropdown (Option A from §4.2):
  - [ ] Icon: `fa-clipboard-list`
  - [ ] Label: "Ongoing Sheet"
  - [ ] Route: `clients.sheets.ongoing`
  - [ ] Add divider before/after if needed
- [ ] **Verify:** Link appears in navigation and routes to correct page

### Phase 7: Testing (30 min)

- [ ] **Seed test data** (optional):
  - [ ] Create 5-10 `client_ongoing_references` rows with varied data
  - [ ] Mix of green/yellow/blue highlights
  - [ ] Varied visa expiry dates across multiple months
- [ ] **Manual testing:**
  - [ ] Navigate to Ongoing Sheet from header
  - [ ] Verify all 8 columns display correctly
  - [ ] Verify monthly separators appear (Dec-25, Jan-26, etc.)
  - [ ] Verify row highlighting (green/yellow/blue)
  - [ ] Test filters: office, visa expiry range, highlight, search
  - [ ] Test per-page selector
  - [ ] Test pagination
  - [ ] Verify clicking row navigates to client detail
- [ ] **Edge cases:**
  - [ ] Client with no visa expiry (should show "No Expiry" month)
  - [ ] Client with no payment (should show $0.00)
  - [ ] Client with payment_display_note (should show note instead of amount)
  - [ ] Client with no application/institute (should show "—")
  - [ ] Empty state (no clients match filters)

### Phase 8: Optional Enhancements (As needed)

- [ ] **Edit functionality:**
  - [ ] Modal for editing `current_status`, `row_highlight`, `payment_display_note`
  - [ ] AJAX endpoint: `POST /clients/sheets/ongoing/{clientId}/update`
  - [ ] JavaScript: Open modal, submit via AJAX, refresh row
- [ ] **Insights view:**
  - [ ] Insight cards: Total clients, by highlight, by month, total payments
  - [ ] Charts: Visa expiry months (bar), Highlight distribution (pie)
- [ ] **Export:**
  - [ ] Export to Excel/CSV button
  - [ ] Controller method: Generate Excel from current query + filters
- [ ] **Import:**
  - [ ] Import button to bulk update ongoing references from Excel

### Phase 9: Documentation (15 min)

- [ ] **User guide:** Create `docs/ONGOING_SHEET_USER_GUIDE.md`:
  - [ ] How to navigate to Ongoing Sheet
  - [ ] Column descriptions
  - [ ] How to use filters
  - [ ] How to interpret row highlighting
  - [ ] How to edit current status (if implemented)
- [ ] **Developer notes:** Update this plan with:
  - [ ] Final schema decisions
  - [ ] Any deviations from plan
  - [ ] Known issues or limitations

---

**TOTAL ESTIMATED TIME:** ~3-4 hours for core implementation (Phases 1-7)  
**WITH OPTIONAL:** +2-3 hours for enhancements (Phase 8)

---

## 8. Open points ✓ CRITICAL DECISIONS NEEDED BEFORE IMPLEMENTATION

### 8.1 HIGH PRIORITY (Must decide before Phase 1)

1. **Monthly grouping logic:**  
   **Question:** Which date drives the month separator rows?  
   **Options:**
   - **A)** Visa expiry date (`admins.visaexpiry`) — RECOMMENDED (as assumed in plan)
   - **B)** Latest payment date (`account_client_receipts.created_at`)
   - **C)** Explicit "listing period" field (new column: `listing_period` YYYY-MM)
   
   **→ DECISION REQUIRED:** Confirm Option A or specify alternative.

2. **Row highlighting logic:**  
   **Question:** How are green/yellow/blue colors determined?  
   **Options:**
   - **A)** Manual assignment (staff sets color) — RECOMMENDED
   - **B)** Derived from payment status (e.g. $0 = yellow, >$0 = green)
   - **C)** Derived from Current Status text (e.g. contains "Waiting" = yellow)
   - **D)** Combination (auto-set initially, manual override allowed)
   
   **→ DECISION REQUIRED:** Confirm Option A or specify alternative.

3. **Schema choice:**  
   **Question:** New table or add columns to `admins`?  
   **Options:**
   - **A)** New table `client_ongoing_references` — RECOMMENDED (follows migrationmanager2 pattern; cleaner separation)
   - **B)** Add columns to `admins` table (simpler but less flexible)
   
   **→ DECISION REQUIRED:** Confirm Option A or specify alternative.

### 8.2 MEDIUM PRIORITY (Can decide during implementation)

4. **Navigation label:**  
   **Question:** What exact text appears in the navigation menu?  
   **Options:** "Ongoing Sheet" | "Sheets" | "Ongoing" | "Client Sheet"  
   **→ RECOMMENDATION:** "Ongoing Sheet" (clear and descriptive)

5. **Editable fields:**  
   **Question:** Can staff edit Current Status and Row Highlight directly from the sheet?  
   **Options:**
   - **A)** Yes, via modal (click row → edit modal pops up)
   - **B)** Yes, via inline edit (click cell → input appears)
   - **C)** No, edit only from client detail page
   
   **→ RECOMMENDATION:** Option C for Phase 1 (simplest); add Option A in Phase 8 (enhancement).

6. **Empty payment handling:**  
   **Question:** How to display payment when `total_payment = 0` and no `payment_display_note`?  
   **Options:** "$0.00" | "—" | "No Payment" | "(Pending)"  
   **→ RECOMMENDATION:** "$0.00" (factual and consistent)

### 8.3 LOW PRIORITY (Nice to have; can defer)

7. **Institute fallback priority:**  
   **Question:** If client has both application (partner_name) and service_takens (edu_college), which takes precedence?  
   **→ RECOMMENDATION:** Latest application partner_name > service_takens edu_college > institute_override (if present)

8. **Insights view:**  
   **Question:** Should insights view be built in Phase 1?  
   **→ RECOMMENDATION:** Defer to Phase 8 (optional enhancement); focus on list view first.

9. **Pagination default:**  
   **Question:** How many rows per page by default?  
   **→ RECOMMENDATION:** 50 (as coded in §5.2; matches typical usage)

10. **Client filtering:**  
    **Question:** Show only clients with active visas? Or all clients (role=7)?  
    **→ RECOMMENDATION:** All clients; add optional "Active Visa Only" filter if needed later.

---

## 9. Summary ✓ DEEP REVIEWED & VERIFIED

| Category | Detail | Status |
|----------|--------|--------|
| **Purpose** | Display Ongoing worksheet: CRM Ref, Name, DOB, Payment, Institute, Visa Expiry, Visa Category, Current Status | ✓ Defined |
| **Data sources** | All columns mapped to existing fields EXCEPT `current_status`, `payment_display_note`, `row_highlight` (new fields required) | ✓ Verified |
| **Schema** | New table `client_ongoing_references` (minimal: 3 new fields + metadata) OR add 3 columns to `admins` | ⚠️ Choice needed |
| **Monthly grouping** | By visa expiry month (or confirm alternative) | ⚠️ Confirm |
| **Row highlighting** | Green/yellow/blue stored in `row_highlight` field (manual or derived) | ⚠️ Logic needed |
| **Navigation** | Add "Ongoing Sheet" link to Clients dropdown in `resources/views/Elements/Admin/header.blade.php` | ✓ Located |
| **Routes** | `GET /clients/sheets/ongoing` → `OngoingSheetController@index` | ✓ Defined |
| **Controller** | `OngoingSheetController` with base query (LEFT JOIN ongoing refs, payment SUM, institute subquery), filters, sorting | ✓ Coded |
| **View** | `Admin/sheets/ongoing.blade.php` with light blue header, month separators, row colors, filters, pagination | ✓ Templated |
| **Implementation time** | Core: ~3-4 hours (Phases 1-7) | ✓ Estimated |
| **Target codebase** | **bansalcrm2** (confirmed — data sources verified in this codebase) | ✓ Confirmed |

---

## 10. Key differences from initial plan (updated after deep review)

| Item | Initial Plan (before review) | After Deep Review (updated) |
|------|------------------------------|----------------------------|
| Institute source | "TBD — see §2" | ✓ **VERIFIED:** `applications.partner_id` → `partners.partner_name` + fallback to `client_service_takens.edu_college` |
| Visa Category source | "TBD — see §2" | ✓ **VERIFIED:** `admins.visa_type` + `admins.visa_opt` (concatenate or show separately) |
| Visa Expiry suffix | "Optional suffix field TBD" | ✓ **VERIFIED:** `admins.visa_opt` (already exists; e.g. "(HE)", "BV - A") |
| Payment data | "TBD" | ✓ **VERIFIED:** `account_client_receipts.deposit_amount` SUM WHERE `receipt_type = 1` |
| Navigation location | "CRM header (migrationmanager2 pattern)" | ✓ **CORRECTED:** `resources/views/Elements/Admin/header.blade.php` in Clients dropdown (bansalcrm2 structure differs) |
| Layout | "layouts.crm_client_detail" | ✓ **CORRECTED:** `layouts.admin` (bansalcrm2 uses admin layout, not separate CRM layout) |
| ClientMatter model | "Use client_matters table" | ✓ **CORRECTED:** ClientMatter model does NOT exist in bansalcrm2 (checked in EmailUploadV2Controller) |

---

## 11. Final recommendation

**✅ PLAN IS READY FOR IMPLEMENTATION** once the following 3 critical decisions are confirmed:

1. **Monthly grouping:** Visa expiry month (Option A) — Confirm or specify alternative
2. **Row highlight logic:** Manual assignment (Option A) — Confirm or specify alternative  
3. **Schema:** New table `client_ongoing_references` (Option A) — Confirm or specify alternative

**All data sources verified.** All code templates provided. Implementation checklist complete.

**→ NEXT STEP:** User confirms the 3 decisions above, then proceed with Phase 1 (Database Schema).
