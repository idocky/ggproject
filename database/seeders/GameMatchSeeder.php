<?php

namespace Database\Seeders;

use App\Models\GameMatch;
use App\Models\Team;
use Illuminate\Database\Seeder;

class GameMatchSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $teams = Team::all();

        if ($teams->count() < 2) {
            Team::factory(2)->create();
            $teams = Team::all();
        }

        foreach (range(1, 12) as $index) {
            [$teamA, $teamB] = $teams->random(2)->values()->all();

            GameMatch::factory()->create([
                'team_a' => $teamA->toSnapshot(),
                'team_b' => $teamB->toSnapshot(),
            ]);
        }
    }
}
