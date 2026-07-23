<?php

namespace App\Models;

use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MongoDB\Laravel\Eloquent\Model;

#[Fillable(['name', 'country', 'logo', 'ranking', 'grid_id'])]
class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    protected $connection = 'mongodb';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ranking' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Team $team): void {
            $hasMatches = GameMatch::query()
                ->where('team_a.id', $team->id)
                ->orWhere('team_b.id', $team->id)
                ->exists();

            if ($hasMatches) {
                throw new \RuntimeException('Cannot delete a team that is referenced by a match.');
            }

            $team->players()->delete();

            Map::query()->where('winner_team.id', $team->id)->update(['winner_team' => null]);
        });
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'team_a.id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'team_b.id');
    }

    /**
     * Snapshot of this team's data as embedded into matches/maps for history,
     * including the current roster so lineups are preserved even if players
     * later transfer to another team.
     *
     * @return array{id: string, name: ?string, country: ?string, logo: ?string, ranking: ?int, grid_id: ?string, players: array<int, array>}
     */
    public function toSnapshot(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country' => $this->country,
            'logo' => $this->logo,
            'ranking' => $this->ranking,
            'grid_id' => $this->grid_id,
            'players' => $this->players->map(fn (Player $player) => $player->toSnapshot())->all(),
        ];
    }
}
