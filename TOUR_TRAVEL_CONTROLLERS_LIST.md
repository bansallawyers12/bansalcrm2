# Tour/Travel Legacy Code - Controllers List

This document lists all controllers that contain tour/travel related legacy code.

## Controllers with Tour/Travel Code

### 1. **app/Http/Controllers/Admin/ClientsController.php** ✅ VERIFIED
   - **Line 2725**: Tourist Visa check (`noe_id == 4`) in appointment validation logic
     - Comment: `//Tourist Visa`
     - Used for checking appointment conflicts for Tourist Visa appointments
     - Code: `else if( isset($obj->noe_id) && ( $obj->noe_id == 4 ) ) { //Tourist Visa`
   - **Line 4201**: `traveled` field assignment in `saveonlineform()` method
     - Method starts at line 4140
     - Saves travel history information from online forms
     - Code: `$obj->traveled = $requestData['traveled'];`
   - **Line 4704**: `traveled` field in `merge_records()` method
     - Method starts at line 4717
     - Includes traveled field when merging client records
     - Code: `'traveled' => $clientval->traveled,`

### 2. **app/Http/Controllers/Admin/AppointmentsController.php** ❌ NOT FOUND
   - **Status**: This file does not exist in the codebase
   - The document claims line 217 has Tourist Visa check, but the controller file is missing

### 3. **app/Http/Controllers/Admin/AdminController.php** ❌ NOT FOUND
   - **Status**: `appointmentsTourist()` method does not exist
   - Line 1911-1913 contains `gensettingsupdate()` method, not `appointmentsTourist()`
   - No appointment-related methods found in AdminController

### 4. **app/Http/Controllers/API/AppointmentController.php** ❌ NOT FOUND
   - **Status**: This file does not exist in the codebase
   - No API controllers found that handle appointment nature of enquiry mapping

## Related Routes

### routes/web.php
- **appointments-tourist route**: ❌ NOT FOUND
  - No route found for `/admin/appointments-tourist`
  - No commented out route found for appointments-tourist
- **Line 331-333**: ✅ VERIFIED - Routes for online forms that handle `traveled` field:
  - `/admin/saveonlineprimaryform` → `ClientsController@saveonlineform`
  - `/admin/saveonlinesecform` → `ClientsController@saveonlineform`
  - `/admin/saveonlinechildform` → `ClientsController@saveonlineform`

## Related Views

### resources/views/Admin/clients/detail.blade.php ✅ VERIFIED
- Contains forms with `traveled` field for:
  - Primary applicant (line 2952) ✅
  - Secondary applicant (line 3164) ✅
  - Child applicant (line 3376) ✅
- Field label: "Have you travelled to any other country including Australia in last 10 years"
- Field description: "If Yes Mention Visa Subclass, Country Name, Departure Date, Arrival Date, type of visa"

## Database Fields

- **`traveled`** field in `online_forms` table (handled via `OnlineForm` model)
- **`noe_id`** field in `appointments` table (value 4 = Tourist Visa)

## Summary

**Total Controllers with Tour/Travel Code: 1** (Verified)
1. ✅ ClientsController.php - Contains Tourist Visa check and traveled field handling

**Controllers Listed But Not Found:**
1. ❌ AppointmentsController.php - File does not exist
2. ❌ AdminController.php - appointmentsTourist() method does not exist
3. ❌ API/AppointmentController.php - File does not exist

**Key Legacy Features (Verified):**
- ✅ Tourist Visa appointment type handling (noe_id = 4) in ClientsController
- ✅ Travel history tracking (`traveled` field) in ClientsController and views
- ❌ Tourist-specific appointment calendar view - Route and method not found

## Verification Notes

- Line numbers in the original document were incorrect for some references
- Some controllers and methods mentioned in the document do not exist in the codebase
- Only ClientsController.php contains verified tour/travel related code

