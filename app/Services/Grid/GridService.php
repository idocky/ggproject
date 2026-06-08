<?php

namespace App\Services\Grid;

class GridService
{
    public function __construct(
        private readonly GridClient $client,
    ) {}

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
}
