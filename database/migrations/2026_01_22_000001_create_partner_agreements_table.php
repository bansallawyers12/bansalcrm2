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
        // Create partner_agreements table
        Schema::create('partner_agreements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_id');
            $table->date('contract_start')->nullable();
            $table->date('contract_expiry')->nullable();
            $table->text('represent_region')->nullable();
            $table->decimal('commission_percentage', 10, 2)->nullable();
            $table->decimal('bonus', 10, 2)->nullable()->comment('Bonus amount for this agreement');
            $table->text('description')->nullable()->comment('Agreement description/notes');
            $table->boolean('gst')->default(0);
            $table->unsignedBigInteger('default_super_agent')->nullable();
            $table->text('file_upload')->nullable()->comment('S3 URL of agreement document');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            // Add foreign key constraint
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            
            // Add index for better query performance
            $table->index('partner_id');
            $table->index('status');
        });
        
        // Migrate existing agreement data from partners table to partner_agreements table
        $this->migrateExistingAgreements();
    }

    /**
     * Migrate existing agreement data from partners table
     *
     * @return void
     */
    private function migrateExistingAgreements()
    {
        // Get all partners that have agreement data
        $partners = DB::table('partners')
            ->whereNotNull('contract_start')
            ->orWhereNotNull('contract_expiry')
            ->orWhereNotNull('commission_percentage')
            ->orWhereNotNull('file_upload')
            ->get();
        
        foreach ($partners as $partner) {
            // Only migrate if there's actual agreement data
            if ($partner->contract_start || $partner->contract_expiry || $partner->commission_percentage) {
                DB::table('partner_agreements')->insert([
                    'partner_id' => $partner->id,
                    'contract_start' => $partner->contract_start,
                    'contract_expiry' => $partner->contract_expiry,
                    'represent_region' => $partner->represent_region,
                    'commission_percentage' => $partner->commission_percentage,
                    'bonus' => null, // New field, no existing data
                    'description' => null, // New field, no existing data
                    'gst' => $partner->gst ?? 0,
                    'default_super_agent' => $partner->default_super_agent,
                    'file_upload' => $partner->file_upload,
                    'status' => 'active', // Set existing agreements as active
                    'created_at' => $partner->created_at ?? now(),
                    'updated_at' => $partner->updated_at ?? now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_agreements');
    }
};
