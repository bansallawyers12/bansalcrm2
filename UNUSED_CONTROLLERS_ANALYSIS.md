# Unused Controllers Analysis

**Date Generated:** December 2025  
**Purpose:** Identify controllers that are not referenced in route files

---

## Analysis Method

I've analyzed all route files (`routes/web.php`, `routes/agent.php`, `routes/api.php`) to identify which controllers are actually used.

---

## Controllers Found in Routes

Based on route file analysis, the following controllers ARE USED:

### Admin Controllers (Used)
1. ✅ **AdminController** - Many routes
2. ✅ **ClientsController** - Many routes
3. ✅ **UserController** - User management routes
4. ✅ **StaffController** - Staff routes
5. ✅ **LeadController** - Lead routes
6. ✅ **ServicesController** - Services routes
7. ✅ **ContactController** - Contact management routes
8. ✅ **PartnersController** - Partner routes
9. ✅ **ProductsController** - Product routes
10. ✅ **InvoiceController** - Invoice routes
11. ✅ **QuotationsController** - Quotation routes
12. ✅ **AgentController** - Agent routes
13. ✅ **TasksController** - Task routes
14. ✅ **ApplicationsController** - Application routes
15. ✅ **AccountController** - Account/payment routes
16. ✅ **BranchesController** - Branch routes
17. ✅ **UsertypeController** - User type routes
18. ✅ **UserroleController** - User role routes
19. ✅ **ProductTypeController** - Product type routes
20. ✅ **ProfileController** - Profile routes
21. ✅ **PartnerTypeController** - Partner type routes
22. ✅ **VisaTypeController** - Visa type routes
23. ✅ **MasterCategoryController** - Master category routes
24. ✅ **LeadServiceController** - Lead service routes
25. ✅ **TaxController** - Tax routes
26. ✅ **SubjectAreaController** - Subject area routes
27. ✅ **SubjectController** - Subject routes
28. ✅ **SourceController** - Source routes
29. ✅ **TagController** - Tag routes
30. ✅ **ChecklistController** - Checklist routes
31. ✅ **EnquirySourceController** - Enquiry source routes
32. ✅ **FeeTypeController** - Fee type routes
33. ✅ **WorkflowController** - Workflow routes
34. ✅ **EducationController** - Education routes
35. ✅ **EmailTemplateController** - Email template routes
36. ✅ **EmailController** - Email routes
37. ✅ **CrmEmailTemplateController** - CRM email template routes
38. ✅ **OfficeVisitController** - Office visit routes
39. ✅ **EnquireController** - Enquiry routes
40. ✅ **AuditLogController** - Audit log routes
41. ✅ **ReportController** - Report routes
42. ✅ **PromotionController** - Promotion routes
43. ✅ **PromoCodeController** - Promo code routes
44. ✅ **AssigneeController** - Assignee routes (resource route)
45. ✅ **UploadChecklistController** - Upload checklist routes
46. ✅ **TeamController** - Team routes
47. ✅ **SmsController** - SMS routes
48. ✅ **SMSTwilioController** - Twilio SMS routes
49. ✅ **DocumentChecklistController** - Document checklist routes
50. ✅ **FollowupController** - Followup routes

### Agent Controllers (Used)
1. ✅ **DashboardController** - Agent dashboard routes
2. ✅ **ClientsController** - Agent client routes
3. ✅ **ApplicationsController** - Agent application routes

### Auth Controllers (Used)
1. ✅ **AdminLoginController** - Admin login routes
2. ✅ **AgentLoginController** - Agent login routes

### API Controllers (Used)
1. ✅ **AuthController** - API authentication routes

### Other Controllers (Used)
1. ✅ **HomeController** - Client self-update feature (email verify, DOB verification, edit client)
2. ✅ **ExceptionController** - Exception handling route

---

## Controllers to Check (May be Unused)

Based on the controller list vs route usage, I need to verify these controllers exist and if they're used:

1. ⚠️ **Controller.php** - Base controller (should exist, used by all controllers)

---

## Next Steps

I need to do a more detailed analysis by:
1. Extracting all controller class names from route files
2. Comparing with actual controller files
3. Identifying which controllers exist but are not referenced

Let me create a more detailed analysis...

