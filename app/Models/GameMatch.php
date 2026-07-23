<?php

namespace App\Models;

use App\Enums\MatchStatus;
use Database\Factories\GameMatchFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MongoDB\Laravel\Eloquent\Model;

#[Fillable(['team_a', 'team_b', 'date_time', 'tournament', 'format', 'html_url', 'grid_id', 'status'])]
class GameMatch extends Model
{
    /** @use HasFactory<GameMatchFactory> */
    use HasFactory;

    protected $connection = 'mongodb';

    protected $table = 'matches';

    protected $attributes = [
        'status' => MatchStatus::Planned->value,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_time' => 'datetime',
            'format' => 'integer',
            'status' => MatchStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (GameMatch $match): void {
            $match->maps()->delete();
        });
    }

    public function maps(): HasMany
    {
        return $this->hasMany(Map::class, 'match_id')->orderBy('pick');
    }
}
