<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamService
{
    /**
     * @return Collection<int, Team>
     */
    public function index(): Collection
    {
        return Team::query()
            ->with('players')
            ->orderBy('name')
            ->get();
    }

    public function show(Team $team): Team
    {
        return $team->load('players');
    }

    public function create(array $data): Team
    {
        $team = Team::query()->create($data);

        return $team->load('players');
    }

    public function update(Team $team, array $data): Team
    {
        $team->update($data);

        return $team->load('players');
    }
}
