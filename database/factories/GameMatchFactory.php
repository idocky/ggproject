<?php

namespace Database\Factories;

use App\Models\GameMatch;
use App\Models\Team;
use App\Models\Tournament;
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
            'team_a' => fn () => Team::factory()->create()->toSnapshot(),
            'team_b' => fn () => Team::factory()->create()->toSnapshot(),
            'date_time' => fake()->optional()->dateTimeBetween('-1 month', '+1 month'),
            'tournament' => fn () => Tournament::factory()->create()->toSnapshot(),
            'format' => fake()->numberBetween(1, 5),
            'html_url' => fake()->optional()->url(),
            'grid_id' => (string) fake()->unique()->numberBetween(1, 9999999),
        ];
    }
}
