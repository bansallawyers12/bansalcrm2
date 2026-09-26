<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Concurrent index builds cannot run inside a transaction.
     *
     * @var bool
     */
    public $withinTransaction = false;

    /**
     * Phase 3 list-page indexes (see docs/POSTGRES_PERFORMANCE_ROLLOUT_PLAN.md).
     * Indexes only — list filters, totals, and type matching stay unchanged.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        }

        foreach ($this->definitions() as $index) {
            $this->createIndex(
                $index['table'],
                $index['name'],
                $index['postgres'],
                $index['columns'],
                $index['postgres_only'] ?? false,
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (array_reverse($this->definitions()) as $index) {
            $this->dropIndex($index['table'], $index['name']);
        }
    }

    /**
     * @return list<array{table: string, name: string, postgres: string, columns: list<string>, postgres_only?: bool}>
     */
    private function definitions(): array
    {
        return [
            [
                'table' => 'admins',
                'name' => 'admins_active_clients_list_idx',
                'postgres' => "CREATE INDEX CONCURRENTLY IF NOT EXISTS admins_active_clients_list_idx ON admins (id DESC) WHERE is_archived = 0 AND (is_deleted IS NULL OR is_deleted = 0) AND LOWER(type) = 'client'",
                'columns' => ['id'],
            ],
            [
                'table' => 'admins',
                'name' => 'admins_active_leads_list_idx',
                'postgres' => "CREATE INDEX CONCURRENTLY IF NOT EXISTS admins_active_leads_list_idx ON admins (id DESC) WHERE type = 'lead' AND converted = false AND is_archived = 0 AND (is_deleted IS NULL OR is_deleted = 0)",
                'columns' => ['id'],
            ],
            [
                'table' => 'admins',
                'name' => 'admins_archived_list_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS admins_archived_list_idx ON admins (id DESC) WHERE is_archived = 1 AND (is_deleted IS NULL OR is_deleted = 0)',
                'columns' => ['id'],
            ],
            [
                'table' => 'admins',
                'name' => 'admins_archived_on_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS admins_archived_on_idx ON admins (archived_on DESC) WHERE is_archived = 1',
                'columns' => ['archived_on'],
            ],
            [
                'table' => 'admins',
                'name' => 'admins_first_name_trgm_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS admins_first_name_trgm_idx ON admins USING gin (first_name gin_trgm_ops)',
                'columns' => ['first_name'],
                'postgres_only' => true,
            ],
            [
                'table' => 'admins',
                'name' => 'admins_last_name_trgm_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS admins_last_name_trgm_idx ON admins USING gin (last_name gin_trgm_ops)',
                'columns' => ['last_name'],
                'postgres_only' => true,
            ],
            [
                'table' => 'admins',
                'name' => 'admins_email_trgm_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS admins_email_trgm_idx ON admins USING gin (email gin_trgm_ops)',
                'columns' => ['email'],
                'postgres_only' => true,
            ],
            [
                'table' => 'admins',
                'name' => 'admins_full_name_trgm_idx',
                'postgres' => "CREATE INDEX CONCURRENTLY IF NOT EXISTS admins_full_name_trgm_idx ON admins USING gin ((COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) gin_trgm_ops)",
                'columns' => ['first_name', 'last_name'],
                'postgres_only' => true,
            ],
        ];
    }

    /**
     * @param  list<string>  $columns
     */
    private function createIndex(string $table, string $name, string $postgresSql, array $columns, bool $postgresOnly = false): void
    {
        if (! Schema::hasTable($table) || Schema::hasIndex($table, $name)) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement($postgresSql);

            return;
        }

        if ($postgresOnly) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($columns, $name): void {
            $tableBlueprint->index($columns, $name);
        });
    }

    private function dropIndex(string $table, string $name): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasIndex($table, $name)) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX CONCURRENTLY IF EXISTS '.$name);

            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($name): void {
            $tableBlueprint->dropIndex($name);
        });
    }
};
