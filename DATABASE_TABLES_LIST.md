# Database Tables List

This document contains a complete list of all database tables in the Bansal CRM system.

**Total Tables: 70**

## Table List

| # | Table Name | Description |
|---|------------|-------------|
| 1 | account_client_receipts | Client receipt accounts |
| 2 | activities_logs | System activity logs |
| 3 | admins | Admin users table |
| 4 | agents | Agent records |
| 5 | application_activities_logs | Application activity logs |
| 6 | application_document_lists | Application document lists |
| 7 | application_documents | Application documents |
| 8 | application_fee_option_types | Application fee option types |
| 9 | application_fee_options | Application fee options |
| 10 | applications | Student applications |
| 11 | branches | Office branches |
| 12 | categories | Service/product categories |
| 13 | checkin_histories | Check-in history records |
| 14 | checkin_logs | Check-in log entries |
| 15 | checklists | Document checklists |
| 16 | client_phones | Client phone numbers |
| 17 | client_service_takens | Client services taken |
| 18 | contacts | Contact records |
| 19 | countries | Country reference data |
| 20 | crm_email_templates | CRM email templates |
| 21 | document_checklists | Document checklist items |
| 22 | documents | Document records |
| 23 | education | Education records |
| 24 | email_templates | Email templates |
| 25 | emails | Email records |
| 26 | fee_types | Fee type definitions |
| 27 | followup_types | Follow-up type definitions |
| 28 | followups | Follow-up records |
| 29 | income_sharings | Income sharing records |
| 30 | interested_services | Client interested services |
| 31 | invoice_details | Invoice detail line items |
| 32 | invoice_payments | Invoice payment records |
| 33 | invoice_schedules | Invoice schedule records |
| 34 | invoices | Invoice records |
| 35 | lead_services | Lead service records |
| 36 | leads | Lead records |
| 37 | mail_reports | Mail report records |
| 38 | migrations | Laravel migration tracking |
| 39 | notes | Note records |
| 40 | notifications | System notifications |
| 41 | partner_branches | Partner branch associations |
| 42 | partner_emails | Partner email addresses |
| 43 | partner_phones | Partner phone numbers |
| 44 | partner_student_invoices | Partner student invoices |
| 45 | partner_types | Partner type definitions |
| 46 | partners | Partner records |
| 47 | product_types | Product type definitions |
| 48 | products | Product records |
| 49 | profiles | User profiles |
| 50 | promotions | Promotion records |
| 51 | schedule_items | Schedule item records |
| 52 | sessions | Laravel session storage |
| 53 | share_invoices | Shared invoice records |
| 54 | sources | Lead/client sources |
| 55 | sub_categories | Sub-category records |
| 56 | subject_areas | Subject area definitions |
| 57 | subjects | Subject records |
| 58 | suburbs | Suburb/location data |
| 59 | tags | Tag records |
| 60 | teams | Team records |
| 61 | test_scores | Test score records |
| 62 | upload_checklists | Upload checklist records |
| 63 | user_logs | User activity logs |
| 64 | user_roles | User role definitions |
| 65 | user_types | User type definitions |
| 66 | verified_numbers | Verified phone numbers |
| 67 | verify_users | User verification records |
| 68 | visa_types | Visa type definitions |
| 69 | workflow_stages | Workflow stage definitions |
| 70 | workflows | Workflow records |

## Table Categories

### Core Tables
- `admins` - Admin users
- `agents` - Agent records
- `partners` - Partner records
- `leads` - Lead records
- `applications` - Application records

### Client Management
- `client_phones` - Client phone numbers
- `client_service_takens` - Client services
- `contacts` - Contact records
- `profiles` - User profiles

### Application Management
- `applications` - Main application table
- `application_documents` - Application documents
- `application_activities_logs` - Application activity logs
- `application_fee_options` - Fee options
- `application_fee_option_types` - Fee option types

### Financial
- `invoices` - Invoice records
- `invoice_details` - Invoice line items
- `invoice_payments` - Payment records
- `invoice_schedules` - Payment schedules
- `account_client_receipts` - Receipt records
- `income_sharings` - Income sharing

### Communication
- `emails` - Email records
- `email_templates` - Email templates
- `crm_email_templates` - CRM templates
- `notifications` - System notifications
- `mail_reports` - Mail reports

### Reference Data
- `countries` - Countries
- `suburbs` - Suburbs/locations
- `categories` - Categories
- `sub_categories` - Sub-categories
- `sources` - Lead sources
- `visa_types` - Visa types
- `fee_types` - Fee types
- `followup_types` - Follow-up types
- `partner_types` - Partner types
- `product_types` - Product types
- `user_roles` - User roles
- `user_types` - User types

### Documents & Checklists
- `documents` - Document records
- `checklists` - Checklist definitions
- `document_checklists` - Document checklist items
- `upload_checklists` - Upload checklists

### System Tables
- `migrations` - Laravel migrations
- `sessions` - Session storage
- `activities_logs` - Activity logs
- `user_logs` - User logs
- `checkin_logs` - Check-in logs
- `checkin_histories` - Check-in history

### Workflow & Process
- `workflows` - Workflow definitions
- `workflow_stages` - Workflow stages
- `followups` - Follow-up records
- `notes` - Note records
- `schedule_items` - Schedule items

### Products & Services
- `products` - Product records
- `services` - Service records (if exists)
- `interested_services` - Client interested services
- `lead_services` - Lead services

### Partners & Branches
- `partners` - Partner records
- `partner_branches` - Partner branches
- `partner_emails` - Partner emails
- `partner_phones` - Partner phones
- `partner_student_invoices` - Partner invoices
- `branches` - Office branches

### Other
- `tags` - Tagging system
- `teams` - Team records
- `promotions` - Promotions
- `test_scores` - Test scores
- `education` - Education records
- `subjects` - Subject records
- `subject_areas` - Subject areas
- `verified_numbers` - Verified numbers
- `verify_users` - User verification
- `share_invoices` - Shared invoices

## Notes

- This list is based on the PostgreSQL database schema
- Some tables may have been dropped in recent migrations (see `database/migrations/` folder)
- Table names follow Laravel conventions (plural, snake_case)
- The `migrations` and `sessions` tables are Laravel framework tables

## Last Updated
Generated on: 2025-01-XX
Database: PostgreSQL
Total Tables: 70

