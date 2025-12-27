# Unused Code References - Removed Tables

This document lists all code references to tables that have been removed from the database. **DO NOT DELETE YET** - this is a reference list for cleanup.

## Tables Removed

### Backup Tables
- `admins_bk_10feb2024`
- `admins_bk_25jan2024`

### Frontend/Website Tables
- `blog_categories`
- `blogs`
- `cms_pages`
- `testimonials`
- `sliders`
- `popups`
- `reviews`
- `wishlists`
- `free_downloads`
- `home_contents`
- `faqs`
- `seo_pages`
- `navmenus`
- `theme_options`
- `banners`
- `our_offices`
- `our_services`
- `why_chooseuses`

### Legacy Tour/Travel Tables
- `destinations`
- `hotels`
- `airports`
- `packages`

### Appointment System Tables
- `appointments`
- `appointment_logs`
- `book_services`
- `book_service_disable_slots`
- `book_service_slot_per_persons`
- `tbl_paid_appointment_payment`

### Other Unused Tables
- `coupons`
- `offers`
- `omrs`
- `media_images`

### BKK Backup Tables
- `applications_bkk_19dec2024`
- `appointments_bkk_16apr2024`
- `book_services_bkk_9oct2024`
- `checkin_logs_bkk_13mar2024`
- `emails_bkk_2apr2025`
- `enquiries_bkk_22oct2024`
- `notes_bkk_13feb2024`
- `our_services_bkk_13may2024`
- `partner_student_invoices_bkk_30nov2024`
- `partners_bkk_18nov2024`
- `upload_checklists_bkk_29mar2025`
- `user_roles_bkk_25jan2024`
- `why_chooseuses_bkk_13may2024`
- `workflow_stages_bkk_7dec2024`

---

## 1. Appointment System Code (HIGH PRIORITY)

### Models
- **File**: `app/Models/Appointment.php` (if exists, check)
- **File**: `app/Models/AppointmentLog.php` - **EXISTS** - Used in AssigneeController

### Controllers

#### `app/Http/Controllers/Admin/ClientsController.php`
- **Line 2466-2974**: `addAppointment()` method - **ALREADY COMMENTED OUT** but contains commented code referencing `Appointment` model
- **Line 2528**: `DB::table('book_services')->where('id', $request->service_id)->first()` - **ACTIVE CODE** - references removed table
- **Line 2649**: `\App\Models\Mail\AppointmentStripeMail` - **ACTIVE CODE** - mail class may not exist
- **Line 2686**: `\App\Models\Appointment::find()` - in commented code
- **Line 2732, 2734**: `\App\Models\Appointment::where()->exists()` - in commented code
- **Line 2801, 2815, 2838, 2856, 2867, 2878**: Multiple `Appointment::` references - in commented code
- **Line 2936**: `DB::table('book_services')->where('id', $obj->service_id)->first()` - **ACTIVE CODE** - references removed table
- **Line 2977-3096**: `getAppointments()` method - **ACTIVE CODE** - contains `\App\Models\Appointment` queries
- **Line 3096**: `\App\Models\Appointment::find($request->id)` - **ACTIVE CODE**
- **Line 3240-3242**: `deleteappointment()` method - **PARTIALLY COMMENTED** - contains `\App\Models\Appointment::where()` and `DB::table('appointments')`
- **Line 4780-4809**: Client merge functionality - **ACTIVE CODE** - `DB::table('appointments')` queries
- **Line 5109-5118**: Client merge functionality - **ACTIVE CODE** - `DB::table('appointments')` queries

#### `app/Http/Controllers/Agent/ClientsController.php`
- **Line 1177-1284**: `addAppointment()` method - **ALREADY COMMENTED OUT** but contains commented code
- **Line 1286-1380**: `getAppointments()` method - **ALREADY COMMENTED OUT** but contains commented code
- **Line 1380**: `\App\Models\Appointment::find($request->id)` - in commented code
- **Line 1524-1526**: `deleteappointment()` method - **ALREADY COMMENTED OUT** - contains `\App\Models\Appointment::where()` and `DB::table('appointments')`

#### `app/Http/Controllers/Admin/AssigneeController.php`
- **Line 9**: `// use App\Models\Appointment;` - commented import
- **Line 11**: `use App\Models\AppointmentLog;` - **ACTIVE IMPORT** - model may need to be checked
- **Line 717-720**: `create()` method - returns `view('appointment.create')` - **ACTIVE CODE** - view may not exist
- **Line 736**: `route('appointment.index')` - **ACTIVE CODE** - route may not exist
- **Line 746-754**: `show()` method - **PARTIALLY COMMENTED** - contains commented `Appointment::with()` query
- **Line 752**: `view('Admin.appointments.show')` - in commented code
- **Line 762-770**: `edit()` method - **PARTIALLY COMMENTED** - contains commented `Appointment::with()` query
- **Line 768**: `view('Admin.appointments.edit')` - in commented code
- **Line 796**: `route('appointments.index')` - **ACTIVE CODE** - route may not exist
- **Line 932**: `Appointment::with([...])->where('id',$request->id)->first()` - **ACTIVE CODE** - in `getappointmentdetail()` method
- **Line 1084**: `AppointmentLog::where('appointment_id',$appointmentdetail->id)` - **ACTIVE CODE**
- **Line 1114, 1130, 1136, 1166, 1198, 1242, 1246**: Multiple `AppointmentLog` model usages - **ACTIVE CODE**

