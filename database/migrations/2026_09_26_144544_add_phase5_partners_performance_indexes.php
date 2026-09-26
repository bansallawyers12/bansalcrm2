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
     * Phase 5 partners indexes (see docs/POSTGRES_PERFORMANCE_ROLLOUT_PLAN.md).
     * Indexes only — list filters, sorting, and totals stay unchanged.
     */
    public function up(): void
    {
        foreach ($this->definitions() as $index) {
            $this->createIndex(
                $index['table'],
                $index['name'],
                $index['postgres'],
                $index['columns'],
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
     * @return list<array{table: string, name: string, postgres: string, columns: list<string>}>
     */
    private function definitions(): array
    {
        return [
            [
                'table' => 'partners',
                'name' => 'partners_status_created_at_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS partners_status_created_at_idx ON partners (status, created_at DESC)',
                'columns' => ['status', 'created_at'],
            ],
            [
                'table' => 'notes',
                'name' => 'notes_partner_list_note_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS notes_partner_list_note_idx ON notes (client_id, pin DESC, created_at DESC) WHERE type = \'partner\' AND assigned_to IS NULL AND task_group IS NULL',
                'columns' => ['client_id', 'pin', 'created_at'],
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
