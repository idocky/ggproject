<?php

namespace App\Models;

use Database\Factories\PlayerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MongoDB\Laravel\Eloquent\Model;

#[Fillable(['nickname', 'full_name', 'country', 'team_id', 'grid_id'])]
class Player extends Model
{
    /** @use HasFactory<PlayerFactory> */
    use HasFactory;

    protected $connection = 'mongodb';

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Snapshot of this player's data as embedded into team rosters for history.
     *
     * @return array{id: string, nickname: ?string, full_name: ?string, country: ?string, grid_id: ?string}
     */
    public function toSnapshot(): array
    {
        return [
            'id' => $this->id,
            'nickname' => $this->nickname,
            'full_name' => $this->full_name,
            'country' => $this->country,
            'grid_id' => $this->grid_id,
        ];
    }
}
