<?php

namespace App\Services;

use App\Models\GameMatch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MatchService
{
    private const PER_PAGE = 20;

    private const RECENT_MATCHES_LIMIT = 5;

    public function index(): LengthAwarePaginator
    {
        return GameMatch::query()
            ->with(['teamA', 'teamB', 'maps', 'tournament'])
            ->orderByDesc('date_time')
            ->paginate(self::PER_PAGE);
    }

    public function show(GameMatch $match): GameMatch
    {
        return $match->load(['teamA.players', 'teamB.players', 'maps', 'tournament']);
    }

    public function create(array $data): GameMatch
    {
        $match = GameMatch::query()->create($data);

        return $match->load(['teamA', 'teamB', 'maps', 'tournament']);
    }

    public function update(GameMatch $match, array $data): GameMatch
    {
        $match->update($data);

        return $match->load(['teamA', 'teamB', 'maps', 'tournament']);
    }

    /**
     * @return array{team_a: Collection<int, GameMatch>, team_b: Collection<int, GameMatch>}
     */
    public function recentMatches(GameMatch $match): array
    {
        return [
            'team_a' => $this->recentMatchesForTeam($match->team_a_id, $match),
            'team_b' => $this->recentMatchesForTeam($match->team_b_id, $match),
        ];
    }

    /**
     * @return Collection<int, GameMatch>
     */
    private function recentMatchesForTeam(int $teamId, GameMatch $beforeMatch): Collection
    {
        $query = GameMatch::query()
            ->where(fn ($q) => $q->where('team_a_id', $teamId)->orWhere('team_b_id', $teamId))
            ->where('id', '!=', $beforeMatch->id)
            ->with(['teamA', 'teamB', 'maps']);

        if ($beforeMatch->date_time !== null) {
            $query->where('date_time', '<', $beforeMatch->date_time);
        }

        return $query->orderByDesc('date_time')
            ->limit(self::RECENT_MATCHES_LIMIT)
            ->get();
    }
}
