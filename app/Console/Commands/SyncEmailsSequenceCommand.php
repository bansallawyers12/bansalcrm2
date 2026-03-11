<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncEmailsSequenceCommand extends Command
{
    protected $signature = 'emails:sync-sequence';
    protected $description = 'Sync the emails.id sequence to MAX(id) so the next insert gets MAX(id)+1 (fixes duplicate key on compose email)';

    public function handle(): int
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            $this->warn('This command is for PostgreSQL only.');
            return self::FAILURE;
        }
        if (!Schema::hasTable('emails')) {
            $this->error('Table emails does not exist.');
            return self::FAILURE;
        }

        $maxId = DB::table('emails')->max('id');
        $this->info('Current MAX(id) in emails table: ' . ($maxId ?? '0'));

        $seqName = DB::selectOne("SELECT pg_get_serial_sequence('public.emails', 'id') AS seq");
        $seq = $seqName->seq ?? null;

        if (empty($seq)) {
            $this->warn('Column emails.id has no default sequence. Setting default to public.emails_id_seq.');
            try {
                DB::statement("ALTER TABLE public.emails ALTER COLUMN id SET DEFAULT nextval('public.emails_id_seq')");
                $seq = 'public.emails_id_seq';
            } catch (\Throwable $e) {
                try {
                    DB::statement("ALTER TABLE public.emails ALTER COLUMN id SET DEFAULT nextval('public.mail_reports_id_seq')");
                    $seq = 'public.mail_reports_id_seq';
                } catch (\Throwable $e2) {
                    $this->error('Could not set column default. Error: ' . $e2->getMessage());
                    return self::FAILURE;
                }
            }
        }

        DB::statement('SELECT setval(?, ?)', [$seq, $maxId ?? 1]);
        $this->info('Sequence ' . $seq . ' set to ' . ($maxId ?? 1) . '. Next insert will use id = ' . (($maxId ?? 1) + 1) . '.');
        return self::SUCCESS;
    }
}
