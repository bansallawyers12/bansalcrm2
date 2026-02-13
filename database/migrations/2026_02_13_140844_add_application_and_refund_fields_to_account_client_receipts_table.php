<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('account_client_receipts', function (Blueprint $table) {
            $table->unsignedBigInteger('application_id')->nullable()->after('client_id')
                ->comment('Optional link to applications – which course/service this payment is for');
            $table->unsignedBigInteger('parent_receipt_id')->nullable()->after('receipt_type')
                ->comment('For refunds: FK to original receipt id');
            $table->text('refund_reason')->nullable()->after('deposit_amount')
                ->comment('Required when receipt_type = 2 (Refund)');
            $table->text('reassignment_reason')->nullable()->after('refund_reason')
                ->comment('Required when application_id is changed (e.g. transfer to migration)');

            $table->foreign('application_id')->references('id')->on('applications')->onDelete('set null');
            $table->foreign('parent_receipt_id')->references('id')->on('account_client_receipts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_client_receipts', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropForeign(['parent_receipt_id']);
            $table->dropColumn(['application_id', 'parent_receipt_id', 'refund_reason', 'reassignment_reason']);
        });
    }
};
