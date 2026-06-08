<?php

namespace Database\Seeders;

use App\Models\GameMatch;
use App\Models\Map;
use Illuminate\Database\Seeder;

class MapSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $matches = GameMatch::query()->get();

        if ($matches->isEmpty()) {
            $matches = GameMatch::factory(5)->create();
        }

        $matches->each(function (GameMatch $match): void {
            Map::factory(random_int(2, 5))->create([
                'match_id' => $match->id,
            ]);
        });
    }
}
