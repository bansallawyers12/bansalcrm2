# Frontend Immigration Website - Blade Files List

This document lists all blade files related to the frontend immigration website for Bansal Immigration Consultants.

## Active Frontend Layout Files

1. **`resources/views/layouts/dashboard_frontend.blade.php`**
   - Main frontend layout file
   - Contains frontend-specific CSS and JavaScript includes
   - Used for client-facing pages

## Active Frontend Page Files

2. **`resources/views/index_old.blade.php`**
   - Old index/home page
   - Uses `dashboard_frontend` layout
   - Contains service listings and search functionality

3. **`resources/views/coming_soon.blade.php`**
   - Coming soon page (currently commented out in routes)
   - Contains placeholder content

## Old Frontend Elements (Frontend_old directory)

### Header & Navigation
4. **`resources/views/Elements/Frontend_old/header.blade.php`**
   - Frontend website header
   - Contains top header with contact info and social links
   - Main navigation menu with immigration services (Study in Australia, Visitor Visa, Migration, Family visa, Employee sponsored visas, Business visas, Appeals, Citizenships, Other Countries)

5. **`resources/views/Elements/Frontend_old/navigation.blade.php`**
   - Navigation component (appears to be for dashboard/user area)

6. **`resources/views/Elements/Frontend_old/footer.blade.php`**
   - Frontend website footer
   - Contains "BANSAL Immigration Consultants" branding
   - MARN: 1569359
   - Office locations and contact information
   - Social media links

### Sidebar
7. **`resources/views/Elements/Frontend_old/left-sidebar.blade.php`**
   - Left sidebar component for user dashboard

8. **`resources/views/Elements/Frontend_old/left-sidebar_old.blade.php`**
   - Old version of left sidebar

### Content Pages
9. **`resources/views/Elements/Frontend_old/content-pages/download_test.blade.php`**
10. **`resources/views/Elements/Frontend_old/content-pages/evaluated_sheet.blade.php`**
11. **`resources/views/Elements/Frontend_old/content-pages/mentoring.blade.php`**
12. **`resources/views/Elements/Frontend_old/content-pages/order-summary.blade.php`**
13. **`resources/views/Elements/Frontend_old/content-pages/query.blade.php`**
14. **`resources/views/Elements/Frontend_old/content-pages/result.blade.php`**
15. **`resources/views/Elements/Frontend_old/content-pages/upload_test.blade.php`**
16. **`resources/views/Elements/Frontend_old/content-pages/user_management.blade.php`**

### Old Content Pages (backup)
17. **`resources/views/Elements/Frontend_old/content-pages_old/`** (directory with backup versions of above files)

### Other Old Files
18. **`resources/views/Elements/Frontend_old/footer.blade_old.php`**
   - Old version of footer

## Email Templates (Immigration-Related)

These email templates contain references to "Bansal Immigration" and are used for client communications:

19. **`resources/views/emails/clientverifymail.blade.php`**
    - Email verification template
    - Contains links to bansalimmigration.com.au

20. **`resources/views/emails/googlereview.blade.php`**
    - Google review request email
    - References "Bansal Immigration"

21. **`resources/views/emails/visaexpirereminder.blade.php`**
    - Visa expiry reminder email
    - Contains "Bansal Immigration Consultants" branding

22. **`resources/views/emails/appointment.blade.php`**
    - Appointment email template
    - Contains "Bansal Immigration Consultants" branding

23. **`resources/views/emails/printpreview.blade.php`**
    - Print preview template
    - Contains "BANSAL IMMIGRATION CONSULTANTS" header

## Notes

- Most frontend website routes are currently commented out in `routes/web.php`
- The `HomeController` has most frontend methods removed/commented out
- The active frontend functionality appears to be limited to client self-update features (email verification, DOB verification, client edit form)
- The old frontend website files are preserved in the `Frontend_old` directory but may not be actively used
- The header file shows extensive navigation structure for immigration services including:
  - Study in Australia (Education, Student Visa, Other)
  - Visitor Visa
  - Migration (Graduate visa, Permanent Visa, Regional Visas, Skill Assessment, Others)
  - Family visa (Partner visa, Parents Visa, Child Visas, Relative visas)
  - Employee sponsored visas
  - Business visas
  - Appeals
  - Citizenships
  - Other Countries (Canada, New Zealand, USA)

## Status

**Active/Current:** Files 1-3 (though routes may be commented out)
**Archived/Old:** Files 4-18 (in Frontend_old directory)
**Supporting:** Files 19-23 (Email templates with immigration branding)

