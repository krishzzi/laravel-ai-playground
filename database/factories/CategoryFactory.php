<?php

namespace Database\Factories;

use App\Casts\ModelStatusCast;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(rand(1, 3), true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'parent_id' => null,
            'status' => fake()->randomElement(ModelStatusCast::cases()),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => ModelStatusCast::PUBLISHED,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => ModelStatusCast::DRAFT,
        ]);
    }
}
