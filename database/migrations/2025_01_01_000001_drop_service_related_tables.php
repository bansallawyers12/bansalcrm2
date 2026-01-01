<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop service_fee_option_types first (child table)
        Schema::dropIfExists('service_fee_option_types');
        
        // Drop service_fee_options (child table)
        Schema::dropIfExists('service_fee_options');
        
        // Drop services (parent table)
        Schema::dropIfExists('services');
        
        // Drop settings table (unused - only 1 record with invalid date)
        Schema::dropIfExists('settings');
        
        // Drop tax_rates table (user-specific tax rates)
        Schema::dropIfExists('tax_rates');
        
        // Drop taxes table (system-wide tax codes)
        Schema::dropIfExists('taxes');
        
        echo "Dropped tables: service_fee_option_types, service_fee_options, services, settings, tax_rates, taxes\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Recreate services table
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->unsignedBigInteger('workflow')->nullable();
            $table->string('branch')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('intake_month')->nullable();
            $table->date('start_date')->nullable();
            $table->string('notes')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            
            // Add indexes if needed
            $table->index('user_id');
            $table->index('partner_id');
            $table->index('product_id');
        });
        
        // Recreate service_fee_options table
        Schema::create('service_fee_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('app_id')->nullable();
            $table->string('name')->nullable();
            $table->string('country')->nullable();
            $table->string('installment_type')->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('discount_sem', 10, 2)->nullable();
            $table->decimal('total_discount', 10, 2)->nullable();
            $table->timestamps();
            
            // Add indexes if needed
            $table->index('user_id');
            $table->index('app_id');
        });
        
        // Recreate service_fee_option_types table
        Schema::create('service_fee_option_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fee_id')->nullable();
            $table->string('fee_type')->nullable();
            $table->decimal('inst_amt', 10, 2)->nullable();
            $table->integer('installment')->nullable();
            $table->decimal('total_fee', 10, 2)->nullable();
            $table->string('claim_term')->nullable();
            $table->decimal('commission', 10, 2)->nullable();
            $table->timestamps();
            
            // Add indexes if needed
            $table->index('fee_id');
        });
        
        // Recreate settings table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('office_id')->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
            $table->string('date_format')->nullable();
            $table->string('time_format')->nullable();
        });
        
        // Recreate tax_rates table
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('rate', 10, 2)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
        });
        
        // Recreate taxes table
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->timestamps();
        });
        
        echo "Recreated tables: services, service_fee_options, service_fee_option_types, settings, tax_rates, taxes\n";
    }
};

