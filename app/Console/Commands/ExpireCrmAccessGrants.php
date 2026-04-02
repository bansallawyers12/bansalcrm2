<?php

namespace App\Console\Commands;

use App\Services\CrmAccess\CrmAccessService;
use Illuminate\Console\Command;

class ExpireCrmAccessGrants extends Command
{
    protected $signature = 'access:expire-grants';

    protected $description = 'Expire active CRM access grants past ends_at and stale pending supervisor requests';

    public function handle(CrmAccessService $crmAccess): int
    {
        $n = $crmAccess->expireStaleGrants();
        $this->info("Updated {$n} grant row(s).");

        return self::SUCCESS;
    }
}
