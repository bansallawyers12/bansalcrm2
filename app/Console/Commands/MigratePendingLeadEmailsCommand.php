<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigratePendingLeadEmailsCommand extends Command
{
    protected $signature = 'emails:migrate-pending-leads';

    protected $description = 'Migrate pending lead emails to client_emails (is_lead_migrate_to_admin=1, is_email_migrate=0). Run after leads migration to catch any missed admins.';

    public function handle(): int
    {
        if (!Schema::hasTable('admins') || !Schema::hasTable('client_emails')) {
            $this->error('Required tables (admins, client_emails) do not exist.');
            return 1;
        }

        if (!Schema::hasColumn('admins', 'email') || !Schema::hasColumn('admins', 'email_type') || !Schema::hasColumn('admins', 'is_email_migrate')) {
            $this->error('admins table missing required columns (email, email_type, is_email_migrate).');
            return 1;
        }

        $admins = DB::table('admins')
            ->where('is_lead_migrate_to_admin', 1)
            ->where('is_email_migrate', 0)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->select('id', 'email', 'email_type')
            ->get();

        if ($admins->isEmpty()) {
            $this->info('No pending admins to migrate (is_lead_migrate_to_admin=1, is_email_migrate=0, non-empty email).');
            return 0;
        }

        $userId = DB::table('admins')->where('id', 1)->value('id')
            ?? DB::table('admins')->min('id');
        if ($userId === null) {
            $this->error('No admins exist. Cannot set user_id for client_emails.');
            return 1;
        }

        $now = now();
        $inserted = 0;
        $alreadyExisted = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar($admins->count());
        $bar->start();

        foreach ($admins as $row) {
            try {
                $existing = DB::table('client_emails')
                    ->where('client_id', $row->id)
                    ->where('client_email', $row->email)
                    ->first();

                if ($existing) {
                    DB::table('admins')
                        ->where('id', $row->id)
                        ->update(['is_email_migrate' => 1, 'updated_at' => $now]);
                    $alreadyExisted++;
                } else {
                    DB::table('client_emails')->insert([
                        'user_id' => $userId,
                        'client_id' => $row->id,
                        'email_type' => $row->email_type,
                        'client_email' => $row->email,
                        'is_verified' => null,
                        'verified_at' => null,
                        'verified_by' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    DB::table('admins')
                        ->where('id', $row->id)
                        ->update(['is_email_migrate' => 1, 'updated_at' => $now]);
                    $inserted++;
                }
            } catch (\Throwable $e) {
                DB::table('admins')
                    ->where('id', $row->id)
                    ->update(['is_email_migrate' => 2, 'updated_at' => $now]);
                $failed++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Status', 'Count'],
            [
                ['Inserted into client_emails', $inserted],
                ['Already existed (flag updated)', $alreadyExisted],
                ['Failed', $failed],
            ]
        );

        return $failed > 0 ? 1 : 0;
    }
}
