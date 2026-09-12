<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use App\Models\Style;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * A handful of sample T-Shirt Design products so the admin/storefront
     * have something real to display — image_path here is a placeholder
     * external URL (ProductResource passes those through as-is) rather
     * than an uploaded file, since seeders have no file to store.
     */
    public function run(): void
    {
        $service = Service::where('slug', 't-shirt-design')->first();

        if (! $service) {
            return;
        }

        $products = [
            [
                'category' => 'Outdoor Adventure',
                'subcategory' => 'Fishing',
                'style' => 'Realistic',
                'name' => 'Bass Strike Tee',
                'description' => 'A largemouth bass breaking the surface, rendered in bold realistic linework.',
                'price' => 24.99,
            ],
            [
                'category' => 'Outdoor Adventure',
                'subcategory' => 'Camping',
                'style' => 'Vintage',
                'name' => 'Campfire Nights Tee',
                'description' => 'A retro badge-style design for late nights around the fire.',
                'price' => 22.99,
            ],
            [
                'category' => 'Motorsports',
                'subcategory' => 'Racing & Cars',
                'style' => 'Bold Typography',
                'name' => 'Redline Racer Tee',
                'description' => 'High-contrast racing typography with a checkered-flag motif.',
                'price' => 26.99,
            ],
            [
                'category' => 'Typography',
                'subcategory' => 'Bold Statements',
                'style' => 'Minimalist',
                'name' => 'No Bad Days Tee',
                'description' => 'Clean, minimalist statement type — print-ready in one color.',
                'price' => 19.99,
            ],
        ];

        foreach ($products as $index => $entry) {
            $category = Category::where('service_id', $service->id)->where('name', $entry['category'])->first();
            $subcategory = Subcategory::where('category_id', $category?->id)->where('name', $entry['subcategory'])->first();
            $style = Style::where('name', $entry['style'])->first();

            if (! $category || ! $subcategory) {
                continue;
            }

            $service->products()->updateOrCreate(
                ['slug' => Str::slug($entry['name'])],
                [
                    'category_id' => $category->id,
                    'subcategory_id' => $subcategory->id,
                    'style_id' => $style?->id,
                    'name' => $entry['name'],
                    'image_path' => 'https://placehold.co/600x600/0a0a0a/faf9f6?font=montserrat&text='.urlencode($entry['name']),
                    'description' => $entry['description'],
                    'price' => $entry['price'],
                    'status' => 'active',
                    'sort_order' => $index,
                ],
            );
        }
    }
}
