# Used PHP Model Files - Website Analysis

This document lists which PHP model files from the provided list are actively used in the website.

## ✅ USED MODELS (25 files)

These models are actively used in controllers, routes, or views:

1. **AcademicRequirement.php** - Used in `ProductsController`
2. **AccountClientReceipt.php** - Used in `ClientsController`
3. **ActivitiesLog.php** - Used in multiple controllers (HomeController, ClientsController, AssigneeController, AdminController, AppointmentsController, PartnersController, ApplicationsController)
4. **Admin.php** - Used extensively across many controllers
5. **Agent.php** - Used in AdminController, ApplicationsController, Agent controllers
6. **Application.php** - Used in multiple controllers (ClientsController, AdminController, PartnersController, ApplicationsController, InvoiceController, ReportController)
7. **ApplicationActivitiesLog.php** - Used in ClientsController, ApplicationsController
8. **ApplicationDocument.php** - Used in ApplicationsController
9. **ApplicationDocumentList.php** - Used in ApplicationsController
10. **ApplicationFeeOption.php** - Used in ApplicationsController, InvoiceController
11. **ApplicationFeeOptionType.php** - Used in ApplicationsController, InvoiceController
12. **Appointment.php** - Used in multiple controllers (ClientsController, AssigneeController, AppointmentsController, PartnersController, API)
13. **AppointmentLog.php** - Used in AssigneeController, AppointmentsController
14. **AttachFile.php** - Used in InvoiceController
15. **Attachment.php** - Used in FollowupController
16. **Branch.php** - Used in AssigneeController, AppointmentsController, OfficeVisitController, TasksController, BranchesController
17. **Category.php** - Used in MasterCategoryController
18. **CheckinHistory.php** - Used in OfficeVisitController
19. **CheckinLog.php** - Used in ClientsController, AdminController, OfficeVisitController, ReportController
20. **Checklist.php** - Used in ChecklistController
21. **City.php** - Used in AdminController
22. **ClientPhone.php** - Used in HomeController, ClientsController
23. **clientServiceTaken.php** - Used in ClientsController
24. **Contact.php** - Used in AdminController, PartnersController, InvoiceController, ContactController
25. **Country.php** - Used in ProductsController, LoginController, OrganisationController, VendorController

---

## ❌ NOT USED MODELS (3 files)

These models are NOT actively used in the website:

1. **AdminMeta.php** - No references found in controllers, routes, or views
2. **ApplicationNote.php** - No references found in controllers, routes, or views
3. **AuditLog.php** - Has an AuditLogController route, but the controller uses `UserLog` model instead, not the AuditLog model

---

## Summary

- **Total files checked:** 29
- **Used:** 25 files
- **Not used:** 4 files

---

## Notes

- Some models like `Admin` are used extensively throughout the application
- The system appears to use the `Admin` model for both admin users and clients (differentiated by role)
- `AuditLog` model exists but is not used; the audit functionality uses `UserLog` model instead

