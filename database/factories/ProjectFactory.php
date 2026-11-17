<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['a_faire', 'en_cours', 'termine']),
            'owner_id' => User::factory(),
        ];
    }
    // État pour forcer le statut active
    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }
}
