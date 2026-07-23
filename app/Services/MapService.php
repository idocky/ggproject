<?php

namespace App\Services;

use App\Models\Map;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class MapService
{
    /**
     * @return Collection<int, Map>
     */
    public function index(): Collection
    {
        return Map::query()
            ->with('match')
            ->orderBy('pick')
            ->get();
    }

    public function show(Map $map): Map
    {
        return $map->load('match');
    }

    public function create(array $data): Map
    {
        $map = Map::query()->create($this->resolveWinnerSnapshot($data));

        return $map->load('match');
    }

    public function update(Map $map, array $data): Map
    {
        $map->update($this->resolveWinnerSnapshot($data));

        return $map->load('match');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function resolveWinnerSnapshot(array $data): array
    {
        if (array_key_exists('winner_team_id', $data)) {
            $data['winner_team'] = $data['winner_team_id'] !== null
                ? Team::query()->findOrFail($data['winner_team_id'])->toSnapshot()
                : null;
            unset($data['winner_team_id']);
        }

        return $data;
    }
}
