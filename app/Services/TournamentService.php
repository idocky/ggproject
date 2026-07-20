<?php

namespace App\Services;

use App\Models\Tournament;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TournamentService
{
    private const PER_PAGE = 24;

    public function index(): LengthAwarePaginator
    {
        return Tournament::query()
            ->withCount('matches')
            ->orderBy('name')
            ->paginate(self::PER_PAGE);
    }

    public function show(Tournament $tournament): Tournament
    {
        return $tournament->load(['matches.teamA', 'matches.teamB']);
    }
}
