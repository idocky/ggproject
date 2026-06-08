<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $teams = Team::query()->get();

        if ($teams->isEmpty()) {
            $teams = Team::factory(10)->create();
        }

        $teams->each(function (Team $team): void {
            Player::factory(random_int(3, 6))->create([
                'team_id' => $team->id,
            ]);
        });
    }
}
