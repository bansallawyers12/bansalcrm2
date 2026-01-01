# Nearly Empty Tables Analysis (1-20 records, no activity in 6+ months)

## Summary
- **Total nearly empty tables:** 43
- **No activity in 6+ months:** 37
- **Recent activity (within 6 months):** 6

---

## ⚠️ KEEP - Still Actively Used (Reference/Lookup Tables)

These tables are lookup/reference tables that are still needed even if not updated recently:

1. **website_settings** (1 record, last: 2019-07-25)
   - ✅ ACTIVE - Has controller `AdminController@websiteSetting`
   - ✅ Model: `WebsiteSetting`
   - ✅ Route: `/admin/website_setting`
   - **Reason:** Used for website configuration

2. **password_reset_links** (2 records, last: 2020-08-14)
   - ✅ ACTIVE - Used for password reset functionality
   - **Reason:** Required for password reset feature

3. **sessions** (3 records, no timestamp)
   - ✅ ACTIVE - Laravel session storage
   - **Reason:** Required by Laravel framework

4. **branches** (3 records, last: 2022-11-25)
   - ✅ ACTIVE - Used in PartnersController, ProductsController
   - **Reason:** Reference data for branches

5. **partner_types** (3 records, last: 2022-11-14)
   - ✅ ACTIVE - Used in PartnersController import
   - **Reason:** Reference data for partner types

6. **workflows** (5 records, last: 2022-11-18)
   - ✅ ACTIVE - Used in PartnersController, ApplicationsController
   - **Reason:** Reference data for workflows

7. **user_types** (6 records, last: 2022-05-14)
   - ✅ ACTIVE - Used in UserController
   - **Reason:** Reference data for user types

8. **user_roles** (8 records, last: 2024-12-21)
   - ✅ ACTIVE - Used in UserroleController
   - **Reason:** Reference data for user roles/permissions

9. **categories** (11 records, last: 2019-07-12)
   - ✅ ACTIVE - Used in multiple controllers
   - **Reason:** Reference data for categories

10. **email_templates** (11 records, last: 2020-09-02)
    - ✅ ACTIVE - Used in EmailTemplateController, CronJob
    - **Reason:** Email template storage

11. **education** (13 records, last: 2024-08-19)
    - ✅ ACTIVE - Used in EducationController
    - **Reason:** Reference data for education levels

12. **document_checklists** (14 records, last: 2025-02-18)
    - ✅ ACTIVE - Used in DocumentChecklistController
    - **Reason:** Reference data for document checklists

13. **emails** (16 records, last: 2025-06-25)
    - ✅ ACTIVE - Used for email logging
    - **Reason:** Email history/logging

14. **lead_services** (19 records, last: 2022-11-21)
    - ✅ ACTIVE - Used in LeadServiceController
    - **Reason:** Reference data for lead services

---

## ⚠️ KEEP - Recent Activity (Within 6 Months)

15. **services** (1 record, last: 2025-12-03) - RECENT
16. **api_tokens** (3 records, last: 2025-08-03) - RECENT
17. **quotations** (8 records, last: 2025-12-03) - RECENT
18. **personal_access_tokens** (10 records, last: 2025-08-05) - RECENT
19. **quotation_infos** (13 records, last: 2025-12-03) - RECENT
20. **application_document_lists** (14 records, last: 2025-09-29) - RECENT

---

## ✅ POTENTIALLY SAFE TO REMOVE (Need Further Verification)

These tables have very old data and may be unused, but need codebase verification:

21. **taxes** (1 record, last: 2022-07-02)
    - ⚠️ Check if used in tax calculations

22. **fee_options** (1 record, last: 2022-11-06)
    - ⚠️ Check if used in fee management

23. **fee_option_types** (1 record, last: 2022-11-06)
    - ⚠️ Check if used in fee management

24. **service_fee_option_types** (1 record, last: 2022-11-19)
    - ⚠️ Check if used in service fees

25. **service_fee_options** (1 record, last: 2022-11-19)
    - ⚠️ Check if used in service fees

26. **settings** (1 record, last: 1970-01-01 - invalid date)
    - ⚠️ Check if used (different from website_settings)

27. **check_partners** (3 records, no timestamp)
    - ⚠️ Check if used in import/check functionality

28. **product_area_levels** (3 records, last: 2022-07-14)
    - ⚠️ Check if used in product management

29. **fee_types** (3 records, last: 2024-01-03)
    - ⚠️ Check if used in fee management

30. **profiles** (3 records, last: 2024-03-05)
    - ⚠️ Check if used in profile management

31. **academic_requirements** (4 records, last: 2022-07-14)
    - ⚠️ Check if used in application process

32. **online_forms** (4 records, last: 2024-08-12)
    - ⚠️ Check if used in form functionality

33. **teams** (5 records, last: 2023-02-07)
    - ⚠️ Check if used in team management

34. **currencies** (6 records, last: 2021-08-27)
    - ⚠️ Check if used in financial calculations

35. **sub_categories** (6 records, last: 2022-08-16)
    - ⚠️ Check if used in categorization

36. **invoice_schedules** (6 records, last: 2024-08-24)
    - ⚠️ Check if used in invoice scheduling

37. **tax_rates** (7 records, last: 2021-07-13)
    - ⚠️ Check if used in tax calculations

38. **product_types** (8 records, last: 2022-11-18)
    - ⚠️ Check if used in product management

39. **checklists** (8 records, last: 2023-01-13)
    - ⚠️ Check if used in checklist functionality

40. **promotions** (10 records, last: 2024-02-24)
    - ⚠️ Check if used in promotion management

41. **followup_types** (11 records, no timestamp)
    - ⚠️ Check if used in followup management

42. **subject_areas** (14 records, no timestamp)
    - ⚠️ Check if used in subject management

43. **schedule_items** (16 records, last: 2024-08-24)
    - ⚠️ Check if used in scheduling

---

## Recommendation

**Immediate Action:** Keep all tables for now. Most are reference/lookup tables that are still needed.

**Next Steps:**
1. Verify each "potentially safe to remove" table by checking:
   - Direct database queries
   - Model relationships
   - Foreign key constraints
   - Controller usage

2. Consider archiving old data instead of dropping tables if they're reference tables.

3. Focus on tables with invalid dates (settings) or no timestamps for deeper investigation.

