<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Seeds the real service/subcategory taxonomy documented in CLAUDE.md —
     * matches what the frontend and admin panel already display.
     */
    public function run(): void
    {
        $catalog = [
            [
                'name' => 'T-Shirt Design',
                'status' => 'active',
                'icon' => 'tshirt',
                'description' => 'Custom illustration & typography, print-ready for your merch run.',
                'subcategories' => [
                    'Outdoor Adventure', 'Fishing', 'Camping', 'Hiking', 'Hunting',
                    'Motorsports', 'Western', 'Fitness', 'Typography', 'Vintage / Retro',
                ],
            ],
            [
                'name' => 'Logo Design',
                'status' => 'coming_soon',
                'icon' => 'tag',
                'description' => 'Brand marks & wordmarks.',
                'subcategories' => ['Wordmark', 'Lettermark', 'Monogram', 'Symbol / Icon', 'Combination Mark'],
            ],
            [
                'name' => 'Packaging',
                'status' => 'coming_soon',
                'icon' => 'box',
                'description' => 'Boxes, labels & hang tags.',
                'subcategories' => ['Box Packaging', 'Pouch Packaging', 'Label Design', 'Bottle Packaging', 'Food Packaging'],
            ],
            [
                'name' => 'Branding',
                'status' => 'coming_soon',
                'icon' => 'palette',
                'description' => 'Full identity systems.',
                'subcategories' => ['Brand Identity', 'Brand Guidelines', 'Business Card', 'Social Media Branding', 'Marketing Collateral'],
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

            foreach ($entry['subcategories'] as $subIndex => $subName) {
                $service->subcategories()->updateOrCreate(
                    ['slug' => Str::slug($subName)],
                    ['name' => $subName, 'sort_order' => $subIndex],
                );
            }
        }
    }
}
