<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailLabel;

class SeedEmailLabels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-labels:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed default system email labels';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding default email labels...');

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

        $count = 0;
        foreach ($systemLabels as $labelData) {
            // Check if label already exists
            $exists = EmailLabel::where('name', $labelData['name'])
                ->where('type', 'system')
                ->exists();

            if (!$exists) {
                EmailLabel::create($labelData);
                $this->info("✓ Created system label: {$labelData['name']}");
                $count++;
            } else {
                $this->comment("  Label '{$labelData['name']}' already exists");
            }
        }

        if ($count > 0) {
            $this->info("\n✓ Successfully seeded {$count} email labels!");
        } else {
            $this->comment("\nAll system labels already exist.");
        }

        return 0;
    }
}

