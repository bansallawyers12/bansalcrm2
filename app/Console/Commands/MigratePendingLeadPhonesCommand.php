<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigratePendingLeadPhonesCommand extends Command
{
    protected $signature = 'phones:migrate-pending-leads';

    protected $description = 'Migrate pending lead phones to client_phones (is_lead_migrate_to_admin=1, is_phone_migrate=0). Run after leads migration to catch any missed admins.';

    public function handle(): int
    {
        if (!Schema::hasTable('admins') || !Schema::hasTable('client_phones')) {
            $this->error('Required tables (admins, client_phones) do not exist.');
            return 1;
        }

        if (!Schema::hasColumn('admins', 'phone') || !Schema::hasColumn('admins', 'contact_type') || !Schema::hasColumn('admins', 'is_phone_migrate')) {
            $this->error('admins table missing required columns (phone, contact_type, is_phone_migrate).');
            return 1;
        }

        $admins = DB::table('admins')
            ->where('is_lead_migrate_to_admin', 1)
            ->where('is_phone_migrate', 0)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->select('id', 'phone', 'country_code', 'contact_type')
            ->get();

        if ($admins->isEmpty()) {
            $this->info('No pending admins to migrate (is_lead_migrate_to_admin=1, is_phone_migrate=0, non-empty phone).');
            return 0;
        }

        $userId = DB::table('admins')->where('id', 1)->value('id')
            ?? DB::table('admins')->min('id');
        if ($userId === null) {
            $this->error('No admins exist. Cannot set user_id for client_phones.');
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
                $existing = DB::table('client_phones')
                    ->where('client_id', $row->id)
                    ->where('client_phone', $row->phone)
                    ->first();

                if ($existing) {
                    DB::table('admins')
                        ->where('id', $row->id)
                        ->update(['is_phone_migrate' => 1, 'updated_at' => $now]);
                    $alreadyExisted++;
                } else {
                    $insertData = [
                        'user_id' => $userId,
                        'client_id' => $row->id,
                        'contact_type' => $row->contact_type,
                        'client_phone' => $row->phone,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    if (Schema::hasColumn('client_phones', 'client_country_code')) {
                        $insertData['client_country_code'] = $row->country_code;
                    }

                    if (Schema::hasColumn('client_phones', 'is_verified')) {
                        $insertData['is_verified'] = false;
                    }

                    DB::table('client_phones')->insert($insertData);

                    DB::table('admins')
                        ->where('id', $row->id)
                        ->update(['is_phone_migrate' => 1, 'updated_at' => $now]);
                    $inserted++;
                }
            } catch (\Throwable $e) {
                DB::table('admins')
                    ->where('id', $row->id)
                    ->update(['is_phone_migrate' => 2, 'updated_at' => $now]);
                $failed++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Status', 'Count'],
            [
                ['Inserted into client_phones', $inserted],
                ['Already existed (flag updated)', $alreadyExisted],
                ['Failed', $failed],
            ]
        );

        return $failed > 0 ? 1 : 0;
    }
}
