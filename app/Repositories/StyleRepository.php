<?php

namespace App\Repositories;

use App\Models\Style;
use Illuminate\Database\Eloquent\Collection;

class StyleRepository
{
    public function all(): Collection
    {
        return Style::orderBy('name')->get();
    }

    public function find(int $id): Style
    {
        return Style::findOrFail($id);
    }

    public function create(array $data): Style
    {
        return Style::create($data);
    }

    public function update(Style $style, array $data): Style
    {
        $style->update($data);

        return $style->fresh();
    }

    public function delete(Style $style): void
    {
        $style->delete();
    }
}
