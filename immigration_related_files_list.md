# Immigration-Related Files in the App Folder

## Models (PHP Classes)
1. **app/VisaType.php** - Model for managing visa types
2. **app/NatureOfEnquiry.php** - Model for nature of enquiry types (includes immigration-related enquiry types)

## Controllers
3. **app/Http/Controllers/HomeController.php** - Contains immigration website contact form handling, references to "bansalimmigration.com.au", payment processing for migration advice
4. **app/Http/Controllers/Admin/VisaTypeController.php** - Admin controller for managing visa types (CRUD operations)
5. **app/Http/Controllers/Admin/ApplicationsController.php** - Contains `migrationindex()` method for managing migration applications, handles visa application document types
6. **app/Http/Controllers/Admin/ClientsController.php** - Handles client visa information (visa_type, visaExpiry, prev_visa), migration document management, references to "Bansal Immigration"
7. **app/Http/Controllers/Admin/LeadController.php** - Handles lead visa information (visa_type, visa_expiry_date)
8. **app/Http/Controllers/Admin/ReportController.php** - Contains `visaexpires()` method for visa expiry reports
9. **app/Http/Controllers/Admin/AppointmentsController.php** - Handles tourist visa appointments
10. **app/Http/Controllers/Admin/EnquireController.php** - Handles enquiry visa information
11. **app/Http/Controllers/API/AppointmentController.php** - API for appointments, includes migration advice and migration consultation services, visa-related enquiry types
12. **app/Http/Controllers/Controller.php** - Base controller with email sender configuration for "noreply@bansalimmigration.com.au"
13. **app/Http/Controllers/Auth/AdminLoginController.php** - Contains references to "Bansal Immigration" in email templates

## Mail Classes
14. **app/Mail/VisaExpireReminderMail.php** - Email template for visa expiry reminders
15. **app/Mail/GoogleReviewMail.php** - Contains subject line "Invitation For Google Review At Bansal Immigration"

## Console Commands
16. **app/Console/Commands/VisaExpireReminderEmail.php** - Cron job command to send visa expiry reminder emails 15 days before expiry
17. **app/Console/Commands/WpAppointmentToCrm.php** - WordPress to CRM sync, maps migration advice and migration consultation services, references "Bansal immigration.com.au"

## Notes:
- The system handles multiple visa-related enquiry types including:
  - Tourist Visa (noe_id = 4)
  - Student Visa/Education (noe_id = 5)
  - Migration Advice (noe_id = 1)
  - Migration Consultation (noe_id = 2)
  - Complex matters: AAT, Protection visa, Federal Case (noe_id = 6)
  - Visa Cancellation/NOICC/Visa refusals (noe_id = 7)
- The system tracks visa expiry dates and sends automated reminders
- Migration applications use workflow ID 5
- Documents are categorized as "migration" or "education" types
