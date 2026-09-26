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
     * Phase 7 client detail indexes (see docs/POSTGRES_PERFORMANCE_ROLLOUT_PLAN.md §7.2).
     * Child tables already indexed from Phase 1 / dashboard migrations; this adds the
     * notes tab partial index used by ClientDetailNotes.
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
                'table' => 'notes',
                'name' => 'notes_client_detail_list_idx',
                'postgres' => "CREATE INDEX CONCURRENTLY IF NOT EXISTS notes_client_detail_list_idx ON notes (client_id, pin DESC, created_at DESC) WHERE type = 'client' AND assigned_to IS NULL AND task_group IS NULL",
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
