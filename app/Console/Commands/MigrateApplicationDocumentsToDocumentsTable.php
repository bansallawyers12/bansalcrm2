<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateApplicationDocumentsToDocumentsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:application-documents-to-documents
                            {--client= : Client ID to migrate (only application docs for this client; ignored if --all)}
                            {--all : Migrate all application_documents (all clients)}
                            {--chunk=1000 : Process in batches of N records (for large datasets)}
                            {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate application_documents to documents table under Application category';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $migrateAll = $this->option('all');
        $clientId = $this->option('client') !== null && $this->option('client') !== '' ? (int) $this->option('client') : 36220;
        $chunkSize = max(100, (int) $this->option('chunk'));
        $isDryRun = $this->option('dry-run');

        $this->info('=== Application Documents Migration ===');
        if ($migrateAll) {
            $this->info('Scope: All clients (--all)');
        } else {
            $this->info("Scope: Client ID {$clientId}");
        }
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No data will be modified');
        }
        $this->newLine();

        // Get Application category
        $applicationCategory = DocumentCategory::where('name', 'Application')
            ->where('is_default', true)
            ->first();

        if (!$applicationCategory) {
            $this->error('Application category not found. Please ensure a default "Application" category exists.');
            return self::FAILURE;
        }

        $this->info("Application category ID: {$applicationCategory->id}");

        // Get application_documents where is_migrated = 0
        $query = ApplicationDocument::query()
            ->join('applications', 'application_documents.application_id', '=', 'applications.id')
            ->where('application_documents.is_migrated', 0);

        if (!$migrateAll) {
            $query->where('applications.client_id', $clientId);
        }

        $total = (clone $query)->count();
        $this->info("Application documents to process: {$total}");
        $this->info("Chunk size: {$chunkSize}");
        $this->newLine();

        if ($total === 0) {
            $this->info('Nothing to migrate.');
            return self::SUCCESS;
        }

        $inserted = 0;
        $skipped = 0;
        $chunkIndex = 0;
        $totalChunks = (int) ceil($total / $chunkSize);
        $progressAdvanceInterval = 500;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        try {
            $query->select('application_documents.*')->orderBy('application_documents.id')->chunk($chunkSize, function ($appDocs) use ($applicationCategory, $isDryRun, &$inserted, &$skipped, &$chunkIndex, $totalChunks, $progressAdvanceInterval, $bar) {
                $chunkIndex++;
                $this->newLine();
                $this->line("Processing chunk {$chunkIndex}/{$totalChunks}...");

                DB::transaction(function () use ($appDocs, $applicationCategory, $isDryRun, &$inserted, &$skipped, $progressAdvanceInterval, $bar) {
                    $processedInChunk = 0;
                    foreach ($appDocs as $appDoc) {
                        $application = Application::find($appDoc->application_id);
                        if (!$application) {
                            $this->warn("Skipping app_doc id {$appDoc->id}: Application not found.");
                            $processedInChunk++;
                            if ($processedInChunk % $progressAdvanceInterval === 0) {
                                $bar->advance($progressAdvanceInterval);
                            }
                            continue;
                        }

                        $docClientId = $application->client_id;
                        $myfileKey = $appDoc->myfile_key;

                        // Check for duplicate in documents table (by myfile_key)
                        $existing = Document::where('myfile_key', $myfileKey)
                            ->where('client_id', $docClientId)
                            ->exists();

                        if ($existing) {
                            if (!$isDryRun) {
                                DB::table('application_documents')->where('id', $appDoc->id)->update(['is_migrated' => 2]);
                            }
                            $skipped++;
                            $processedInChunk++;
                            if ($processedInChunk % $progressAdvanceInterval === 0) {
                                $bar->advance($progressAdvanceInterval);
                            }
                            continue;
                        }

                        if (!$isDryRun) {
                            Document::create([
                                'client_id' => $docClientId,
                                'category_id' => $applicationCategory->id,
                                'file_name' => $appDoc->file_name,
                                'myfile' => $appDoc->myfile,
                                'myfile_key' => $myfileKey,
                                'filetype' => $appDoc->file_type ?? '',
                                'file_size' => $appDoc->file_size ?? null,
                                'user_id' => $appDoc->user_id,
                                'type' => 'client',
                                'doc_type' => 'documents',
                                'checklist' => $appDoc->typename ?? null,
                                'application_id' => $appDoc->application_id,
                                'application_list_id' => $appDoc->list_id,
                                'application_stage' => $appDoc->typename ?? null,
                            ]);

                            DB::table('application_documents')->where('id', $appDoc->id)->update(['is_migrated' => 1]);
                        }

                        $inserted++;
                        $processedInChunk++;

                        // Advance progress bar every 500 records for visible feedback
                        if ($processedInChunk % $progressAdvanceInterval === 0) {
                            $bar->advance($progressAdvanceInterval);
                        }
                    }
                    // Advance remainder for this chunk
                    $remainder = $processedInChunk % $progressAdvanceInterval;
                    if ($remainder > 0) {
                        $bar->advance($remainder);
                    }
                });
            });
        } catch (\Throwable $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            $bar->finish();
            $this->newLine();
            return self::FAILURE;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Inserted: {$inserted}");
        $this->info("Skipped (duplicates): {$skipped}");
        $this->newLine();
        $this->info('Migration completed successfully.');

        return self::SUCCESS;
    }
}
