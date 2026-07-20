<?php

namespace Tests\Feature\Grid;

use App\Models\Map;
use App\Models\Player;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\Grid\GridSeriesMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GridSeriesMapperTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_tournament_teams_and_match_from_a_series_node(): void
    {
        $mapper = app(GridSeriesMapper::class);

        $match = $mapper->map($this->seriesNode());

        $this->assertDatabaseCount('tournaments', 1);
        $this->assertDatabaseCount('teams', 2);
        $this->assertDatabaseCount('matches', 1);

        $this->assertSame('9000', Tournament::query()->sole()->grid_id);
        $this->assertSame(3, $match->format);
        $this->assertSame('100', $match->teamA->grid_id);
        $this->assertSame('200', $match->teamB->grid_id);
    }

    public function test_it_creates_maps_with_map_name_score_and_winner_from_series_state_games(): void
    {
        $mapper = app(GridSeriesMapper::class);

        $match = $mapper->map($this->seriesNode(), $this->games());

        $this->assertDatabaseCount('maps', 2);

        $maps = Map::query()->orderBy('pick')->get();

        $this->assertSame('mirage', $maps[0]->map);
        $this->assertSame(1, $maps[0]->pick);
        $this->assertSame(['team_a' => 13, 'team_b' => 8], $maps[0]->score);
        $this->assertSame($match->team_a_id, $maps[0]->winner_team_id);

        $this->assertSame('inferno', $maps[1]->map);
        $this->assertSame(2, $maps[1]->pick);
        $this->assertSame(['team_a' => 9, 'team_b' => 13], $maps[1]->score);
        $this->assertSame($match->team_b_id, $maps[1]->winner_team_id);

        $this->assertSame($match->id, $maps[0]->match_id);
    }

    public function test_it_skips_games_that_have_not_finished_yet(): void
    {
        $games = $this->games();
        $games[] = ['sequenceNumber' => 3, 'finished' => false, 'map' => ['name' => 'ancient'], 'teams' => []];

        $mapper = app(GridSeriesMapper::class);
        $mapper->map($this->seriesNode(), $games);

        $this->assertDatabaseCount('maps', 2);
    }

    public function test_it_does_not_create_maps_when_the_series_has_not_been_played_yet(): void
    {
        $mapper = app(GridSeriesMapper::class);
        $mapper->map($this->seriesNode(), []);

        $this->assertDatabaseCount('maps', 0);
    }

    public function test_it_creates_players_from_the_series_state_rosters(): void
    {
        $mapper = app(GridSeriesMapper::class);

        $match = $mapper->map($this->seriesNode(), $this->games(), $this->rosters());

        $this->assertDatabaseCount('players', 4);

        $teamAPlayers = Player::query()->where('team_id', $match->team_a_id)->pluck('nickname');
        $this->assertEqualsCanonicalizing(['Ritchie', 'freaq'], $teamAPlayers->all());

        $teamBPlayers = Player::query()->where('team_id', $match->team_b_id)->pluck('nickname');
        $this->assertEqualsCanonicalizing(['El-Nino', 'MISTRrepubliky78'], $teamBPlayers->all());
    }

    public function test_it_is_idempotent_when_mapping_rosters_twice(): void
    {
        $mapper = app(GridSeriesMapper::class);

        $mapper->map($this->seriesNode(), $this->games(), $this->rosters());
        $mapper->map($this->seriesNode(), $this->games(), $this->rosters());

        $this->assertDatabaseCount('players', 4);
    }

    public function test_it_does_not_create_players_when_no_roster_is_available(): void
    {
        $mapper = app(GridSeriesMapper::class);
        $mapper->map($this->seriesNode(), $this->games());

        $this->assertDatabaseCount('players', 0);
    }

    public function test_it_is_idempotent_when_mapping_the_same_series_twice(): void
    {
        $mapper = app(GridSeriesMapper::class);

        $mapper->map($this->seriesNode(), $this->games());
        $mapper->map($this->seriesNode(), $this->games());

        $this->assertDatabaseCount('tournaments', 1);
        $this->assertDatabaseCount('teams', 2);
        $this->assertDatabaseCount('matches', 1);
        $this->assertDatabaseCount('maps', 2);
    }

    public function test_it_updates_existing_rows_when_grid_data_changes(): void
    {
        $mapper = app(GridSeriesMapper::class);

        $mapper->map($this->seriesNode());

        $node = $this->seriesNode();
        $node['teams'][0]['baseInfo']['name'] = 'Renamed Team A';

        $mapper->map($node);

        $this->assertDatabaseCount('teams', 2);
        $this->assertSame('Renamed Team A', Team::query()->where('grid_id', '100')->sole()->name);
    }

    /**
     * @return array<string, mixed>
     */
    private function seriesNode(): array
    {
        return [
            'id' => '2878741',
            'startTimeScheduled' => '2026-01-02T07:00:00Z',
            'format' => ['nameShortened' => 'Bo3'],
            'teams' => [
                ['baseInfo' => ['id' => '100', 'name' => 'Team A', 'logoUrl' => 'https://example.test/a.png']],
                ['baseInfo' => ['id' => '200', 'name' => 'Team B', 'logoUrl' => 'https://example.test/b.png']],
            ],
            'tournament' => ['id' => '9000', 'name' => 'Some Championship'],
        ];
    }

    /**
     * @return array<int, array>
     */
    private function games(): array
    {
        return [
            [
                'sequenceNumber' => 1,
                'finished' => true,
                'map' => ['name' => 'mirage'],
                'teams' => [
                    ['id' => '100', 'score' => 13, 'won' => true],
                    ['id' => '200', 'score' => 8, 'won' => false],
                ],
            ],
            [
                'sequenceNumber' => 2,
                'finished' => true,
                'map' => ['name' => 'inferno'],
                'teams' => [
                    ['id' => '100', 'score' => 9, 'won' => false],
                    ['id' => '200', 'score' => 13, 'won' => true],
                ],
            ],
        ];
    }

    /**
     * @return array<string, array<int, array>>
     */
    private function rosters(): array
    {
        return [
            '100' => [
                ['id' => 'p1', 'name' => 'Ritchie'],
                ['id' => 'p2', 'name' => 'freaq'],
            ],
            '200' => [
                ['id' => 'p3', 'name' => 'El-Nino'],
                ['id' => 'p4', 'name' => 'MISTRrepubliky78'],
            ],
        ];
    }
}
