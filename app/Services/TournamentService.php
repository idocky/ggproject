<?php

namespace App\Services;

use App\Models\GameMatch;
use App\Models\Tournament;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TournamentService
{
    private const PER_PAGE = 24;

    public function index(): LengthAwarePaginator
    {
        $tournaments = Tournament::query()
            ->orderBy('name')
            ->paginate(self::PER_PAGE);

        $matchCounts = GameMatch::query()
            ->whereIn('tournament.id', $tournaments->pluck('id'))
            ->get(['tournament'])
            ->groupBy(fn (GameMatch $match) => $match->tournament['id'])
            ->map(fn ($matches) => $matches->count());

        $tournaments->getCollection()->each(
            fn (Tournament $tournament) => $tournament->setAttribute(
                'matches_count',
                $matchCounts->get($tournament->id, 0),
            ),
        );

        return $tournaments;
    }

    public function show(Tournament $tournament): Tournament
    {
        return $tournament->load('matches');
    }
}
