<?php

namespace App\Services\Grid;

use DateTimeInterface;
use Illuminate\Support\Facades\Log;
use Throwable;

class GridIngestionService
{
    public function __construct(
        private readonly GridService $gridService,
        private readonly GridSeriesMapper $mapper,
    ) {}

    /**
     * @return array{processed: int, failed: int}
     */
    public function syncDateRange(DateTimeInterface $from, DateTimeInterface $to): array
    {
        $processed = 0;
        $failed = 0;

        foreach ($this->gridService->listSeries($from, $to) as $seriesNode) {
            try {
                $state = $this->gridService->getSeriesState($seriesNode['id']);
                $this->mapper->map($seriesNode, $state['games'], $state['rosters']);
                $processed++;
            } catch (Throwable $e) {
                $failed++;
                Log::error('grid ingestion: failed to map series', [
                    'series_id' => $seriesNode['id'] ?? null,
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        return compact('processed', 'failed');
    }
}
