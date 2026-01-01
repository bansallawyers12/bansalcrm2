<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backup tables
        $backupTables = [
            'admins_bk_10feb2024',
            'admins_bk_25jan2024',
        ];

        // Frontend/Website tables (no frontend website)
        $frontendTables = [
            'blog_categories',
            'blogs',
            'cms_pages',
            'testimonials',
            'sliders',
            'popups',
            'reviews',
            'wishlists',
            'free_downloads',
            'home_contents',
            'faqs',
            'seo_pages',
            'navmenus',
            'theme_options',
            'banners',
            'our_offices',
            'our_services',
            'why_chooseuses',
        ];

        // Legacy tour/travel tables
        $legacyTravelTables = [
            'destinations',
            'hotels',
            'airports',
            'packages',
        ];

        // Appointment system tables
        $appointmentTables = [
            'appointments',
            'appointment_logs',
            'book_services',
            'book_service_disable_slots',
            'book_service_slot_per_persons',
            'tbl_paid_appointment_payment',
        ];

        // Other unused tables
        $otherUnusedTables = [
            'coupons',
            'offers',
            'omrs',
            'media_images',
        ];

        // Combine all tables
        $tablesToDrop = array_merge(
            $backupTables,
            $frontendTables,
            $legacyTravelTables,
            $appointmentTables,
            $otherUnusedTables
        );

        // Drop from MySQL (if connection available)
        foreach ($tablesToDrop as $table) {
            try {
                Schema::connection('mysql')->dropIfExists($table);
            } catch (\Exception $e) {
                // MySQL connection not available, skip
            }
        }

        // Drop from PostgreSQL (if connection available)
        foreach ($tablesToDrop as $table) {
            try {
                Schema::connection('pgsql')->dropIfExists($table);
            } catch (\Exception $e) {
                // PostgreSQL connection not available, skip
            }
        }

        // Also try default connection
        foreach ($tablesToDrop as $table) {
            Schema::dropIfExists($table);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Cannot reverse table drops without schema definitions
        // These were unused/legacy tables that should not be recreated
    }
};
