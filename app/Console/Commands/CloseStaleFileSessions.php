<?php

namespace App\Console\Commands;

use App\Services\StaffFileSessionService;
use Illuminate\Console\Command;

class CloseStaleFileSessions extends Command
{
    protected $signature = 'my-day:close-stale-sessions';

    protected $description = 'Close auto file-time sessions whose heartbeat is older than 3 minutes';

    public function handle(StaffFileSessionService $sessions): int
    {
        $closed = $sessions->closeStale(now());
        $this->info("Closed {$closed} stale file session(s).");

        return self::SUCCESS;
    }
}
