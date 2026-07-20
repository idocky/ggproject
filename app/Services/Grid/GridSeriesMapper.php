<?php

namespace App\Services\Grid;

use App\Models\GameMatch;
use App\Models\Map;
use App\Models\Player;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Support\Facades\DB;

class GridSeriesMapper
{
    /**
     * @param  array<int, array>  $games  Per-map results from the Series State API (map name,
     *                                    per-team score, winner). Empty for series that haven't
     *                                    been played yet.
     * @param  array<string, array<int, array>>  $rosters  Team rosters from the Series State API,
     *                                                     keyed by GRID team id. Empty for series
     *                                                     that haven't been played yet.
     */
    public function map(array $seriesNode, array $games = [], array $rosters = []): GameMatch
    {
        return DB::transaction(function () use ($seriesNode, $games, $rosters) {
            $tournament = Tournament::query()->updateOrCreate(
                ['grid_id' => $seriesNode['tournament']['id']],
                ['name' => $seriesNode['tournament']['name']],
            );

            [$teamANode, $teamBNode] = $seriesNode['teams'];

            $teamA = $this->upsertTeam($teamANode['baseInfo'], $rosters[$teamANode['baseInfo']['id']] ?? []);
            $teamB = $this->upsertTeam($teamBNode['baseInfo'], $rosters[$teamBNode['baseInfo']['id']] ?? []);

            $match = GameMatch::query()->updateOrCreate(
                ['grid_id' => $seriesNode['id']],
                [
                    'team_a_id' => $teamA->id,
                    'team_b_id' => $teamB->id,
                    'date_time' => $seriesNode['startTimeScheduled'] ?? null,
                    'tournament_id' => $tournament->id,
                    'format' => $this->parseFormat($seriesNode['format']['nameShortened'] ?? null),
                ],
            );

            $this->syncMaps($games, $match, $teamA, $teamB);

            return $match;
        });
    }

    /**
     * @param  array<int, array>  $games
     */
    private function syncMaps(array $games, GameMatch $match, Team $teamA, Team $teamB): void
    {
        foreach ($games as $index => $game) {
            $mapName = $game['map']['name'] ?? null;

            if ($mapName === null || ($game['finished'] ?? false) !== true) {
                continue;
            }

            Map::query()->updateOrCreate(
                [
                    'match_id' => $match->id,
                    'pick' => $game['sequenceNumber'] ?? $index + 1,
                ],
                [
                    'map' => $mapName,
                    ...$this->buildMapResult($game['teams'] ?? [], $teamA, $teamB),
                ],
            );
        }
    }

    /**
     * @param  array<int, array>  $gameTeams
     * @return array{score: array{team_a: int, team_b: int}, winner_team_id: int|null}
     */
    private function buildMapResult(array $gameTeams, Team $teamA, Team $teamB): array
    {
        $score = ['team_a' => 0, 'team_b' => 0];
        $winnerTeamId = null;

        foreach ($gameTeams as $gameTeam) {
            $teamId = $gameTeam['id'] ?? null;
            $teamScore = (int) ($gameTeam['score'] ?? 0);
            $team = match ($teamId) {
                $teamA->grid_id => $teamA,
                $teamB->grid_id => $teamB,
                default => null,
            };

            if ($team === null) {
                continue;
            }

            $score[$team->is($teamA) ? 'team_a' : 'team_b'] = $teamScore;

            if ($gameTeam['won'] ?? false) {
                $winnerTeamId = $team->id;
            }
        }

        return ['score' => $score, 'winner_team_id' => $winnerTeamId];
    }

    /**
     * @param  array{id: string, name: string, logoUrl?: string|null}  $baseInfo
     * @param  array<int, array>  $roster  Players from the Series State API (id, name). GRID does
     *                                     not expose team ranking or country/region on this or any
     *                                     other endpoint, so only the roster can be enriched here.
     */
    private function upsertTeam(array $baseInfo, array $roster = []): Team
    {
        $team = Team::query()->updateOrCreate(
            ['grid_id' => $baseInfo['id']],
            [
                'name' => $baseInfo['name'],
                'logo' => $baseInfo['logoUrl'] ?? null,
            ],
        );

        foreach ($roster as $player) {
            if (! isset($player['id'], $player['name'])) {
                continue;
            }

            Player::query()->updateOrCreate(
                ['grid_id' => $player['id']],
                [
                    'nickname' => $player['name'],
                    'team_id' => $team->id,
                ],
            );
        }

        return $team;
    }

    private function parseFormat(?string $nameShortened): ?int
    {
        if ($nameShortened === null || ! preg_match('/(\d+)/', $nameShortened, $matches)) {
            return null;
        }

        return (int) $matches[1];
    }
}
