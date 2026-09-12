<?php

namespace App\Services;

use App\Models\Style;
use App\Repositories\StyleRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class StyleService
{
    public function __construct(protected StyleRepository $styles) {}

    public function list(): Collection
    {
        return $this->styles->all();
    }

    public function find(int $id): Style
    {
        return $this->styles->find($id);
    }

    public function create(array $data): Style
    {
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['name']);

        return $this->styles->create($data);
    }

    public function update(Style $style, array $data): Style
    {
        if (array_key_exists('slug', $data) || array_key_exists('name', $data)) {
            $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['name'] ?? $style->name, ignoreId: $style->id);
        }

        return $this->styles->update($style, $data);
    }

    public function delete(Style $style): void
    {
        $this->styles->delete($style);
    }

    /**
     * Styles are global, so the slug is unique across the whole table
     * (unlike Category/Subcategory, which only need to be unique within
     * their parent).
     */
    protected function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        $slug = $base;
        $suffix = 1;

        while (
            Style::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
