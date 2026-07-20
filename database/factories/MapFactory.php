<?php

namespace Database\Factories;

use App\Models\GameMatch;
use App\Models\Map;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Map>
 */
class MapFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'map' => fake()->randomElement([
                'Dust2',
                'Mirage',
                'Inferno',
                'Nuke',
                'Ancient',
                'Anubis',
                'Train',
            ]),
            'score' => [
                'team_a' => fake()->numberBetween(0, 16),
                'team_b' => fake()->numberBetween(0, 16),
            ],
            'match_id' => GameMatch::factory(),
            'pick' => fake()->numberBetween(1, 5),
            'winner_team_id' => null,
        ];
    }
}