#### `app/Http/Controllers/Admin/AdminController.php`
- **Line 1196-1207**: `deleteSlotAction()` method - **ACTIVE CODE** - contains `DB::table('book_service_disable_slots')` queries

### Views

#### `resources/views/Elements/Admin/left-side-bar.blade.php`
- **Line 27-28**: Commented appointment menu link - **ALREADY COMMENTED**

#### `resources/views/Admin/clients/detail.blade.php`
- **Line 818**: Commented appointments tab - **ALREADY COMMENTED**
- **Line 1773**: Commented appointments tab content - **ALREADY COMMENTED**
- **Line 7920**: Commented appointment create functionality - **ALREADY COMMENTED**

#### `resources/views/Agent/clients/detail.blade.php`
- **Line 266**: Commented appointments tab - **ALREADY COMMENTED**
- **Line 554**: Commented appointments tab removed comment - **ALREADY COMMENTED**
- **Line 797**: `url: site_url+'/agent/get-appointments'` - **ACTIVE CODE** - AJAX call
- **Line 1513**: `$('.appointmentcreatedemail').html(...)` - **ACTIVE CODE** - JavaScript reference

#### `resources/views/Admin/partners/detail.blade.php`
- **Line 229**: Commented appointments tab - **ALREADY COMMENTED**
- **Line 926**: Commented appointments tab removed comment - **ALREADY COMMENTED**

#### `resources/views/Admin/applications/detail.blade.php`
- **Line 180**: `<a class="nav-link" ... id="appointments-tab" href="#appointments"` - **ACTIVE CODE** - appointments tab link
- **Line 307, 334, 361, 388, 415, 442, 465, 488**: Multiple "Add Appointments" links - **ACTIVE CODE**
- **Line 1034**: `<div class="tab-pane fade" id="appointments"` - **ACTIVE CODE** - appointments tab content

#### `resources/views/Admin/users/view.blade.php`
- **Line 580**: `<p class="mb-0">Appointments</p>` - **ACTIVE CODE**
- **Line 633**: `<li>Invitee in Appointments</li>` - **ACTIVE CODE**
- **Line 776**: `url: site_url+'/admin/get-appointments'` - **ACTIVE CODE** - AJAX call

#### `resources/views/Admin/agents/detail.blade.php`
- **Line 605**: `url: site_url+'/admin/get-appointments'` - **ACTIVE CODE** - AJAX call
- **Line 1082**: `$('.appointmentcreatedemail').html(...)` - **ACTIVE CODE** - JavaScript reference

#### `resources/views/Admin/products/detail.blade.php`
- **Line 772**: `url: site_url+'/admin/get-appointments'` - **ACTIVE CODE** - AJAX call

#### `resources/views/Admin/userrole/edit.blade.php`
- **Line 273-281**: "APPOINTMENTS" section with module access checkbox - **ACTIVE CODE**

#### `resources/views/Admin/userrole/create.blade.php`
- **Line 267-275**: "APPOINTMENTS" section with module access checkbox - **ACTIVE CODE**

### JavaScript Files

#### `public/js/custom-form-validation.js`
- **Line 2084**: `url: site_url+'/admin/get-appointments'` - **ACTIVE CODE**
- **Line 2137**: `url: site_url+'/admin/partner/get-appointments'` - **ACTIVE CODE**
- **Line 2172, 2302, 2397**: Multiple `url: site_url+'/admin/get-appointments'` - **ACTIVE CODE**
- **Line 2447**: `url: site_url+'/admin/partner/get-appointments'` - **ACTIVE CODE**

#### `public/js/agent-custom-form-validation.js`
- **Line 1520**: `url: site_url+'/agent/get-appointments'` - **ACTIVE CODE**
- **Line 1570**: `url: site_url+'/agent/partner/get-appointments'` - **ACTIVE CODE**
- **Line 1604, 1648, 1691**: Multiple `url: site_url+'/agent/get-appointments'` - **ACTIVE CODE**
- **Line 1741**: `url: site_url+'/agent/partner/get-appointments'` - **ACTIVE CODE**

### Mail Classes
- **Reference**: `app/Http/Controllers/Admin/ClientsController.php:2649` - `\App\Models\Mail\AppointmentStripeMail` - **ACTIVE CODE** - check if file exists

