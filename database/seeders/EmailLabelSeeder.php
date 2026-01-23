<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailLabel;
use Illuminate\Support\Facades\DB;

class EmailLabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if system labels already exist
        $existingLabels = EmailLabel::where('type', 'system')->count();
        
        if ($existingLabels > 0) {
            $this->command->info('System email labels already exist. Skipping seeding.');
            return;
        }

        $this->command->info('Seeding default email labels...');

        $systemLabels = [
            [
                'name' => 'Follow Up',
                'color' => '#F59E0B',
                'icon' => 'fas fa-flag',
                'description' => 'Emails requiring follow-up action',
                'type' => 'system',
                'user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Important',
                'color' => '#EF4444',
                'icon' => 'fas fa-star',
                'description' => 'High priority emails',
                'type' => 'system',
                'user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Inbox',
                'color' => '#3B82F6',
                'icon' => 'fas fa-inbox',
                'description' => 'Received emails',
                'type' => 'system',
                'user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Sent',
                'color' => '#10B981',
                'icon' => 'fas fa-paper-plane',
                'description' => 'Sent emails',
                'type' => 'system',
                'user_id' => null,
                'is_active' => true,
            ],
        ];

        foreach ($systemLabels as $label) {
            EmailLabel::create($label);
            $this->command->info("Created system label: {$label['name']}");
        }

        $this->command->info('Email labels seeded successfully!');
    }
}
