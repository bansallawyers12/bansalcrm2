<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('emails')) {
            return;
        }

        Schema::table('emails', function (Blueprint $table) {
            if (! Schema::hasColumn('emails', 'delivery_status')) {
                $table->string('delivery_status', 32)->nullable()->after('message_id');
            }
            if (! Schema::hasColumn('emails', 'delivery_status_at')) {
                $table->timestamp('delivery_status_at')->nullable()->after('delivery_status');
            }
            if (! Schema::hasColumn('emails', 'delivery_detail')) {
                $table->json('delivery_detail')->nullable()->after('delivery_status_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('emails')) {
            return;
        }

        Schema::table('emails', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('emails', 'delivery_detail')) {
                $cols[] = 'delivery_detail';
            }
            if (Schema::hasColumn('emails', 'delivery_status_at')) {
                $cols[] = 'delivery_status_at';
            }
            if (Schema::hasColumn('emails', 'delivery_status')) {
                $cols[] = 'delivery_status';
            }
            if ($cols !== []) {
                $table->dropColumn($cols);
            }
        });
    }
};
