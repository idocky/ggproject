<?php

namespace Tests\Concerns;

use App\Models\GameMatch;
use App\Models\Map;
use App\Models\Player;
use App\Models\Team;
use App\Models\Tournament;

/**
 * MongoDB isn't rolled back by Laravel's RefreshDatabase (it operates on the
 * default relational test connection). Truncate the domain collections
 * before each test instead so they start empty.
 */
trait InteractsWithMongo
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach ([Map::class, GameMatch::class, Player::class, Team::class, Tournament::class] as $model) {
            $instance = new $model;
            $instance->getConnection()->getCollection($instance->getTable())->deleteMany([]);
        }
    }
}
