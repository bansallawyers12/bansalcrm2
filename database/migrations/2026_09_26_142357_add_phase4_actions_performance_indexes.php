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
     * Phase 4 actions indexes (see docs/POSTGRES_PERFORMANCE_ROLLOUT_PLAN.md).
     * Indexes only — list filters and totals stay unchanged.
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
                'table' => 'notes',
                'name' => 'notes_assigned_to_status_assign_date_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS notes_assigned_to_status_assign_date_idx ON notes (assigned_to, status, action_assign_date)',
                'columns' => ['assigned_to', 'status', 'action_assign_date'],
            ],
            [
                'table' => 'notes',
                'name' => 'notes_user_open_actions_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS notes_user_open_actions_idx ON notes (user_id, is_action, status, type, created_at DESC) WHERE is_action = 1 AND status <> 1',
                'columns' => ['user_id', 'is_action', 'status', 'type', 'created_at'],
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
