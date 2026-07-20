<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TeamService
{
    private const PER_PAGE = 24;

    public function index(): LengthAwarePaginator
    {
        return Team::query()
            ->with('players')
            ->orderBy('name')
            ->paginate(self::PER_PAGE);
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
