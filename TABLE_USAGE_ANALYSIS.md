# Table Usage Analysis

This document provides detailed information about each table's usage, purpose, and status in the application.

---

## **17. cashbacks**
**Status:** ✅ **MIGRATION EXECUTED - TABLE REMOVED**
- **Model:** No model found
- **References:** No active code references found
- **Function:** Likely for tracking cashback rewards (legacy/unused feature)
- **Migration:** `2025_12_27_180929_drop_cashbacks_table.php` - **EXECUTED**
- **Action:** ✅ Table dropped from both MySQL and PostgreSQL

---

## **25. cities**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/City.php` exists
- **References:** Used in 20+ files
- **Function:** Stores city/location data for clients, partners, addresses
- **Usage:** 
  - Dropdowns in client/partner forms
  - Address management
  - Location filtering
- **Recommendation:** **KEEP** - Core reference data

---

## **26. client_monthly_rewards**
**Status:** ✅ **MIGRATION EXECUTED - TABLE REMOVED**
- **Model:** No dedicated model (accessed via DB::table)
- **References:** 
  - `app/Http/Controllers/Admin/ReportController.php` (lines 147-201) - `clientrandomlyselectmonthly()` and `saveclientrandomlyselectmonthly()` methods
  - `app/Console/Commands/RandomClientSelectionReward.php` - Console command (already commented in Kernel.php)
  - View: `resources/views/Admin/reports/clientrandomlyselectmonthly.blade.php`
  - Routes: `routes/web.php` (lines 718, 720)
  - Navigation: `resources/views/Elements/Admin/left-side-bar.blade.php` (line 327)
- **Function:** Tracks monthly randomly selected clients for rewards
- **Migration:** `2025_12_27_180929_drop_cashbacks_table.php` - **EXECUTED**
- **Action:** ✅ Table dropped - Code cleanup needed (remove controller methods, routes, views, navigation)

---

## **27. client_phones**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/ClientPhone.php` exists
- **References:** Used in 8 files including ClientsController, HomeController
- **Function:** Stores multiple phone numbers for clients (not just single phone field)
- **Usage:** 
  - Client detail pages
  - Contact information management
  - Phone number tracking
- **Recommendation:** **KEEP** - Core client data

---

## **28. client_service_takens**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/clientServiceTaken.php` exists
- **References:** Used in ClientsController and client detail views
- **Function:** Tracks services taken by clients (education courses, migration services, etc.)
- **Fields:** service_type, mig_ref_no, edu_course, edu_college, edu_service_start_date
- **Usage:** 
  - Client service history
  - Application-related services
- **Recommendation:** **KEEP** - Core client service tracking

---

## **29. contacts**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/Contact.php` exists
- **References:** Used in 13 files including ContactController
- **Function:** Manages contact/company information (separate from clients)
- **Usage:** 
  - Contact management module (`/admin/contact`)
  - Partner contacts
  - General contact directory
- **Routes:** Full CRUD routes in web.php
- **Recommendation:** **KEEP** - Active contact management system

---

## **30. countries**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/Country.php` exists
- **References:** Used in 20+ files
- **Function:** Stores country reference data
- **Usage:** 
  - Country dropdowns in forms
  - Address management
  - Client/partner profiles
- **Recommendation:** **KEEP** - Core reference data

---

## **32. currencies**
**Status:** ✅ **IN USE**
- **Model:** No dedicated model (but referenced)
- **References:** Used in 20+ files, especially in invoice and quotation views
- **Function:** Currency reference data for invoices, quotations, pricing
- **Usage:** 
  - Invoice creation/display
  - Quotation pricing
  - Multi-currency support
- **Recommendation:** **KEEP** - Essential for financial transactions

---

## **35. download_schedule_dates**
**Status:** ✅ **MIGRATION EXECUTED - TABLE REMOVED**
- **Model:** No model found
- **References:** No code references found
- **Function:** Unknown (possibly for scheduling file downloads)
- **Migration:** `2025_12_27_180929_drop_cashbacks_table.php` - **EXECUTED**
- **Action:** ✅ Table dropped from both MySQL and PostgreSQL

---

