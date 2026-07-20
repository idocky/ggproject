<?php

namespace Tests\Feature\Grid;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GridBackfillCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_backfills_series_for_the_given_range_and_exits_successfully(): void
    {
        Http::fake([
            config('services.grid.url') => Http::response([
                'data' => [
                    'allSeries' => [
                        'edges' => [
                            ['node' => $this->seriesNode('1', '100', '200')],
                            ['node' => $this->seriesNode('2', '100', '300')],
                        ],
                        'pageInfo' => ['hasNextPage' => false, 'endCursor' => null],
                    ],
                ],
            ]),
            config('services.grid.series_state_url') => Http::response(['data' => ['seriesState' => null]]),
        ]);

        $this->artisan('grid:backfill', ['from' => '2026-01-01', 'to' => '2026-01-02'])
            ->assertExitCode(0);

        $this->assertDatabaseCount('matches', 2);
        $this->assertDatabaseCount('teams', 3);
        $this->assertDatabaseCount('tournaments', 1);
    }

    public function test_it_exits_with_failure_when_a_series_fails_to_map_but_still_persists_the_rest(): void
    {
        Http::fake([
            config('services.grid.url') => Http::response([
                'data' => [
                    'allSeries' => [
                        'edges' => [
                            ['node' => ['id' => 'bad', 'startTimeScheduled' => null, 'format' => null, 'teams' => [], 'tournament' => null]],
                            ['node' => $this->seriesNode('2', '100', '300')],
                        ],
                        'pageInfo' => ['hasNextPage' => false, 'endCursor' => null],
                    ],
                ],
            ]),
            config('services.grid.series_state_url') => Http::response(['data' => ['seriesState' => null]]),
        ]);

        $this->artisan('grid:backfill', ['from' => '2026-01-01', 'to' => '2026-01-02'])
            ->assertExitCode(1);

        $this->assertDatabaseCount('matches', 1);
    }

    /**
     * @return array<string, mixed>
     */
    private function seriesNode(string $id, string $teamAId, string $teamBId): array
    {
        return [
            'id' => $id,
            'startTimeScheduled' => '2026-01-02T07:00:00Z',
            'format' => ['nameShortened' => 'Bo3'],
            'teams' => [
                ['baseInfo' => ['id' => $teamAId, 'name' => "Team {$teamAId}", 'logoUrl' => 'https://example.test/a.png']],
                ['baseInfo' => ['id' => $teamBId, 'name' => "Team {$teamBId}", 'logoUrl' => 'https://example.test/b.png']],
            ],
            'tournament' => ['id' => '9000', 'name' => 'Some Championship'],
        ];
    }
}
