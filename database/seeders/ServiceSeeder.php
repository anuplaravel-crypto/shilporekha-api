<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Seeds the Service -> Category (niche) -> Subcategory (micro-niche)
     * taxonomy. T-Shirt Design's categories/subcategories are a real
     * regrouping of the original flat 10-name list (e.g. Fishing/Camping/
     * Hiking/Hunting are all micro-niches of the Outdoor Adventure niche).
     * The other three services don't have a natural sub-grouping yet, so
     * each of their original names becomes its own category with a single
     * "General" placeholder subcategory — trivially editable via the new
     * Category/Subcategory admin CRUD once real products need finer niches.
     */
    public function run(): void
    {
        $catalog = [
            [
                'name' => 'T-Shirt Design',
                'status' => 'active',
                'icon' => 'tshirt',
                'description' => 'Custom illustration & typography, print-ready for your merch run.',
                'categories' => [
                    'Outdoor Adventure' => ['Fishing', 'Camping', 'Hiking', 'Hunting'],
                    'Motorsports' => ['Racing & Cars'],
                    'Western' => ['Rodeo & Ranch'],
                    'Fitness' => ['Gym & Training'],
                    'Typography' => ['Bold Statements'],
                    'Vintage & Retro' => ['Retro Sunset'],
                ],
            ],
            [
                'name' => 'Logo Design',
                'status' => 'coming_soon',
                'icon' => 'tag',
                'description' => 'Brand marks & wordmarks.',
                'categories' => [
                    'Wordmark' => ['General'],
                    'Lettermark' => ['General'],
                    'Monogram' => ['General'],
                    'Symbol & Icon' => ['General'],
                    'Combination Mark' => ['General'],
                ],
            ],
            [
                'name' => 'Packaging',
                'status' => 'coming_soon',
                'icon' => 'box',
                'description' => 'Boxes, labels & hang tags.',
                'categories' => [
                    'Box Packaging' => ['General'],
                    'Pouch Packaging' => ['General'],
                    'Label Design' => ['General'],
                    'Bottle Packaging' => ['General'],
                    'Food Packaging' => ['General'],
                ],
            ],
            [
                'name' => 'Branding',
                'status' => 'coming_soon',
                'icon' => 'palette',
                'description' => 'Full identity systems.',
                'categories' => [
                    'Brand Identity' => ['General'],
                    'Brand Guidelines' => ['General'],
                    'Business Card' => ['General'],
                    'Social Media Branding' => ['General'],
                    'Marketing Collateral' => ['General'],
                ],
            ],
        ];

        foreach ($catalog as $index => $entry) {
            $service = Service::updateOrCreate(
                ['slug' => Str::slug($entry['name'])],
                [
                    'name' => $entry['name'],
                    'status' => $entry['status'],
                    'icon' => $entry['icon'],
                    'description' => $entry['description'],
                    'sort_order' => $index,
                ],
            );

            $categoryIndex = 0;
            foreach ($entry['categories'] as $categoryName => $subcategoryNames) {
                $category = $service->categories()->updateOrCreate(
                    ['slug' => Str::slug($categoryName)],
                    ['name' => $categoryName, 'sort_order' => $categoryIndex],
                );
                $categoryIndex++;

                foreach ($subcategoryNames as $subIndex => $subName) {
                    $category->subcategories()->updateOrCreate(
                        ['slug' => Str::slug($subName)],
                        ['name' => $subName, 'sort_order' => $subIndex],
                    );
                }
            }
        }
    }
}
