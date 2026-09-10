<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'icon' => fake()->word(),
            'status' => 'active',
            'sort_order' => 0,
        ];
    }

    /**
     * Indicate that the service is not yet launched.
     */
    public function comingSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'coming_soon',
        ]);
    }
}
