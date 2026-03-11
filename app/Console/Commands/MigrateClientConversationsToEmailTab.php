<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateClientConversationsToEmailTab extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:client-conversations-to-email-tab
                            {--dry-run : Preview what will be migrated without making any changes}
                            {--skip-backup : Skip creating the backup table (not recommended)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Client Conversation tab records to Email tab by filling missing columns in emails';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun     = $this->option('dry-run');
        $skipBackup   = $this->option('skip-backup');
        $backupTable  = 'emails_conv_migration_backup_' . now()->format('Ymd_His');

        $this->newLine();
        $this->info('=== Client Conversations → Email Tab Migration ===');
        $this->newLine();

        if ($isDryRun) {
            $this->warn('DRY RUN MODE — No data will be modified.');
            $this->newLine();
        }

        // ── Step 1: Count records to be migrated ─────────────────────────────
        // Conversation tab records have type='client' but conversion_type=NULL
        $toMigrate = DB::table('emails')
            ->where('type', 'client')
            ->whereNull('conversion_type')
            ->count();

        $inboxCount = DB::table('emails')
            ->where('type', 'client')
            ->whereNull('conversion_type')
            ->where('mail_type', 1)
            ->count();

        $sentCount = DB::table('emails')
            ->where('type', 'client')
            ->whereNull('conversion_type')
            ->where('mail_type', 0)
            ->count();

        $this->info("Records identified for migration:");
        $this->table(
            ['Category', 'Count'],
            [
                ['Total to migrate', $toMigrate],
                ['  → Inbox (mail_type = 1)', $inboxCount],
                ['  → Sent  (mail_type = 0)', $sentCount],
            ]
        );
        $this->newLine();

        if ($toMigrate === 0) {
            $this->info('Nothing to migrate. All conversation records are already migrated or do not exist.');
            return self::SUCCESS;
        }

        if ($isDryRun) {
            $this->info('Dry run complete. Run without --dry-run to apply changes.');
            return self::SUCCESS;
        }

        // ── Step 2: Confirm before proceeding ────────────────────────────────
        if (!$this->confirm("Proceed with migrating {$toMigrate} records?", true)) {
            $this->warn('Migration cancelled by user.');
            return self::SUCCESS;
        }
        $this->newLine();

        // ── Step 3: Create backup table ───────────────────────────────────────
        if (!$skipBackup) {
            $this->info("Creating backup table: {$backupTable} ...");
            try {
                DB::statement("CREATE TABLE {$backupTable} AS SELECT * FROM emails WHERE type = 'client' AND conversion_type IS NULL");
                $backupCount = DB::table($backupTable)->count();
                $this->info("  Backup created successfully. ({$backupCount} rows backed up)");
            } catch (\Throwable $e) {
                $this->error('Backup failed: ' . $e->getMessage());
                $this->error('Migration aborted. No data was changed.');
                return self::FAILURE;
            }
        } else {
            $this->warn('Skipping backup (--skip-backup flag used).');
        }
        $this->newLine();

        // ── Step 4: Run migration inside a transaction ────────────────────────
        $this->info('Running migration inside a transaction ...');

        try {
            DB::transaction(function () use ($toMigrate) {
                $affected = DB::table('emails')
                    ->where('type', 'client')
                    ->whereNull('conversion_type')
                    ->update([
                        'conversion_type' => 'conversion_email_fetch',
                        'mail_body_type'  => DB::raw("CASE WHEN mail_type = 1 THEN 'inbox' ELSE 'sent' END"),
                    ]);

                if ($affected !== $toMigrate) {
                    throw new \RuntimeException(
                        "Row count mismatch: expected {$toMigrate} rows updated, got {$affected}. Rolling back."
                    );
                }
            });

            $this->info("  Transaction committed successfully.");
        } catch (\Throwable $e) {
            $this->error('Migration failed and was rolled back: ' . $e->getMessage());
            $this->newLine();
            if (!$skipBackup) {
                $this->warn("Your backup table '{$backupTable}' is intact. No data was lost.");
            }
            return self::FAILURE;
        }

        $this->newLine();

        // ── Step 5: Post-migration verification ───────────────────────────────
        $this->info('Verifying migration ...');

        $migratedTotal = DB::table('emails')
            ->where('type', 'client')
            ->where('conversion_type', 'conversion_email_fetch')
            ->whereNull('uploaded_doc_id')
            ->count();

        $migratedInbox = DB::table('emails')
            ->where('type', 'client')
            ->where('conversion_type', 'conversion_email_fetch')
            ->where('mail_body_type', 'inbox')
            ->whereNull('uploaded_doc_id')
            ->count();

        $migratedSent = DB::table('emails')
            ->where('type', 'client')
            ->where('conversion_type', 'conversion_email_fetch')
            ->where('mail_body_type', 'sent')
            ->whereNull('uploaded_doc_id')
            ->count();

        $remaining = DB::table('emails')
            ->where('type', 'client')
            ->whereNull('conversion_type')
            ->count();

        $this->newLine();
        $this->info('Migration Results:');
        $this->table(
            ['Check', 'Before', 'After'],
            [
                ['Total conversation records', $toMigrate, $migratedTotal],
                ['  → Inbox',                  $inboxCount, $migratedInbox],
                ['  → Sent',                   $sentCount,  $migratedSent],
                ['Remaining unmigrated',        '—',         $remaining],
            ]
        );
        $this->newLine();

        if ($remaining > 0) {
            $this->warn("{$remaining} record(s) were NOT migrated. Please investigate.");
        } else {
            $this->info('All records migrated successfully. Zero data loss confirmed.');
        }

        if (!$skipBackup) {
            $this->newLine();
            $this->line("Backup table: <comment>{$backupTable}</comment>");
            $this->line('To restore if needed, run:');
            $this->line("  UPDATE emails m SET conversion_type = b.conversion_type, mail_body_type = b.mail_body_type FROM {$backupTable} b WHERE m.id = b.id;");
        }

        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }
}
