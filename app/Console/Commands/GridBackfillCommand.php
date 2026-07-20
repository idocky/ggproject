<?php

namespace App\Console\Commands;

use App\Services\Grid\GridIngestionService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GridBackfillCommand extends Command
{
    protected $signature = 'grid:backfill {from=2026-01-01} {to?}';

    protected $description = 'Backfill CS2 tournaments/teams/matches from GRID from a start date through now (or an explicit end date)';

    public function handle(GridIngestionService $ingestion): int
    {
        $from = Carbon::parse($this->argument('from'))->startOfDay();
        $to = $this->argument('to') ? Carbon::parse($this->argument('to'))->endOfDay() : now();

        $this->info("Backfilling GRID series from {$from} to {$to}...");

        $result = $ingestion->syncDateRange($from, $to);

        $this->info("Processed: {$result['processed']}, Failed: {$result['failed']}");

        return $result['failed'] > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
