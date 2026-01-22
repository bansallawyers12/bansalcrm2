<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_default')->default(false)->comment('True for General category visible to all');
            $table->unsignedBigInteger('user_id')->nullable()->comment('NULL for default categories, specific user ID for user-created categories');
            $table->unsignedBigInteger('client_id')->nullable()->comment('NULL for global categories, specific client ID for client-specific categories');
            $table->boolean('status')->default(1)->comment('1 = active, 0 = inactive');
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['client_id', 'status']);
            $table->index(['is_default', 'status']);
        });

        // Insert default "General" category
        DB::table('document_categories')->insert([
            'name' => 'General',
            'is_default' => true,
            'user_id' => null,
            'client_id' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_categories');
    }
};
