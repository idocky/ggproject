<?php

namespace Tests\Feature\Grid;

use App\Services\Grid\GridService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GridServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_pages_through_all_series_in_a_date_range(): void
    {
        $pageOne = [
            'data' => [
                'allSeries' => [
                    'edges' => [
                        ['node' => $this->seriesNode('1')],
                        ['node' => $this->seriesNode('2')],
                    ],
                    'pageInfo' => [
                        'hasNextPage' => true,
                        'endCursor' => 'cursor-1',
                    ],
                ],
            ],
        ];

        $pageTwo = [
            'data' => [
                'allSeries' => [
                    'edges' => [
                        ['node' => $this->seriesNode('3')],
                    ],
                    'pageInfo' => [
                        'hasNextPage' => false,
                        'endCursor' => 'cursor-2',
                    ],
                ],
            ],
        ];

        Http::fake([
            config('services.grid.url') => Http::sequence()
                ->push($pageOne)
                ->push($pageTwo),
        ]);

        $service = app(GridService::class);

        $nodes = iterator_to_array($service->listSeries(
            new \DateTimeImmutable('2026-01-01T00:00:00Z'),
            new \DateTimeImmutable('2026-01-02T00:00:00Z'),
        ));

        $this->assertCount(3, $nodes);
        $this->assertSame(['1', '2', '3'], array_column($nodes, 'id'));

        Http::assertSentInOrder([
            fn ($request) => $request['variables']['after'] === null
                && $request['variables']['titleId'] === config('services.grid.cs2_title_id'),
            fn ($request) => $request['variables']['after'] === 'cursor-1',
        ]);
    }

    public function test_it_fetches_per_map_results_and_team_rosters_from_the_series_state_api(): void
    {
        Http::fake([
            config('services.grid.series_state_url') => Http::response([
                'data' => [
                    'seriesState' => [
                        'teams' => [
                            ['id' => '100', 'players' => [['id' => 'p1', 'name' => 'Ritchie']]],
                            ['id' => '200', 'players' => [['id' => 'p2', 'name' => 'freaq']]],
                        ],
                        'games' => [
                            [
                                'sequenceNumber' => 1,
                                'finished' => true,
                                'map' => ['name' => 'mirage'],
                                'teams' => [
                                    ['id' => '100', 'score' => 13, 'won' => true],
                                    ['id' => '200', 'score' => 8, 'won' => false],
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $service = app(GridService::class);

        $state = $service->getSeriesState('2878741');

        $this->assertCount(1, $state['games']);
        $this->assertSame('mirage', $state['games'][0]['map']['name']);

        $this->assertSame([
            '100' => [['id' => 'p1', 'name' => 'Ritchie']],
            '200' => [['id' => 'p2', 'name' => 'freaq']],
        ], $state['rosters']);

        Http::assertSent(fn ($request) => $request['variables']['id'] === '2878741');
    }

    public function test_it_returns_empty_games_and_rosters_when_the_series_has_not_started(): void
    {
        Http::fake([
            config('services.grid.series_state_url') => Http::response(['data' => []]),
        ]);

        $service = app(GridService::class);

        $this->assertSame(['games' => [], 'rosters' => []], $service->getSeriesState('2878741'));
    }

    /**
     * @return array<string, mixed>
     */
    private function seriesNode(string $id): array
    {
        return [
            'id' => $id,
            'startTimeScheduled' => '2026-01-02T07:00:00Z',
            'format' => ['nameShortened' => 'Bo3'],
            'teams' => [
                ['baseInfo' => ['id' => '100', 'name' => 'Team A', 'logoUrl' => 'https://example.test/a.png']],
                ['baseInfo' => ['id' => '200', 'name' => 'Team B', 'logoUrl' => 'https://example.test/b.png']],
            ],
            'tournament' => ['id' => '9000', 'name' => 'Some Championship'],
        ];
    }
}
