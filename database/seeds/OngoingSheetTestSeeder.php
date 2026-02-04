<?php

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\ClientOngoingReference;

class OngoingSheetTestSeeder extends Seeder
{
    /**
     * Seed test data for ongoing sheet
     *
     * @return void
     */
    public function run()
    {
        // Get first 10 clients for testing
        $clients = Admin::where('role', 7)
            ->where('is_archived', 0)
            ->whereNull('is_deleted')
            ->take(10)
            ->get();

        if ($clients->isEmpty()) {
            $this->command->warn('No clients found to seed ongoing references.');
            return;
        }

        $statuses = [
            '01/02: Waiting for LOF & Payment',
            '28/01: Payment pending - will pay next week',
            '04/02: Documents submitted - processing',
            '03/02: Waiting for signed LOF & payment',
            '31/01: Application completed',
            '05/02: Checklist sent - awaiting response',
            '02/02: Follow-up required',
            '29/01: Review in progress',
            '04/02: Waiting for additional documents',
            '30/01: Final review stage',
        ];

        $paymentNotes = [
            null,
            '(Deferment)',
            'VOE Client',
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        ];

        $count = 0;
        foreach ($clients as $index => $client) {
            ClientOngoingReference::updateOrCreate(
                ['client_id' => $client->id],
                [
                    'current_status' => $statuses[$index],
                    'payment_display_note' => $paymentNotes[$index],
                    'institute_override' => null, // Will use computed value from applications
                    'visa_category_override' => null, // Will use computed value from admins
                ]
            );
            $count++;
        }

        $this->command->info("Successfully created {$count} ongoing sheet test records.");
    }
}
