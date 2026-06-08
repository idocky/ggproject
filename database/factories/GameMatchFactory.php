<?php

namespace Database\Factories;

use App\Models\GameMatch;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameMatch>
 */
class GameMatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_a_id' => Team::factory(),
            'team_b_id' => Team::factory(),
            'date_time' => fake()->optional()->dateTimeBetween('-1 month', '+1 month'),
            'tournament_id' => fake()->numberBetween(1, 5000),
            'format' => fake()->numberBetween(1, 5),
            'html_url' => fake()->optional()->url(),
        ];
    }
}
