<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drops 7 unused columns from account_client_receipts table.
     */
    public function up(): void
    {
        if (!Schema::hasTable('account_client_receipts')) {
            return;
        }

        $columnsToDrop = [
            'agent_id',
            'gst_included',
            'payment_type',
            'deposit_amount_before_void',
            'withdrawal_amount',
            'invoice_no',
            'save_type',
        ];

        $toDrop = array_filter($columnsToDrop, fn (string $col) => Schema::hasColumn('account_client_receipts', $col));

        if (!empty($toDrop)) {
            Schema::table('account_client_receipts', function (Blueprint $table) use ($toDrop) {
                $table->dropColumn($toDrop);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('account_client_receipts')) {
            return;
        }

        Schema::table('account_client_receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('account_client_receipts', 'agent_id')) {
                $table->unsignedBigInteger('agent_id')->nullable()->after('client_id');
            }
            if (!Schema::hasColumn('account_client_receipts', 'gst_included')) {
                $table->boolean('gst_included')->nullable()->after('entry_date');
            }
            if (!Schema::hasColumn('account_client_receipts', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('gst_included');
            }
            if (!Schema::hasColumn('account_client_receipts', 'deposit_amount_before_void')) {
                $table->decimal('deposit_amount_before_void', 15, 2)->nullable()->after('deposit_amount');
            }
            if (!Schema::hasColumn('account_client_receipts', 'withdrawal_amount')) {
                $table->decimal('withdrawal_amount', 15, 2)->nullable()->after('deposit_amount_before_void');
            }
            if (!Schema::hasColumn('account_client_receipts', 'invoice_no')) {
                $table->string('invoice_no')->nullable()->after('withdrawal_amount');
            }
            if (!Schema::hasColumn('account_client_receipts', 'save_type')) {
                $table->string('save_type')->nullable()->after('invoice_no');
            }
        });
    }
};
