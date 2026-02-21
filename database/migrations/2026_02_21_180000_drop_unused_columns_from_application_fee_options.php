<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drops 9 unused columns from application_fee_options table.
     */
    public function up(): void
    {
        if (!Schema::hasTable('application_fee_options')) {
            return;
        }

        $columnsToDrop = [
            'name',
            'country',
            'installment_type',
            'discount_amount',
            'discount_sem',
            'total_discount',
            'total_anticipated_fee',
            'commission_as_per_anticipated_fee',
            'bonus_paid',
        ];

        Schema::table('application_fee_options', function (Blueprint $table) use ($columnsToDrop) {
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('application_fee_options', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('application_fee_options')) {
            return;
        }

        Schema::table('application_fee_options', function (Blueprint $table) {
            if (!Schema::hasColumn('application_fee_options', 'name')) {
                $table->string('name')->nullable()->after('app_id');
            }
            if (!Schema::hasColumn('application_fee_options', 'country')) {
                $table->string('country')->nullable()->after('name');
            }
            if (!Schema::hasColumn('application_fee_options', 'installment_type')) {
                $table->string('installment_type')->nullable()->after('country');
            }
            if (!Schema::hasColumn('application_fee_options', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->nullable()->after('updated_at');
            }
            if (!Schema::hasColumn('application_fee_options', 'discount_sem')) {
                $table->decimal('discount_sem', 10, 2)->nullable()->after('discount_amount');
            }
            if (!Schema::hasColumn('application_fee_options', 'total_discount')) {
                $table->decimal('total_discount', 10, 2)->nullable()->after('discount_sem');
            }
            if (!Schema::hasColumn('application_fee_options', 'bonus_paid')) {
                $table->decimal('bonus_paid', 10, 2)->nullable()->after('bonus_pending_amount');
            }
            if (!Schema::hasColumn('application_fee_options', 'total_anticipated_fee')) {
                $table->decimal('total_anticipated_fee', 10, 2)->nullable()->after('bonus_paid');
            }
            if (!Schema::hasColumn('application_fee_options', 'commission_as_per_anticipated_fee')) {
                $table->decimal('commission_as_per_anticipated_fee', 10, 2)->nullable()->after('fee_reported_by_college');
            }
        });
    }
};
