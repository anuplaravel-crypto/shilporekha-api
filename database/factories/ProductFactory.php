<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Builds a consistent Service -> Category -> Subcategory chain by
     * default, since Product requires all three to actually relate to
     * each other (see ProductService::assertTaxonomyIsConsistent()).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $service = Service::factory()->create();
        $category = Category::factory()->for($service)->create();
        $subcategory = Subcategory::factory()->for($category)->create();

        $name = fake()->unique()->words(3, true);

        return [
            'service_id' => $service->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'style_id' => null,
            'name' => $name,
            'slug' => Str::slug($name),
            'image_path' => 'https://placehold.co/600x600',
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 15, 60),
            'status' => 'active',
            'sort_order' => 0,
        ];
    }
}
