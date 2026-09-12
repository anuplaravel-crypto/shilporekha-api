<?php

namespace App\Services;

use App\Models\Subcategory;
use App\Repositories\SubcategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class SubcategoryService
{
    public function __construct(protected SubcategoryRepository $subcategories) {}

    public function list(?int $categoryId = null): Collection
    {
        return $this->subcategories->all($categoryId);
    }

    public function find(int $id): Subcategory
    {
        return $this->subcategories->find($id);
    }

    public function create(array $data): Subcategory
    {
        $data['slug'] = $this->uniqueSlug($data['category_id'], $data['slug'] ?? $data['name']);

        return $this->subcategories->create($data);
    }

    public function update(Subcategory $subcategory, array $data): Subcategory
    {
        $categoryId = $data['category_id'] ?? $subcategory->category_id;

        if (array_key_exists('slug', $data) || array_key_exists('name', $data)) {
            $data['slug'] = $this->uniqueSlug(
                $categoryId,
                $data['slug'] ?? $data['name'] ?? $subcategory->name,
                ignoreId: $subcategory->id,
            );
        }

        return $this->subcategories->update($subcategory, $data);
    }

    public function delete(Subcategory $subcategory): void
    {
        $this->subcategories->delete($subcategory);
    }

    /**
     * Slugs only need to be unique within a category.
     */
    protected function uniqueSlug(int $categoryId, string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        $slug = $base;
        $suffix = 1;

        while (
            Subcategory::where('category_id', $categoryId)
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
