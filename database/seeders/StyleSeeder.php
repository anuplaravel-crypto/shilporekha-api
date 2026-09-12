<?php

namespace Database\Seeders;

use App\Models\Style;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StyleSeeder extends Seeder
{
    public function run(): void
    {
        $styles = [
            'Minimalist',
            'Vintage',
            'Bold Typography',
            'Realistic',
            'Cartoon / Illustrated',
            'Line Art',
        ];

        foreach ($styles as $name) {
            Style::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
