<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SearchService;

class TestSearch extends Command
{
    protected $signature = 'test:search {query}';
    protected $description = 'Test the search functionality';

    public function handle()
    {
        $query = $this->argument('query');
        
        $this->info("Testing search for: {$query}");
        $this->line("=====================================\n");
        
        try {
            $searchService = new SearchService($query, 50, false);
            $results = $searchService->search();
            
            if (empty($results['items'])) {
                $this->error("NO RESULTS FOUND!");
            } else {
                $this->info("Found " . count($results['items']) . " result(s):");
                $this->line("");
                
                foreach ($results['items'] as $item) {
                    $this->line("  Name: " . strip_tags($item['name']));
                    $this->line("  Email: " . strip_tags($item['email'] ?? 'N/A'));
                    $this->line("  Phone: " . strip_tags($item['phone'] ?? 'N/A'));
                    $this->line("  Client ID: " . ($item['client_id'] ?? 'N/A'));
                    $this->line("  Status: " . $item['status']);
                    $this->line("  ---");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->line($e->getTraceAsString());
        }
    }
}