### Routes
- **File**: `routes/web.php` - Check for appointment-related routes (may be commented)
- **Reference**: `app/Http/Controllers/Admin/AssigneeController.php:736` - `route('appointment.index')` - **ACTIVE CODE**
- **Reference**: `app/Http/Controllers/Admin/AssigneeController.php:796` - `route('appointments.index')` - **ACTIVE CODE**
- **Reference**: `routes/web.php:96` - Commented `bookappointment` route - **ALREADY COMMENTED**

### Storage Framework Views
- **File**: `storage/framework/views/eecaaf9f96b58d2fc4ad15aa2a9f3ad5.php` (compiled view)
  - **Line 27**: `URL::to('/admin/appointments-cal')` - **ACTIVE CODE** - compiled view reference

---

## 2. SeoPage Model (CHECK IF STILL NEEDED)

### Models
- **File**: `app/Models/SeoPage.php` - **EXISTS** - Check if still used elsewhere

### Controllers
- **File**: `app/Http/Controllers/Admin/AdminController.php`
  - **Line 15**: `use App\Models\SeoPage;` - **ACTIVE IMPORT** - check usage in file

---

## 3. Free Downloads Config (LOW PRIORITY)

### Config Files
- **File**: `config/constants.php`
  - **Line 34-37**: Path constants for free downloads:
    - `'free_imgs' => public_path().'/img/free_downloads/free_imgs'`
    - `'free_audio' => public_path().'/img/free_downloads/audio'`
    - `'free_video' => public_path().'/img/free_downloads/video'`
    - `'free_pdf' => public_path().'/img/free_downloads/pdf'`
  - **Status**: **ACTIVE CODE** - May be safe to remove if directories don't exist

### Directories
- Check if `public/img/free_downloads/` directory exists (not found in current listing)

---

## 4. Frontend/Website Tables (NO ACTIVE REFERENCES FOUND)

The following tables were removed but no active code references found:
- `blog_categories`
- `blogs`
- `cms_pages`
- `testimonials`
- `sliders`
- `popups`
- `reviews`
- `wishlists`
- `home_contents`
- `faqs`
- `navmenus`
- `theme_options`
- `banners`
- `our_offices`
- `our_services`
- `why_chooseuses`

**Note**: Routes for frontend website were already commented out in `routes/web.php` (lines 94-96).

---

## 5. Legacy Tour/Travel Tables (MINIMAL REFERENCES)

### Controllers
- **File**: `app/Http/Controllers/Admin/ContactController.php`
  - **Line 287**: Commented code referencing `destinations` table - **ALREADY COMMENTED**

### Views
- **File**: `resources/views/layouts/agent-login.blade.php`
  - **Line 9**: Meta description mentioning "holiday packages" - **ACTIVE CODE** but just text, not table reference

---

## 6. Other Unused Tables (NO ACTIVE REFERENCES FOUND)

The following tables were removed but no active code references found:
- `coupons`
- `offers`
- `omrs`
- `media_images`

---

## 7. BKK Backup Tables (NO ACTIVE REFERENCES FOUND)

All BKK backup tables were removed but no active code references found in the codebase.

---

## Summary Statistics

### Active Code References (Need Review/Cleanup)
- **Appointment system**: ~50+ active references across controllers, views, JavaScript
- **Book Services**: 3 active DB queries in ClientsController
- **Book Service Disable Slots**: 1 active method in AdminController
- **AppointmentLog model**: 7+ active references in AssigneeController
- **AppointmentStripeMail**: 1 active reference in ClientsController
- **SeoPage model**: 1 import (check usage)
- **Free downloads config**: 4 config constants

### Already Commented/Disabled
- Most appointment controller methods are already commented out
- Most appointment view tabs are already commented out
- Frontend routes are already commented out

### Priority Actions
1. **HIGH**: Remove active appointment-related code from ClientsController (getAppointments, book_services queries, merge functions)
2. **HIGH**: Remove active AppointmentLog references from AssigneeController
3. **HIGH**: Remove active book_service_disable_slots code from AdminController
4. **MEDIUM**: Remove appointment-related JavaScript AJAX calls
5. **MEDIUM**: Remove appointment-related views/tabs that are still active
6. **MEDIUM**: Check and remove AppointmentStripeMail if not needed
7. **LOW**: Clean up commented code blocks
8. **LOW**: Remove free_downloads config constants if not used

---

## Notes

- Some code is already commented out (marked as removed) but still present in files
- Some views have commented-out appointment tabs but JavaScript may still reference them
- Check if AppointmentLog model file exists and if it's used for anything else
- Check if SeoPage model is used elsewhere before removing
- Verify AppointmentStripeMail class exists and usage
- Routes may need to be checked for appointment-related endpoints

