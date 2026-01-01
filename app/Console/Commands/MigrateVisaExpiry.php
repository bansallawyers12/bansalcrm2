<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateVisaExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:visaexpiry {--dry-run : Show what would be migrated without actually migrating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear and re-migrate visaexpiry data from MySQL to PostgreSQL';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('=== Visa Expiry Data Migration ===');
        $this->newLine();

        try {
            // Test connections
            $this->info('Testing database connections...');
            $mysqlConnection = DB::connection('mysql');
            $pgsqlConnection = DB::connection('pgsql');
            
            $mysqlConnection->getPdo();
            $pgsqlConnection->getPdo();
            
            $this->info('✓ MySQL connection: OK');
            $this->info('✓ PostgreSQL connection: OK');
            $this->newLine();

            if ($isDryRun) {
                $this->warn('DRY RUN MODE - No data will be modified');
                $this->newLine();
            }

            // Step 1: Clear visaexpiry in PostgreSQL
            $this->info('Step 1: Clearing visaexpiry column in PostgreSQL...');
            if (!$isDryRun) {
                $cleared = DB::connection('pgsql')
                    ->table('admins')
                    ->where('role', 7)
                    ->update(['visaexpiry' => null]);
                $this->info("  ✓ Cleared visaexpiry for {$cleared} admins with role 7");
            } else {
                $count = DB::connection('pgsql')
                    ->table('admins')
                    ->where('role', 7)
                    ->count();
                $this->info("  [DRY RUN] Would clear visaexpiry for {$count} admins with role 7");
            }
            $this->newLine();

            // Step 2: Get visaexpiry data from MySQL
            $this->info('Step 2: Fetching visaexpiry data from MySQL...');
            $mysqlData = DB::connection('mysql')
                ->table('admins')
                ->select('id', 'visaexpiry')
                ->where('role', 7)
                ->whereNotNull('visaexpiry')
                ->where('visaexpiry', '!=', '')
                ->where('visaexpiry', '!=', '0000-00-00')  // Invalid MySQL date
                ->get();

            $validRecords = 0;
            $invalidDates = 0;
            $updates = [];

            foreach ($mysqlData as $record) {
                $visaexpiry = $record->visaexpiry;
                
                // Clean invalid MySQL dates (0000-00-00 format)
                if (preg_match('/^0000-/', $visaexpiry)) {
                    $invalidDates++;
                    continue;
                }
                
                // Validate date format
                if (empty($visaexpiry) || trim($visaexpiry) === '') {
                    continue;
                }
                
                // Convert to proper date format if needed
                $timestamp = strtotime($visaexpiry);
                if ($timestamp === false) {
                    $invalidDates++;
                    continue;
                }
                
                $validDate = date('Y-m-d', $timestamp);
                $updates[$record->id] = $validDate;
                $validRecords++;
            }

            $this->info("  ✓ Found {$validRecords} valid visaexpiry records");
            if ($invalidDates > 0) {
                $this->warn("  ⚠ Skipped {$invalidDates} invalid dates");
            }
            $this->newLine();

            // Step 3: Update PostgreSQL with visaexpiry data
            $this->info('Step 3: Updating PostgreSQL with visaexpiry data...');
            
            if (!$isDryRun) {
                $updated = 0;
                $notFound = 0;
                $batchSize = 500;
                $batches = array_chunk($updates, $batchSize, true);
                
                $progressBar = $this->output->createProgressBar(count($batches));
                $progressBar->start();
                
                foreach ($batches as $batch) {
                    foreach ($batch as $id => $visaexpiry) {
                        try {
                            $affected = DB::connection('pgsql')
                                ->table('admins')
                                ->where('id', $id)
                                ->update(['visaexpiry' => $visaexpiry]);
                            
                            if ($affected > 0) {
                                $updated++;
                            } else {
                                $notFound++;
                            }
                        } catch (\Exception $e) {
                            $this->newLine();
                            $this->error("  Error updating ID {$id}: " . $e->getMessage());
                        }
                    }
                    $progressBar->advance();
                }
                
                $progressBar->finish();
                $this->newLine();
                $this->newLine();
                
                $this->info("  ✓ Updated {$updated} records");
                if ($notFound > 0) {
                    $this->warn("  ⚠ {$notFound} IDs not found in PostgreSQL");
                }
            } else {
                $this->info("  [DRY RUN] Would update " . count($updates) . " records");
            }
            $this->newLine();

            // Step 4: Verify migration
            $this->info('Step 4: Verifying migration...');
            $pgsqlCount = DB::connection('pgsql')
                ->table('admins')
                ->where('role', 7)
                ->whereNotNull('visaexpiry')
                ->whereRaw("CAST(visaexpiry AS TEXT) != ''")
                ->count();
            
            $this->info("  ✓ PostgreSQL now has {$pgsqlCount} admins with valid visaexpiry");
            $this->newLine();

            // Summary
            $this->info('=== MIGRATION SUMMARY ===');
            $this->info("MySQL records processed: {$validRecords}");
            $this->info("PostgreSQL records updated: " . ($isDryRun ? count($updates) : $updated));
            $this->info("Final count in PostgreSQL: {$pgsqlCount}");
            
            if (!$isDryRun && $pgsqlCount > 0) {
                $this->info('');
                $this->info('✓ Migration completed successfully!');
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('ERROR: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}

