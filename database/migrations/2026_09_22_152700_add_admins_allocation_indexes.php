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
     * Speed allocation lookups. Indexes do not change who is allocated.
     */
    public function up(): void
    {
        if (! Schema::hasTable('admins')) {
            return;
        }

        $this->createBtreeIndex('admins_user_id_idx', 'CREATE INDEX CONCURRENTLY IF NOT EXISTS admins_user_id_idx ON admins (user_id)', ['user_id']);
        $this->createBtreeIndex('admins_assignee_idx', 'CREATE INDEX CONCURRENTLY IF NOT EXISTS admins_assignee_idx ON admins (assignee)', ['assignee']);

        if (DB::getDriverName() !== 'pgsql' || Schema::hasIndex('admins', 'admins_assignee_trgm_idx')) {
            return;
        }

        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS admins_assignee_trgm_idx ON admins USING gin (assignee gin_trgm_ops)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('admins')) {
            return;
        }

        foreach (['admins_assignee_trgm_idx', 'admins_assignee_idx', 'admins_user_id_idx'] as $name) {
            $this->dropIndex($name);
        }
    }

    /**
     * @param  list<string>  $columns
     */
    private function createBtreeIndex(string $name, string $postgresSql, array $columns): void
    {
        if (Schema::hasIndex('admins', $name)) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement($postgresSql);

            return;
        }

        Schema::table('admins', function (Blueprint $table) use ($columns, $name): void {
            $table->index($columns, $name);
        });
    }

    private function dropIndex(string $name): void
    {
        if (! Schema::hasIndex('admins', $name)) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX CONCURRENTLY IF EXISTS '.$name);

            return;
        }

        Schema::table('admins', function (Blueprint $table) use ($name): void {
            $table->dropIndex($name);
        });
    }
};