## **39. enquiries**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/Enquiry.php` exists
- **References:** Used in EnquireController
- **Function:** Stores customer enquiries/leads from website/forms
- **Usage:** 
  - Enquiry management (`/admin/enquiries`)
  - Lead tracking
  - Customer inquiries
- **Routes:** Index, archived, convert to client
- **Recommendation:** **KEEP** - Active enquiry management

---

## **40. enquiry_sources**
**Status:** ✅ **MIGRATION EXECUTED - TABLE REMOVED**
- **Model:** `app/Models/EnquirySource.php` exists
- **References:** Used in EnquirySourceController (but enquiries are no longer created)
- **Function:** Reference table for enquiry sources (how customer found you)
- **Usage:** 
  - Enquiry source management (`/admin/enquirysource`) - standalone management
  - Not actually linked to enquiries table (enquiries.source is just text field)
- **Routes:** Full CRUD routes (but not needed since no new enquiries)
- **Migration:** `2025_12_27_180929_drop_cashbacks_table.php` - **EXECUTED**
- **Action:** ✅ Table dropped - Code cleanup needed (EnquirySourceController, routes, views, navigation)

---

## **41. fee_option_types**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/FeeOptionType.php` exists
- **References:** Used in multiple controllers (Products, Applications)
- **Function:** Types/categories of fee options
- **Usage:** 
  - Fee structure management
  - Product/application fee configuration
- **Recommendation:** **KEEP** - Core billing data

---

## **42. fee_options**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/FeeOption.php` exists
- **References:** Used extensively in fee management
- **Function:** Individual fee options/packages
- **Usage:** 
  - Fee management for products/applications
  - Pricing configuration
- **Recommendation:** **KEEP** - Core billing data

---

## **43. fee_types**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/FeeType.php` exists
- **References:** Used in FeeTypeController
- **Function:** Categories/types of fees (tuition, application, etc.)
- **Usage:** 
  - Fee type management (`/admin/feetype`)
  - Fee categorization
- **Routes:** Full CRUD routes
- **Recommendation:** **KEEP** - Active reference data

---

## **46. groups**
**Status:** ✅ **MIGRATION EXECUTED - TABLE REMOVED**
- **Model:** No direct model found (but `ToDoGroup` model exists for `to_do_groups` table)
- **References:** No code references found - confirmed unused
- **Function:** Legacy table - task grouping actually uses `to_do_groups` table (via `ToDoGroup` model)
- **Migration:** `2025_12_27_180929_drop_cashbacks_table.php` - **EXECUTED**
- **Action:** ✅ Table dropped from both MySQL and PostgreSQL

---

## **47. income_sharings**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/IncomeSharing.php` exists
- **References:** Used in AccountController, InvoiceController
- **Function:** Tracks income sharing/commission between partners and company
- **Usage:** 
  - Income sharing reports
  - Commission calculations
  - Partner payments
- **Views:** Payable unpaid/paid reports
- **Recommendation:** **KEEP** - Core financial/accounting feature

---

## **58. markups**
**Status:** ✅ **MIGRATION EXECUTED - TABLE REMOVED**
- **Model:** No model found
- **References:** No active code references found
- **Function:** Unknown (possibly for price markups)
- **Migration:** `2025_12_27_180929_drop_cashbacks_table.php` - **EXECUTED**
- **Action:** ✅ Table dropped from both MySQL and PostgreSQL

---

## **59. mentorings**
**Status:** ✅ **MIGRATION EXECUTED - TABLE REMOVED**
- **Model:** No model found
- **References:** No code references found
- **Function:** Unknown (possibly for mentoring/coaching tracking)
- **Migration:** `2025_12_27_180929_drop_cashbacks_table.php` - **EXECUTED**
- **Action:** ✅ Table dropped from both MySQL and PostgreSQL

---

## **60. migrations**
**Status:** ✅ **LARAVEL SYSTEM TABLE**
- **Function:** Laravel's migration tracking table
- **Usage:** Tracks which migrations have been run
- **Recommendation:** **MUST KEEP** - Required by Laravel framework

---

