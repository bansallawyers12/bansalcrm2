<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that have duplicate IDs that need to be cleaned up
     */
    private $tablesToClean = ['agents', 'leads', 'tasks'];

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

        $cleaned = 0;
        $errors = [];

        foreach ($this->tablesToClean as $tableName) {
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

                // Get count of duplicates before cleanup
                $duplicateCount = DB::selectOne("
                    SELECT COUNT(*) as count
                    FROM (
                        SELECT id, COUNT(*) as cnt
                        FROM {$tableName}
                        WHERE id IS NOT NULL
                        GROUP BY id
                        HAVING COUNT(*) > 1
                    ) as dup
                ");

                if ($duplicateCount->count == 0) {
                    continue; // No duplicates
                }

                echo "Cleaning {$tableName}: Found {$duplicateCount->count} duplicate ID(s)\n";

                // Use PostgreSQL's ctid (system column) to identify specific rows
                // Delete duplicates, keeping the one with the minimum ctid (first inserted)
                // We'll use a subquery with ROW_NUMBER() for better control
                
                // First, check if table has updated_at or created_at for better selection
                $hasUpdatedAt = DB::selectOne("
                    SELECT EXISTS(
                        SELECT 1 FROM information_schema.columns 
                        WHERE table_name = '{$tableName}' 
                        AND column_name = 'updated_at'
                    ) as exists
                ");

                $orderBy = $hasUpdatedAt->exists ? 'updated_at DESC, ctid' : 'ctid';

                // Delete duplicates using a DELETE with subquery
                // We'll keep the record with the minimum ctid for each duplicate ID
                $deleted = DB::statement("
                    DELETE FROM {$tableName}
                    WHERE ctid IN (
                        SELECT ctid
                        FROM (
                            SELECT ctid,
                                   ROW_NUMBER() OVER (
                                       PARTITION BY id 
                                       ORDER BY {$orderBy}
                                   ) as rn
                            FROM {$tableName}
                            WHERE id IN (
                                SELECT id
                                FROM {$tableName}
                                WHERE id IS NOT NULL
                                GROUP BY id
                                HAVING COUNT(*) > 1
                            )
                        ) as ranked
                        WHERE rn > 1
                    )
                ");

                // Get count after cleanup
                $remainingDuplicates = DB::selectOne("
                    SELECT COUNT(*) as count
                    FROM (
                        SELECT id, COUNT(*) as cnt
                        FROM {$tableName}
                        WHERE id IS NOT NULL
                        GROUP BY id
                        HAVING COUNT(*) > 1
                    ) as dup
                ");

                if ($remainingDuplicates->count == 0) {
                    echo "✅ {$tableName}: All duplicates removed\n";
                    $cleaned++;
                } else {
                    echo "⚠️  {$tableName}: {$remainingDuplicates->count} duplicate ID(s) still remain\n";
                    $errors[$tableName] = "{$remainingDuplicates->count} duplicates still remain";
                }

            } catch (\Exception $e) {
                $errors[$tableName] = $e->getMessage();
                echo "❌ {$tableName}: Error - " . $e->getMessage() . "\n";
            }
        }

        echo "\nDuplicate Cleanup Summary:\n";
        echo "  - Cleaned: {$cleaned} tables\n";
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
        // Cannot reverse duplicate removal - data is permanently deleted
        // This migration should not be rolled back
        echo "Warning: Cannot reverse duplicate removal. Data has been permanently deleted.\n";
    }
};
