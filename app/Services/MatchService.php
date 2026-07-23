<?php

namespace App\Services;

use App\Models\GameMatch;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MatchService
{
    private const PER_PAGE = 20;

    private const RECENT_MATCHES_LIMIT = 5;

    public function index(): LengthAwarePaginator
    {
        return GameMatch::query()
            ->with('maps')
            ->orderByDesc('date_time')
            ->paginate(self::PER_PAGE);
    }

    public function show(GameMatch $match): GameMatch
    {
        return $match->load('maps');
    }

    public function create(array $data): GameMatch
    {
        $match = GameMatch::query()->create($this->resolveSnapshots($data));

        return $match->load('maps');
    }

    public function update(GameMatch $match, array $data): GameMatch
    {
        $match->update($this->resolveSnapshots($data));

        return $match->load('maps');
    }

    /**
     * @return array{team_a: Collection<int, GameMatch>, team_b: Collection<int, GameMatch>}
     */
    public function recentMatches(GameMatch $match): array
    {
        return [
            'team_a' => $this->recentMatchesForTeam($match->team_a['id'], $match),
            'team_b' => $this->recentMatchesForTeam($match->team_b['id'], $match),
        ];
    }

    /**
     * @return Collection<int, GameMatch>
     */
    private function recentMatchesForTeam(string $teamId, GameMatch $beforeMatch): Collection
    {
        $query = GameMatch::query()
            ->where(fn ($q) => $q->where('team_a.id', $teamId)->orWhere('team_b.id', $teamId))
            ->where('id', '!=', $beforeMatch->id)
            ->with('maps');

        if ($beforeMatch->date_time !== null) {
            $query->where('date_time', '<', $beforeMatch->date_time);
        }

        return $query->orderByDesc('date_time')
            ->limit(self::RECENT_MATCHES_LIMIT)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function resolveSnapshots(array $data): array
    {
        if (array_key_exists('team_a_id', $data)) {
            $data['team_a'] = Team::query()->findOrFail($data['team_a_id'])->toSnapshot();
            unset($data['team_a_id']);
        }

        if (array_key_exists('team_b_id', $data)) {
            $data['team_b'] = Team::query()->findOrFail($data['team_b_id'])->toSnapshot();
            unset($data['team_b_id']);
        }

        if (array_key_exists('tournament_id', $data)) {
            $data['tournament'] = Tournament::query()->findOrFail($data['tournament_id'])->toSnapshot();
            unset($data['tournament_id']);
        }

        return $data;
    }
}