## **61. nature_of_enquiry**
**Status:** ✅ **MIGRATION EXECUTED - TABLE REMOVED**
- **Model:** `app/Models/NatureOfEnquiry.php` exists
- **References:** Only used in removed appointment code and unused form field
- **Function:** Categories/types of enquiries (what customer is asking about)
- **Usage:** 
  - Form field exists but `noe_id` is NOT saved to clients table
  - Only referenced in appointment merging code (appointments removed)
- **Migration:** `2025_12_27_180929_drop_cashbacks_table.php` - **EXECUTED**
- **Action:** ✅ Table dropped - Code cleanup needed (remove form field from client add modal)

---

## **64-68. oauth_* tables**
**Status:** ✅ **MIGRATION EXECUTED - TABLES REMOVED**
- **Tables:** 
  - `oauth_access_tokens`
  - `oauth_auth_codes`
  - `oauth_clients`
  - `oauth_personal_access_clients`
  - `oauth_refresh_tokens`
- **Package:** Laravel Passport (installed but NOT USED - removed from composer.json)
- **Function:** OAuth token management (from Laravel Passport)
- **Usage:** 
  - **NOT USED** - System uses Laravel Sanctum instead
  - Sanctum uses `personal_access_tokens` table (keep this one)
  - Passport was installed but never configured/used
- **Migration:** `2025_12_27_180929_drop_cashbacks_table.php` - **EXECUTED**
- **Action:** ✅ All 5 OAuth tables dropped from both MySQL and PostgreSQL - Laravel Passport package removed from composer.json

---

## **69. online_forms**
**Status:** ✅ **IN USE - KEEP**
- **Model:** `app/Models/OnlineForm.php` exists
- **References:** Used in ClientsController, client detail views
- **Function:** Stores client application form data (primary, secondary, child applicant forms)
- **Usage:** 
  - Client detail page forms (Primary/Secondary/Child tabs)
  - `saveonlineform()` method saves form data
  - Routes: `/saveonlineprimaryform`, `/saveonlinesecform`, `/saveonlinechildform`
  - Used in client merging functionality
- **Note:** This is NOT website forms - it's internal client application forms
- **Recommendation:** **KEEP** - Active client application form storage

---

## **84. promo_codes**
**Status:** ✅ **MIGRATION EXECUTED - TABLE REMOVED**
- **Model:** `app/Models/PromoCode.php` exists
- **References:** Used in PromoCodeController (but functionality is broken)
- **Function:** Promotional codes for discounts/special offers
- **Usage:** 
  - Promo code management interface exists (`/admin/promo-code`)
  - Form field exists in client add/edit modals
  - BUT: Code that saves usage is COMMENTED OUT
  - Success message references removed appointment system
- **Migration:** `2025_12_27_180929_drop_cashbacks_table.php` - **EXECUTED**
- **Action:** ✅ Table dropped - Code cleanup needed (PromoCodeController, routes, views, form fields, navigation)

---

## **85. promocode_uses**
**Status:** ✅ **MIGRATION EXECUTED - TABLE REMOVED**
- **Model:** No dedicated model (accessed via DB::table)
- **References:** Used in PromoCodeController (but INSERT is commented out)
- **Function:** Tracks usage of promo codes (which clients used which codes)
- **Usage:** 
  - Only used to CHECK if already used
  - INSERT statement is COMMENTED OUT (never saves data)
  - Table is effectively unused
- **Migration:** `2025_12_27_180929_drop_cashbacks_table.php` - **EXECUTED**
- **Action:** ✅ Table dropped - Table never actually saved data

---

## **86. promotions**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/Promotion.php` exists
- **References:** Used in PromotionController
- **Function:** Promotional campaigns/offers
- **Usage:** 
  - Promotion management
  - Marketing campaigns
- **Recommendation:** **KEEP** - Active promotion system

---

## **94. sessions**
**Status:** ✅ **LARAVEL SYSTEM TABLE**
- **Function:** Laravel session storage (database driver)
- **Usage:** Stores user session data
- **Recommendation:** **MUST KEEP** - Required by Laravel if using database sessions

---

## **95. settings**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/Setting.php` exists
- **References:** Used in AdminController (gensettings)
- **Function:** Application settings/configuration (per office)
- **Usage:** 
  - General settings (`/admin/gen-settings`)
  - Office-specific configuration
- **Routes:** Settings update route
- **Recommendation:** **KEEP** - Active settings system

