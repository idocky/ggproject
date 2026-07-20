<?php

namespace App\Services\Grid;

use DateTimeInterface;

class GridService
{
    private const PAGE_SIZE = 50;

    public function __construct(
        private readonly GridClient $client,
    ) {}

    /**
     * Page through all CS2 series scheduled within the given date range.
     *
     * @return iterable<int, array>
     */
    public function listSeries(DateTimeInterface $from, DateTimeInterface $to): iterable
    {
        $cursor = null;

        do {
            $data = $this->client->query($this->listSeriesQuery(), [
                'titleId' => config('services.grid.cs2_title_id'),
                'gte' => $from->format(DATE_ATOM),
                'lte' => $to->format(DATE_ATOM),
                'after' => $cursor,
                'first' => self::PAGE_SIZE,
            ]);

            $connection = $data['allSeries'] ?? ['edges' => [], 'pageInfo' => ['hasNextPage' => false]];

            foreach ($connection['edges'] as $edge) {
                yield $edge['node'];
            }

            $cursor = ($connection['pageInfo']['hasNextPage'] ?? false)
                ? $connection['pageInfo']['endCursor']
                : null;
        } while ($cursor !== null);
    }

    private function listSeriesQuery(): string
    {
        return <<<'GQL'
        query ListSeries($titleId: ID, $gte: String, $lte: String, $after: String, $first: Int) {
            allSeries(
                first: $first
                after: $after
                filter: { titleId: $titleId, startTimeScheduled: { gte: $gte, lte: $lte } }
                orderBy: StartTimeScheduled
                orderDirection: ASC
            ) {
                edges {
                    node {
                        id
                        startTimeScheduled
                        format {
                            nameShortened
                        }
                        teams {
                            baseInfo {
                                id
                                name
                                logoUrl
                            }
                        }
                        tournament {
                            id
                            name
                        }
                    }
                }
                pageInfo {
                    hasNextPage
                    endCursor
                }
            }
        }
        GQL;
    }

    public function getSeries(string $seriesId): array
    {
        $query = <<<'GQL'
        query Series($id: ID!) {
            series(id: $id) {
                id
                startTimeScheduled
                teams {
                    baseInfo {
                        id
                        name
                        logoUrl
                    }
                }
                tournament {
                    id
                    name
                }
                title {
                    id
                    nameShortened
                }
            }
        }
        GQL;

        $data = $this->client->query($query, [
            'id' => $seriesId,
        ]);

        return $data['series'] ?? [];
    }

    /**
     * Fetch per-map results (map name, per-team score, winner) and team rosters
     * for a series from the Series State (live data feed) API. Both are empty
     * for series that haven't started yet.
     *
     * @return array{games: array<int, array>, rosters: array<string, array<int, array>>}
     */
    public function getSeriesState(string $seriesId): array
    {
        $query = <<<'GQL'
        query GetSeriesState($id: ID!) {
            seriesState(id: $id) {
                teams {
                    id
                    players {
                        id
                        name
                    }
                }
                games {
                    sequenceNumber
                    finished
                    map {
                        name
                    }
                    teams {
                        id
                        score
                        won
                    }
                }
            }
        }
        GQL;

        $data = $this->client->query($query, [
            'id' => $seriesId,
        ], config('services.grid.series_state_url'));

        $state = $data['seriesState'] ?? null;

        if ($state === null) {
            return ['games' => [], 'rosters' => []];
        }

        $rosters = [];

        foreach ($state['teams'] ?? [] as $team) {
            $rosters[$team['id']] = $team['players'] ?? [];
        }

        return ['games' => $state['games'] ?? [], 'rosters' => $rosters];
    }
}
