<?php

namespace Database\Factories;

use App\Casts\ModelStatusCast;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(rand(2, 4), true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name . '-' . fake()->unique()->numberBetween(1000, 9999)),
            'description' => fake()->paragraphs(3, true),

            // If category doesn't exist, create one
            'category_id' => Category::query()->inRandomOrder()->value('id')
                ?? Category::factory(),

            'price' => fake()->randomFloat(2, 99, 99999),
            'quantity' => fake()->numberBetween(0, 500),

            'status' => fake()->randomElement(ModelStatusCast::cases()),
        ];
    }


    public function published(): static
    {
        return $this->state(fn () => [
            'status' => ModelStatusCast::PUBLISHED,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => [
            'quantity' => 0,
        ]);
    }


}
