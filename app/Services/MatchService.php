<?php

namespace App\Services;

use App\Models\GameMatch;
use Illuminate\Database\Eloquent\Collection;

class MatchService
{
    /**
     * @return Collection<int, GameMatch>
     */
    public function index(): Collection
    {
        return GameMatch::query()
            ->with(['teamA', 'teamB', 'maps'])
            ->orderByDesc('date_time')
            ->get();
    }

    public function show(GameMatch $match): GameMatch
    {
        return $match->load(['teamA', 'teamB', 'maps']);
    }

    public function create(array $data): GameMatch
    {
        $match = GameMatch::query()->create($data);

        return $match->load(['teamA', 'teamB', 'maps']);
    }

    public function update(GameMatch $match, array $data): GameMatch
    {
        $match->update($data);

        return $match->load(['teamA', 'teamB', 'maps']);
    }
}
