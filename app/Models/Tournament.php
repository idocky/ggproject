<?php

namespace App\Models;

use Database\Factories\TournamentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MongoDB\Laravel\Eloquent\Model;

#[Fillable(['name', 'grid_id'])]
class Tournament extends Model
{
    /** @use HasFactory<TournamentFactory> */
    use HasFactory;

    protected $connection = 'mongodb';

    protected static function booted(): void
    {
        static::deleting(function (Tournament $tournament): void {
            $hasMatches = GameMatch::query()->where('tournament.id', $tournament->id)->exists();

            if ($hasMatches) {
                throw new \RuntimeException('Cannot delete a tournament that is referenced by a match.');
            }
        });
    }

    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'tournament.id');
    }

    /**
     * Snapshot of this tournament's data as embedded into matches for history.
     *
     * @return array{id: string, name: ?string, grid_id: ?string}
     */
    public function toSnapshot(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'grid_id' => $this->grid_id,
        ];
    }
}
