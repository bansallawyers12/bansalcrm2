<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if system labels already exist to prevent duplicates
        $existingLabels = DB::table('email_labels')->where('type', 'system')->count();
        
        if ($existingLabels > 0) {
            return; // Skip if system labels already exist
        }

        // Insert default system email labels
        DB::table('email_labels')->insert([
            [
                'name' => 'Important',
                'color' => '#EF4444',
                'icon' => 'fas fa-star',
                'description' => 'High priority emails',
                'type' => 'system',
                'user_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Inbox',
                'color' => '#3B82F6',
                'icon' => 'fas fa-inbox',
                'description' => 'Received emails',
                'type' => 'system',
                'user_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sent',
                'color' => '#10B981',
                'icon' => 'fas fa-paper-plane',
                'description' => 'Sent emails',
                'type' => 'system',
                'user_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Follow Up',
                'color' => '#F59E0B',
                'icon' => 'fas fa-flag',
                'description' => 'Emails requiring follow-up action',
                'type' => 'system',
                'user_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove only system labels created by this migration
        DB::table('email_labels')
            ->where('type', 'system')
            ->whereIn('name', ['Important', 'Inbox', 'Sent', 'Follow Up'])
            ->delete();
    }
};
