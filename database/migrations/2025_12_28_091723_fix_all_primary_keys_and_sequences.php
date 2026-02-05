<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * List of tables that need primary key fixes
     * These are tables that have PRIMARY KEY in MySQL but missing in PostgreSQL
     */
    private $tablesToFix = [
        'account_client_receipts',
        'activities_logs',
        'admins',
        'agents',
        'api_tokens',
        'application_activities_logs',
        'application_document_lists',
        'application_documents',
        'application_fee_option_types',
        'application_fee_options',
        'application_notes',
        'applications',
        'attach_files',
        'attachments',
        'branches',
        'categories',
        'check_applications',
        'check_partners',
        'check_products',
        // 'checkin_histories', // Removed - table dropped
        'checkin_logs',
        'checklists',
        'cities',
        'client_phones',
        'client_service_takens',
        'contacts',
        'countries',
        'crm_email_templates',
        'currencies',
        'document_checklists',
        'documents',
        'email_templates',
        'emails',
        // 'enquiries', // Removed - table dropped (enquiries feature removed)
        // 'fee_option_types', // Removed - table dropped (no data since 2022)
        // 'fee_options', // Removed - table dropped (no data since 2022)
        // 'fee_types', // Removed - Fee Type feature removed; static "Tution Fees" used in products
        'followup_types',
        'followups',
        'income_sharings',
        'invoice_details',
        'invoice_followups',
        'invoice_payments',
        // 'invoice_schedules', // Removed - table dropped
        'invoices',
        'items',
        'lead_services',
        'leads',
        'mail_reports',
        'migrations',
        'notes',
        'notifications',
        'partner_branches',
        'partner_emails',
        'partner_phones',
        'partner_student_invoices',
        'partner_types',
        'partners',
        'password_reset_links',
        'personal_access_tokens',
        'postcode_ranges',
        'product_area_levels',
        'product_types',
        'products',
        'profiles',
        'promotions',
        'representing_partners',
        // 'schedule_items', // Removed - table dropped
        // 'service_fee_option_types', // Removed - table dropped
        // 'service_fee_options', // Removed - table dropped
        // 'services', // Removed - table dropped
        // 'settings', // Removed - table dropped (only 1 record with invalid date 1970-01-01)
        'share_invoices',
        'sources',
        'states',
        'sub_categories',
        // 'subject_areas', // Removed - table dropped
        // 'subjects', // Removed - table dropped
        // 'suburbs', // Removed - table dropped
        'tags',
        'task_logs',
        'tasks',
        'tax_rates',
        'taxes',
        'teams',
        'test_scores',
        'to_do_groups',
        'upload_checklists',
        'user_logs',
        'user_roles',
        'user_types',
        'users',
        'verified_numbers',
        'visa_types',
        'website_settings',
        'workflow_stages',
        'workflows',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only proceed if using PostgreSQL
        if (DB::getDriverName() !== 'pgsql') {
            echo "Skipping migration: Not using PostgreSQL\n";
            return;
        }

        $fixed = 0;
        $skipped = 0;
        $errors = [];

        foreach ($this->tablesToFix as $tableName) {
            // Process each table in its own transaction to avoid aborting all on one error
            try {
                $result = DB::transaction(function () use ($tableName) {
                    // Check if table exists
                    $tableExists = DB::selectOne("
                        SELECT EXISTS(
                            SELECT 1 FROM information_schema.tables 
                            WHERE table_name = '{$tableName}'
                        ) as exists
                    ");

                    if (!$tableExists->exists) {
                        return 'skipped'; // Table doesn't exist
                    }

                    // Check if primary key already exists
                    $pkExists = DB::selectOne("
                        SELECT EXISTS(
                            SELECT 1 FROM information_schema.table_constraints 
                            WHERE table_name = '{$tableName}' 
                            AND constraint_type = 'PRIMARY KEY'
                        ) as exists
                    ");

                    if ($pkExists->exists) {
                        return 'skipped'; // PK already exists
                    }

                    // Check if id column exists
                    $idColumnExists = DB::selectOne("
                        SELECT EXISTS(
                            SELECT 1 FROM information_schema.columns 
                            WHERE table_name = '{$tableName}' 
                            AND column_name = 'id'
                        ) as exists
                    ");

                    if (!$idColumnExists->exists) {
                        return 'skipped'; // id column doesn't exist
                    }

                    // Check for duplicate IDs before creating primary key
                    $duplicates = DB::selectOne("
                        SELECT COUNT(*) as count
                        FROM (
                            SELECT id, COUNT(*) as cnt
                            FROM {$tableName}
                            WHERE id IS NOT NULL
                            GROUP BY id
                            HAVING COUNT(*) > 1
                        ) as dup
                    ");

                    if ($duplicates && $duplicates->count > 0) {
                        throw new \Exception("Cannot create primary key: {$duplicates->count} duplicate ID(s) found");
                    }

                    $sequenceName = "{$tableName}_id_seq";

                    // Get current max id value (if any records exist)
                    $maxId = DB::table($tableName)->max('id');
                    $startValue = $maxId ? ($maxId + 1) : 1;

                    // Check if sequence already exists
                    $sequenceExists = DB::selectOne("
                        SELECT EXISTS(
                            SELECT 1 FROM pg_sequences 
                            WHERE sequencename = '{$sequenceName}'
                        ) as exists
                    ");

                    if (!$sequenceExists->exists) {
                        // Create the sequence
                        DB::statement("CREATE SEQUENCE {$sequenceName} START WITH {$startValue}");
                    } else {
                        // Sequence exists, but make sure it's at least at max id + 1
                        $currentValue = DB::selectOne("SELECT last_value FROM {$sequenceName}");
                        if ($currentValue && $currentValue->last_value < $maxId) {
                            DB::statement("SELECT setval('{$sequenceName}', {$startValue}, false)");
                        }
                    }

                    // Drop old CHECK constraints on id column if they exist
                    $checkConstraints = DB::select("
                        SELECT constraint_name 
                        FROM information_schema.table_constraints 
                        WHERE table_name = '{$tableName}' 
                        AND constraint_type = 'CHECK'
                        AND constraint_name LIKE '%id%not%null%'
                    ");

                    foreach ($checkConstraints as $constraint) {
                        try {
                            DB::statement("ALTER TABLE {$tableName} DROP CONSTRAINT IF EXISTS {$constraint->constraint_name}");
                        } catch (\Exception $e) {
                            // Ignore errors when dropping constraints
                        }
                    }

                    // Set the default value for id column to use the sequence
                    DB::statement("
                        ALTER TABLE {$tableName} 
                        ALTER COLUMN id SET DEFAULT nextval('{$sequenceName}'::regclass)
                    ");

                    // Add primary key constraint
                    DB::statement("
                        ALTER TABLE {$tableName} 
                        ADD CONSTRAINT {$tableName}_pkey PRIMARY KEY (id)
                    ");

                    // Make sure the sequence is owned by the column (important for auto-increment)
                    DB::statement("
                        ALTER SEQUENCE {$sequenceName} OWNED BY {$tableName}.id
                    ");

                    return 'fixed';
                });
                
                if ($result === 'skipped') {
                    $skipped++;
                } elseif ($result === 'fixed') {
                    $fixed++;
                }
                
            } catch (\Exception $e) {
                $errors[$tableName] = $e->getMessage();
            }
        }

        // Log results
        echo "Primary Key Fix Summary:\n";
        echo "  - Fixed: {$fixed} tables\n";
        echo "  - Skipped: {$skipped} tables\n";
        echo "  - Errors: " . count($errors) . " tables\n";
        
        if (count($errors) > 0) {
            echo "\nErrors:\n";
            foreach ($errors as $table => $error) {
                echo "  - {$table}: {$error}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Only proceed if using PostgreSQL
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->tablesToFix as $tableName) {
            try {
                // Check if table exists
                $tableExists = DB::selectOne("
                    SELECT EXISTS(
                        SELECT 1 FROM information_schema.tables 
                        WHERE table_name = '{$tableName}'
                    ) as exists
                ");

                if (!$tableExists->exists) {
                    continue;
                }

                // Drop primary key constraint
                DB::statement("
                    ALTER TABLE {$tableName} 
                    DROP CONSTRAINT IF EXISTS {$tableName}_pkey
                ");

                // Remove default value
                DB::statement("
                    ALTER TABLE {$tableName} 
                    ALTER COLUMN id DROP DEFAULT
                ");

                // Drop the sequence
                $sequenceName = "{$tableName}_id_seq";
                DB::statement("DROP SEQUENCE IF EXISTS {$sequenceName}");

            } catch (\Exception $e) {
                // Continue with other tables even if one fails
                continue;
            }
        }
    }
};
