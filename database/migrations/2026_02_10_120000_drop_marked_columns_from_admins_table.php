<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropMarkedColumnsFromAdminsTable extends Migration
{
    protected $columnsToDrop = [
        'default_email_id',
        'primary_email',
        'profile_img',
        'rating',
        'preferredintake',
        'applications',
        'followers',
        'is_greview_mail_sent',
        'gst_no',
        'gstin',
        'gst_date',
        'is_business_gst',
        'lead_status',
        'followup_date',
        'decrypt_password',
        'latitude',
        'longitude',
        'wp_customer_id',
        'is_greview_post',
        'prev_visa',
        'smtp_host',
        'smtp_port',
        'smtp_enc',
        'smtp_username',
        'smtp_password',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('admins', 'default_email_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropForeign(['default_email_id']);
            });
        }

        $existing = array_filter($this->columnsToDrop, fn ($c) => Schema::hasColumn('admins', $c));
        if (!empty($existing)) {
            Schema::table('admins', function (Blueprint $table) use ($existing) {
                $table->dropColumn($existing);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optional: re-add columns with same types for rollback. Not implemented.
    }
}
