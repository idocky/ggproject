<?php

namespace App\Models;

use Database\Factories\GameMatchFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['team_a_id', 'team_b_id', 'date_time', 'tournament_id', 'format', 'html_url'])]
class GameMatch extends Model
{
    /** @use HasFactory<GameMatchFactory> */
    use HasFactory;

    protected $table = 'matches';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_time' => 'datetime',
            'team_a_id' => 'integer',
            'team_b_id' => 'integer',
            'tournament_id' => 'integer',
            'format' => 'integer',
        ];
    }

    public function teamA(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_a_id');
    }

    public function teamB(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_b_id');
    }

    public function maps(): HasMany
    {
        return $this->hasMany(Map::class, 'match_id');
    }
}
