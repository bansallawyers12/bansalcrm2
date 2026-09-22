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
     * Speed the dashboard My Day lookups. Indexes do not change stored rows or query results.
     */
    public function up(): void
    {
        foreach ($this->definitions() as $index) {
            $this->createIndex($index['table'], $index['name'], $index['postgres'], $index['columns']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->definitions() as $index) {
            $this->dropIndex($index['table'], $index['name']);
        }
    }

    /**
     * @return list<array{table: string, name: string, postgres: string, columns: list<string>}>
     */
    private function definitions(): array
    {
        return [
            [
                'table' => 'activities_logs',
                'name' => 'activities_logs_created_by_client_created_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS activities_logs_created_by_client_created_idx ON activities_logs (created_by, client_id, created_at)',
                'columns' => ['created_by', 'client_id', 'created_at'],
            ],
            [
                'table' => 'activities_logs',
                'name' => 'activities_logs_created_by_created_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS activities_logs_created_by_created_idx ON activities_logs (created_by, created_at)',
                'columns' => ['created_by', 'created_at'],
            ],
            [
                'table' => 'notes',
                'name' => 'notes_user_client_created_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS notes_user_client_created_idx ON notes (user_id, client_id, created_at)',
                'columns' => ['user_id', 'client_id', 'created_at'],
            ],
            [
                'table' => 'notes',
                'name' => 'notes_open_actions_assign_date_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS notes_open_actions_assign_date_idx ON notes (is_action, type, action_assign_date) WHERE action_assign_date IS NOT NULL AND status <> 1',
                'columns' => ['is_action', 'type', 'action_assign_date'],
            ],
            [
                'table' => 'application_activities_logs',
                'name' => 'application_activities_logs_user_created_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS application_activities_logs_user_created_idx ON application_activities_logs (user_id, created_at)',
                'columns' => ['user_id', 'created_at'],
            ],
            [
                'table' => 'emails',
                'name' => 'emails_user_client_created_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS emails_user_client_created_idx ON emails (user_id, client_id, created_at)',
                'columns' => ['user_id', 'client_id', 'created_at'],
            ],
            [
                'table' => 'documents',
                'name' => 'documents_user_client_created_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS documents_user_client_created_idx ON documents (user_id, client_id, created_at)',
                'columns' => ['user_id', 'client_id', 'created_at'],
            ],
            [
                'table' => 'documents',
                'name' => 'documents_created_by_client_created_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS documents_created_by_client_created_idx ON documents (created_by, client_id, created_at)',
                'columns' => ['created_by', 'client_id', 'created_at'],
            ],
        ];
    }

    /**
     * @param  list<string>  $columns
     */
    private function createIndex(string $table, string $name, string $postgresSql, array $columns): void
    {
        if (! Schema::hasTable($table) || Schema::hasIndex($table, $name)) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement($postgresSql);

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
