<?php

namespace App\Services;

use App\Models\Map;
use Illuminate\Database\Eloquent\Collection;

class MapService
{
    /**
     * @return Collection<int, Map>
     */
    public function index(): Collection
    {
        return Map::query()
            ->with(['match.teamA', 'match.teamB', 'winnerTeam'])
            ->orderBy('pick')
            ->get();
    }

    public function show(Map $map): Map
    {
        return $map->load(['match.teamA', 'match.teamB', 'winnerTeam']);
    }

    public function create(array $data): Map
    {
        $map = Map::query()->create($data);

        return $map->load(['match.teamA', 'match.teamB', 'winnerTeam']);
    }

    public function update(Map $map, array $data): Map
    {
        $map->update($data);

        return $map->load(['match.teamA', 'match.teamB', 'winnerTeam']);
    }
}