---

## **104. task_logs**
**Status:** ✅ **IN USE**
- **Model:** No dedicated model found (but TaskLog may exist)
- **References:** Used in task-related views
- **Function:** Activity logs/history for tasks
- **Usage:** 
  - Task activity tracking
  - Task history
- **Recommendation:** **KEEP** - Task audit trail

---

## **105. tasks**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/Task.php` exists
- **References:** Used extensively in TasksController, DashboardService
- **Function:** Task management system (to-dos, assignments, tracking)
- **Usage:** 
  - Task management (`/admin/tasks`)
  - Task assignment
  - Task tracking and status
- **Routes:** Full task management routes
- **Recommendation:** **KEEP** - Core task management feature

---

## **106. tax_rates**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/TaxRate.php` exists
- **References:** Used in AdminController (taxrates), invoice views
- **Function:** Tax rate configurations
- **Usage:** 
  - Tax rate management (`/admin/settings/taxes/taxrates`)
  - Invoice tax calculations
- **Recommendation:** **KEEP** - Required for tax calculations

---

## **107. taxes**
**Status:** ✅ **IN USE**
- **Model:** `app/Models/Tax.php` exists
- **References:** Used in TaxController
- **Function:** Tax types/definitions
- **Usage:** 
  - Tax management (`/admin/tax`)
  - Tax type configuration
- **Routes:** Full CRUD routes
- **Recommendation:** **KEEP** - Active reference data

---

## **121. wallets**
**Status:** ✅ **MIGRATION EXECUTED - TABLE REMOVED**
- **Model:** No model found
- **References:** No code references found - confirmed unused
- **Function:** Unknown (possibly for wallet/balance tracking)
- **Note:** `wallet` field exists on `admins` table (different from `wallets` table)
- **Migration:** `2025_12_27_180929_drop_cashbacks_table.php` - **EXECUTED**
- **Action:** ✅ Table dropped from both MySQL and PostgreSQL

---

## Summary

### ✅ Tables to KEEP (Active Usage):
1. cities - Reference data
2. client_monthly_rewards - Monthly reward system
3. client_phones - Client contact data
4. client_service_takens - Client service tracking
5. contacts - Contact management
6. countries - Reference data
7. currencies - Financial transactions
8. enquiries - Enquiry management
9. enquiry_sources - Reference data
10. fee_option_types - Billing structure
11. fee_options - Billing structure
12. fee_types - Reference data
13. income_sharings - Financial/accounting
14. migrations - Laravel system table
15. nature_of_enquiry - Reference data
16. oauth_* (5 tables) - Laravel Passport
17. online_forms - Form data storage
18. promo_codes - Promotion system
19. promocode_uses - Promotion tracking
20. promotions - Promotion system
21. sessions - Laravel system table
22. settings - Application settings
23. task_logs - Task audit trail
24. tasks - Task management
25. tax_rates - Tax calculations
26. taxes - Tax reference data

### ✅ Tables REMOVED (Migration Executed):
1. **cashbacks** - ✅ Removed
2. **client_monthly_rewards** - ✅ Removed (code cleanup needed)
3. **download_schedule_dates** - ✅ Removed
4. **enquiry_sources** - ✅ Removed (code cleanup needed)
5. **groups** - ✅ Removed
6. **markups** - ✅ Removed
7. **mentorings** - ✅ Removed
8. **nature_of_enquiry** - ✅ Removed (code cleanup needed)
9. **oauth_access_tokens** - ✅ Removed (Laravel Passport)
10. **oauth_auth_codes** - ✅ Removed (Laravel Passport)
11. **oauth_clients** - ✅ Removed (Laravel Passport)
12. **oauth_personal_access_clients** - ✅ Removed (Laravel Passport)
13. **oauth_refresh_tokens** - ✅ Removed (Laravel Passport)
14. **promo_codes** - ✅ Removed (code cleanup needed)
15. **promocode_uses** - ✅ Removed (code cleanup needed)
16. **wallets** - ✅ Removed

**Total:** 16 tables removed from both MySQL and PostgreSQL databases
**Migration:** `2025_12_27_180929_drop_cashbacks_table.php` - EXECUTED

