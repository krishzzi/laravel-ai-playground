<?php

namespace Database\Seeders;

use App\Casts\ModelStatusCast;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Electronics' => [
                'Mobile Phones',
                'Laptops',
                'Smart Watches',
            ],
            'Fashion' => [
                'Men Clothing',
                'Women Clothing',
                'Shoes',
            ],
            'Home & Living' => [
                'Furniture',
                'Kitchen',
                'Decor',
            ],
        ];

        foreach ($categories as $parentName => $children) {

            $parent = Category::create([
                'name' => $parentName,
                'slug' => Str::slug($parentName),
                'description' => fake()->sentence(),
                'status' => ModelStatusCast::PUBLISHED,
            ]);

            foreach ($children as $childName) {
                Category::create([
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'parent_id' => $parent->id,
                    'description' => fake()->sentence(),
                    'status' => ModelStatusCast::PUBLISHED,
                ]);
            }
        }
    }


}
