# Tour/Travel Legacy Code - Controllers List

This document lists all controllers that contain tour/travel related legacy code.

## Controllers with Tour/Travel Code

### 1. **app/Http/Controllers/Admin/ClientsController.php**
   - **Line 2733**: Tourist Visa check (`noe_id == 4`) in appointment validation logic
     - Comment: `//Tourist Visa`
     - Used for checking appointment conflicts for Tourist Visa appointments
   - **Line 4362**: `traveled` field assignment in `saveonlineform()` method
     - Saves travel history information from online forms
   - **Line 4865**: `traveled` field in `merge_records()` method
     - Includes traveled field when merging client records

### 2. **app/Http/Controllers/Admin/AppointmentsController.php**
   - **Line 217**: Tourist Visa check (`noe_id == 4`) in appointment validation logic
     - Comment: `//Tourist Visa`
     - Used for checking appointment conflicts for Tourist Visa appointments

### 3. **app/Http/Controllers/Admin/AdminController.php**
   - **Line 1911-1913**: `appointmentsTourist()` method
     - Method name: `appointmentsTourist`
     - Sets type to 'Tourist' and returns appointment calendar view
     - Route: `/admin/appointments-tourist`

### 4. **app/Http/Controllers/API/AppointmentController.php**
   - **Line 70**: Tourist Visa in nature of enquiry mapping
     - Maps `noe_id` 4 to 'Tourist Visa' string
     - Used in API responses for appointment nature of enquiry

## Related Routes

### routes/web.php
- **Line 135-136**: Commented out route for appointments-tourist
- **Line 140**: Active route: `Route::get('/appointments-tourist', 'Admin\AdminController@appointmentsTourist')`
- **Line 346-348**: Routes for online forms that handle `traveled` field:
  - `/admin/saveonlineprimaryform` → `ClientsController@saveonlineform`
  - `/admin/saveonlinesecform` → `ClientsController@saveonlineform`
  - `/admin/saveonlinechildform` → `ClientsController@saveonlineform`

## Related Views

### resources/views/Admin/clients/detail.blade.php
- Contains forms with `traveled` field for:
  - Primary applicant (line ~2952)
  - Secondary applicant (line ~3164)
  - Child applicant (line ~3376)
- Field label: "Have you travelled to any other country including Australia in last 10 years"
- Field description: "If Yes Mention Visa Subclass, Country Name, Departure Date, Arrival Date, type of visa"

## Database Fields

- **`traveled`** field in `online_forms` table (handled via `OnlineForm` model)
- **`noe_id`** field in `appointments` table (value 4 = Tourist Visa)

## Summary

**Total Controllers with Tour/Travel Code: 4**
1. ClientsController.php
2. AppointmentsController.php
3. AdminController.php
4. API/AppointmentController.php

**Key Legacy Features:**
- Tourist Visa appointment type handling (noe_id = 4)
- Travel history tracking (`traveled` field)
- Tourist-specific appointment calendar view

