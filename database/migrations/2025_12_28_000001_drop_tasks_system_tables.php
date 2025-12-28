<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tasks system was removed in December 2025 as it was inactive since August 2024.
     * The new system uses the 'notes' and 'activities_logs' tables instead.
     * 
     * Tables being dropped:
     * - tasks (34 records as of Dec 2025)
     * - task_logs (117 records as of Dec 2025)
     * - to_do_groups (5 records as of Dec 2025)
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('task_logs');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('to_do_groups');
    }

    /**
     * Reverse the migrations.
     *
     * Note: This will recreate the table structure but not restore the data.
     * If you need to restore data, you must use a database backup.
     *
     * @return void
     */
    public function down()
    {
        // Create tasks table
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->nullable(); // 'client' or 'partner'
            $table->integer('status')->default(0);
            $table->integer('priority')->default(0);
            $table->date('due_date')->nullable();
            $table->timestamps();
            
            $table->index('client_id');
            $table->index('user_id');
            $table->index('type');
            $table->index('status');
        });

        // Create task_logs table
        Schema::create('task_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('comment')->nullable();
            $table->string('action_type')->nullable();
            $table->timestamps();
            
            $table->index('task_id');
            $table->index('user_id');
        });

        // Create to_do_groups table
        Schema::create('to_do_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->index('created_by');
        });
    }
};

