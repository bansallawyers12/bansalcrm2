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
     * Speed signature dashboard filters (status tabs, archived exclusion).
     */
    public function up(): void
    {
        foreach ($this->definitions() as $index) {
            $this->createIndex($index['table'], $index['name'], $index['postgres'], $index['columns']);
        }
    }

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
                'table' => 'documents',
                'name' => 'documents_signature_status_created_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS documents_signature_status_created_idx ON documents (status, created_at DESC) WHERE created_by IS NOT NULL AND archived_at IS NULL',
                'columns' => ['status', 'created_at'],
            ],
            [
                'table' => 'documents',
                'name' => 'documents_signature_created_by_status_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS documents_signature_created_by_status_idx ON documents (created_by, status) WHERE created_by IS NOT NULL AND archived_at IS NULL',
                'columns' => ['created_by', 'status'],
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
