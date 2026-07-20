<?php

namespace Tests\Feature\Grid;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GridSyncUpcomingCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_requests_tomorrows_date_range_and_exits_successfully(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-20T12:00:00Z'));

        Http::fake([
            config('services.grid.url') => Http::response([
                'data' => [
                    'allSeries' => [
                        'edges' => [],
                        'pageInfo' => ['hasNextPage' => false, 'endCursor' => null],
                    ],
                ],
            ]),
        ]);

        $this->artisan('grid:sync-upcoming')->assertExitCode(0);

        Http::assertSent(function ($request) {
            return str_starts_with($request['variables']['gte'], '2026-07-21T00:00:00')
                && str_starts_with($request['variables']['lte'], '2026-07-21T23:59:59');
        });
    }
}
