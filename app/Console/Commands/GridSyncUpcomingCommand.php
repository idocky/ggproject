<?php

namespace App\Console\Commands;

use App\Services\Grid\GridIngestionService;
use Illuminate\Console\Command;

class GridSyncUpcomingCommand extends Command
{
    protected $signature = 'grid:sync-upcoming';

    protected $description = 'Refresh CS2 matches/teams/tournaments for tomorrow from GRID';

    public function handle(GridIngestionService $ingestion): int
    {
        $from = now()->addDay()->startOfDay();
        $to = now()->addDay()->endOfDay();

        $this->info("Syncing GRID series for {$from->toDateString()}...");

        $result = $ingestion->syncDateRange($from, $to);

        $this->info("Processed: {$result['processed']}, Failed: {$result['failed']}");

        return $result['failed'] > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
