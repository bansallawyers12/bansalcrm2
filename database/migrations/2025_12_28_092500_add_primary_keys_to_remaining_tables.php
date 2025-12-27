<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that need primary keys added (duplicates have been removed)
     */
    private $tablesToFix = ['agents', 'leads', 'tasks'];

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
            try {
                // Process each table in its own transaction
                $result = DB::transaction(function () use ($tableName) {
                    // Check if table exists
                    $tableExists = DB::selectOne("
                        SELECT EXISTS(
                            SELECT 1 FROM information_schema.tables 
                            WHERE table_name = '{$tableName}'
                        ) as exists
                    ");

                    if (!$tableExists->exists) {
                        return 'skipped';
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
                        return 'skipped';
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
                        return 'skipped';
                    }

                    // Verify no duplicate IDs exist
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
                        throw new \Exception("Cannot create primary key: {$duplicates->count} duplicate ID(s) still exist");
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
                    echo "✅ {$tableName}: Primary key added\n";
                }
                
            } catch (\Exception $e) {
                $errors[$tableName] = $e->getMessage();
                echo "❌ {$tableName}: " . $e->getMessage() . "\n";
            }
        }

        echo "\nPrimary Key Addition Summary:\n";
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

