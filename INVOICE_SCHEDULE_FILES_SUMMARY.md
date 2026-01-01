# Invoice Schedule Related Files - Complete Summary

This document lists all files related to Invoice Scheduling functionality in the Bansal CRM application.

## 📁 Models

1. **`app/Models/InvoiceSchedule.php`**
   - Main model for invoice schedules
   - Relationships: `client()`, `application()`, `scheduleItems()`, `user()`
   - Fillable fields: `user_id`, `client_id`, `application_id`, `installment_name`, `installment_date`, `invoice_sc_date`, `discount`, `installment_no`, `installment_intervel`

2. **`app/Models/ScheduleItem.php`**
   - Model for schedule items (fee details within a schedule)
   - Relationship: `invoiceSchedule()` - belongs to InvoiceSchedule
   - Fillable fields: `schedule_id`, `fee_amount`, `fee_type`, `commission`

## 🎮 Controllers

1. **`app/Http/Controllers/Admin/InvoiceController.php`**
   - Main controller handling all invoice schedule operations
   - Key methods:
     - `invoiceschedules()` - List all invoice schedules (line 976)
     - `paymentschedule()` - Create new payment schedule (line 1010)
     - `setuppaymentschedule()` - Setup payment schedule with application fee options (line 1058)
     - `editpaymentschedule()` - Edit existing payment schedule (line 1146)
     - `deletepaymentschedule()` - Delete payment schedule (line 991)
     - `getallpaymentschedules()` - Get all schedules for a client/application (line 1193)
     - `addscheduleinvoicedetail()` - Get add schedule form HTML (line 1251)
     - `scheduleinvoicedetail()` - Get edit schedule form HTML (line 1400)
     - `apppreviewschedules()` - Preview schedules as PDF (line 1616)
     - `createInvoice()` - Create invoice from schedule (line 45) - includes schedule_id parameter

## 🛣️ Routes

**`routes/web.php`** (Admin routes, lines 476-484):
- `GET /admin/invoice-schedules` → `InvoiceController@invoiceschedules` (named: `admin.invoice.invoiceschedules`)
- `POST /admin/paymentschedule` → `InvoiceController@paymentschedule` (named: `admin.invoice.paymentschedule`)
- `POST /admin/setup-paymentschedule` → `InvoiceController@setuppaymentschedule`
- `POST /admin/editpaymentschedule` → `InvoiceController@editpaymentschedule` (named: `admin.invoice.editpaymentschedule`)
- `GET /admin/scheduleinvoicedetail` → `InvoiceController@scheduleinvoicedetail`
- `GET /admin/addscheduleinvoicedetail` → `InvoiceController@addscheduleinvoicedetail`
- `GET /admin/get-all-paymentschedules` → `InvoiceController@getallpaymentschedules`
- `GET /admin/deletepaymentschedule` → `InvoiceController@deletepaymentschedule`
- `GET /admin/applications/preview-schedules/{id}` → `InvoiceController@apppreviewschedules`

## 👁️ Views

### Main Views
1. **`resources/views/Admin/invoice/invoiceschedules.blade.php`**
   - Main listing page for invoice schedules
   - Contains modals for Add/Edit payment schedule
   - JavaScript handlers for CRUD operations

### Application Detail Views
2. **`resources/views/Admin/clients/applicationdetail.blade.php`**
   - Payment Schedule tab (line 387)
   - Displays schedules related to an application
   - Buttons: "Add Schedule", "Edit", "Delete", "Create Invoice"

3. **`resources/views/Agent/clients/applicationdetail.blade.php`**
   - Agent version of application detail with payment schedule tab

### Modal Views (Embedded in other views)
4. **`resources/views/Admin/clients/detail.blade.php`**
   - Contains payment schedule modals and JavaScript handlers
   - Lines 7013-7602: JavaScript for payment schedule operations

5. **`resources/views/Agent/clients/detail.blade.php`**
   - Agent version with payment schedule functionality
   - Lines 2013-2538: JavaScript handlers

6. **`resources/views/Admin/clients/addclientmodal.blade.php`**
   - Contains payment schedule modals:
     - `#create_paymentschedule` (line 1617)
     - `#create_apppaymentschedule` (line 1938)
     - `#editpaymentschedule` (line 2038)
     - `#addpaymentschedule` (line 2056)

7. **`resources/views/Agent/clients/addclientmodal.blade.php`**
   - Agent version with payment schedule modals
   - Similar structure to admin version

### Email/PDF Templates
8. **`resources/views/emails/paymentschedules.blade.php`**
   - PDF/Email template for payment schedule preview
   - Used by `apppreviewschedules()` method

### Other Views (Reference Invoice Schedules)
9. **`resources/views/Admin/applications/detail.blade.php`**
   - Contains payment schedule tab (line 292, 631)

10. **`resources/views/Admin/products/detail.blade.php`**
    - References payment schedule functionality (line 1634)

11. **`resources/views/Admin/partners/detail.blade.php`**
    - References payment schedule functionality (line 5081)

12. **`resources/views/Admin/products/addproductmodal.blade.php`**
    - Contains payment schedule modal (line 1160)

13. **`resources/views/Admin/partners/addpartnermodal.blade.php`**
    - Contains payment schedule modal (line 1498)

14. **`resources/views/Admin/users/view.blade.php`**
    - References payment schedule (line 1638)

15. **`resources/views/Admin/agents/detail.blade.php`**
    - References payment schedule (line 1411)

## 🗂️ Navigation

**`resources/views/Elements/Admin/left-side-bar.blade.php`**
- Invoice Schedule menu item (line 250)
- Active route check: `admin.invoice.invoiceschedules` (line 231)

## 🔧 Related Files

1. **`routes/test.php`**
   - Test routes for InvoiceSchedule model (line 4, 13-14, 22, etc.)

2. **`nearly_empty_tables_analysis.md`**
   - Database analysis mentioning `invoice_schedules` table (line 134)

3. **`database/migrations/2025_12_28_091723_fix_all_primary_keys_and_sequences.php`**
   - Migration file mentioning `invoice_schedules` table (line 60)

## 🔑 Key Features

1. **Invoice Schedule Management**
   - Create, Read, Update, Delete operations
   - Associate schedules with clients and applications
   - Multiple fee types per schedule
   - Discount support
   - Invoice date scheduling

2. **Integration Points**
   - Linked to Applications (via `application_id`)
   - Linked to Clients (via `client_id`)
   - Can create invoices from schedules
   - PDF preview/export functionality
   - Email scheduling support

3. **Data Structure**
   - InvoiceSchedule (parent)
     - ScheduleItem (child) - multiple items per schedule
   - Each schedule has:
     - Installment name and date
     - Invoice schedule date
     - Discount amount
     - Multiple fee items (fee_type, fee_amount, commission)

## 📊 Database Tables

1. **`invoice_schedules`** - Main schedule table
2. **`schedule_items`** - Fee items within schedules

## 🔄 Workflow

1. User creates payment schedule for an application
2. Schedule includes installment details and fee breakdown
3. Invoice can be scheduled for a specific date (`invoice_sc_date`)
4. User can create invoices from schedules
5. Schedules can be previewed/exported as PDF
6. Schedules can be edited or deleted

---

**Last Updated**: Generated from codebase analysis
**Related Feature**: Invoice Management, Payment Scheduling, Application Management

