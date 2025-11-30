# Models Used for Immigration Website (No Longer Exists)

This document lists PHP model files that were used specifically for the frontend immigration website functionality that has been removed/deactivated.

## ⚠️ IMPORTANT NOTE

The frontend immigration website routes have been commented out in `routes/web.php`, and most frontend methods have been removed from `HomeController.php`. However, many of these models are **STILL ACTIVELY USED** in the CRM/admin panel for managing immigration-related data internally.

---

## ✅ Models Specifically for Immigration Website (Primary Usage)

These models were primarily used for the frontend immigration website that no longer exists:

### 1. **Enquiry.php** 
- **Purpose**: Used for frontend contact/enquiry forms on the immigration website
- **Current Status**: 
  - Frontend routes commented out (`/enquiry`, `/enquiry/store`)
  - Still used in admin panel (`EnquireController`) to manage and convert enquiries to clients
  - Admin can view, archive, and convert enquiries to client records
- **Usage**: Frontend contact forms collected immigration enquiries which were then managed in admin

### 2. **EnquirySource.php**
- **Purpose**: Tracks the source of enquiries (how visitors found the immigration website)
- **Current Status**: 
  - Still actively used in admin panel
  - Admin can manage enquiry sources for reporting
- **Usage**: Was used to track which marketing channels brought enquiries to the frontend website

### 3. **OnlineForm.php**
- **Purpose**: Stores data from online forms submitted on the frontend immigration website
- **Current Status**: 
  - Still used in `ClientsController` to store client form submissions
  - Used for client self-update feature (still active)
- **Usage**: Was used for various online forms on the immigration website

---

## 📋 Models with Immigration-Related Features (Still Used in CRM)

These models contain immigration-specific fields/data but are **STILL ACTIVELY USED** in the CRM:

### 1. **VisaType.php**
- **Purpose**: Manages visa types (Student Visa, Tourist Visa, PR, etc.)
- **Current Status**: ✅ **ACTIVELY USED** in CRM
  - Used in client/lead forms to select visa type
  - Used in admin panel (`VisaTypeController`) for CRUD operations
  - Referenced in views: `clients/edit.blade.php`, `leads/create.blade.php`
- **Immigration Fields**: Visa type names for different immigration categories

### 2. **NatureOfEnquiry.php**
- **Purpose**: Categorizes enquiries by nature (Migration Advice, Student Visa, Tourist Visa, etc.)
- **Current Status**: ✅ **ACTIVELY USED** in CRM
  - Used in appointment booking API
  - Used in admin panel for categorizing enquiries
  - Immigration-related enquiry types include:
    - Migration Advice (noe_id = 1)
    - Migration Consultation (noe_id = 2)
    - Tourist Visa (noe_id = 4)
    - Student Visa/Education (noe_id = 5)
    - Complex matters: AAT, Protection visa, Federal Case (noe_id = 6)
    - Visa Cancellation/NOICC/Visa refusals (noe_id = 7)

### 3. **Admin.php Model** (with immigration fields)
- **Immigration-Specific Fields**:
  - `visa_type` - Current visa type
  - `visaExpiry` - Visa expiry date
  - `prev_visa` - Previous visa history (JSON)
  - `held_visa` - Currently held visa
  - `visa_refused` - Visa refusal status
  - `visa_opt` - Additional visa options
- **Current Status**: ✅ **ACTIVELY USED** in CRM for client/lead management
- **Usage**: These fields are used throughout the CRM to track client immigration status

### 4. **Application.php Model**
- **Immigration Fields**: 
  - Used for migration applications (workflow ID 5)
  - Document type "migration" vs "education"
- **Current Status**: ✅ **ACTIVELY USED** in CRM for application management

### 5. **Document.php Model**
- **Immigration Fields**:
  - `doc_type` can be "migration" or "education"
- **Current Status**: ✅ **ACTIVELY USED** in CRM for document management

---

## 🔄 Models Used in Immigration-Related Functionality (Still Active)

### 1. **WebsiteSetting.php**
- **Purpose**: Website configuration and settings
- **Current Status**: ✅ **ACTIVELY USED** 
  - Used in `HomeController` constructor
  - Used in base `Controller` class
  - Shared with all views via `View::share()`
- **Usage**: Still used for client-facing pages (email verification, DOB form)

### 2. **CrmEmailTemplate.php**
- **Immigration Usage**: 
  - Template ID 35 used for visa expiry reminder emails
- **Current Status**: ✅ **ACTIVELY USED** in CRM for email templates

---

## 📧 Email-Related Models (Still Used)

### 1. **Mail/VisaExpireReminderMail.php**
- **Purpose**: Email template for visa expiry reminders
- **Current Status**: ✅ **ACTIVELY USED** via cron job
- **Console Command**: `app/Console/Commands/VisaExpireReminderEmail.php`
- **Usage**: Automated email sent 15 days before visa expiry

### 2. **Mail/GoogleReviewMail.php**
- **Purpose**: Email requesting Google reviews
- **Contains**: References to "Bansal Immigration"
- **Current Status**: ✅ **ACTIVELY USED** in CRM

---

## 🚫 Frontend Website Features Removed

The following frontend website routes have been **commented out/removed**:

- `/enquiry` - Enquiry form route (commented out)
- `/enquiry/store` - Enquiry submission route (commented out)
- `/contact` - Contact form route (removed)
- `/contactus` - Contact us page (removed)
- Frontend index/home page (removed)
- Frontend service pages (removed)
- Frontend booking/appointment pages (removed)
- Frontend CMS pages (returns 404)

---

## 📊 Summary

### Models ONLY for Frontend Immigration Website:
- **Enquiry.php** - Frontend contact forms (but still used in admin)
- **EnquirySource.php** - Enquiry source tracking (but still used in admin)
- **OnlineForm.php** - Online form submissions (but still used for client forms)

### Models with Immigration Data (Still Used in CRM):
- **VisaType.php** - ✅ Still used
- **NatureOfEnquiry.php** - ✅ Still used
- **Admin.php** (with visa fields) - ✅ Still used
- **Application.php** (migration type) - ✅ Still used
- **Document.php** (migration documents) - ✅ Still used

### Key Insight:
**Most models are still actively used in the CRM/admin panel** even though the frontend immigration website no longer exists. The models were never exclusively for the frontend - they serve dual purposes:
1. Frontend website (removed)
2. CRM/Admin panel (still active)

The immigration website closure mainly affected the **frontend routes and views**, not the underlying data models which continue to support the CRM functionality.

---

## 🔍 Evidence of Removal

1. **routes/web.php**: Frontend routes are commented out (lines 40-99)
2. **HomeController.php**: Comment states "FRONTEND WEBSITE METHODS - REMOVED"
3. **Views**: Frontend immigration website views moved to `Frontend_old/` directory
4. **Email Templates**: Still contain "Bansal Immigration" branding but are used internally

---

## Recommendation

If you want to identify models that can be safely removed, focus on:
1. Models with **zero usage** in admin controllers
2. Models that were **only** referenced in commented-out routes
3. Models with **no active database tables**

However, based on this analysis, **most immigration-related models are still needed** for the CRM to function properly, even without the frontend website.

