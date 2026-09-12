<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(protected CategoryRepository $categories) {}

    public function list(?int $serviceId = null): Collection
    {
        return $this->categories->all($serviceId);
    }

    public function find(int $id): Category
    {
        return $this->categories->find($id);
    }

    public function create(array $data): Category
    {
        $data['slug'] = $this->uniqueSlug($data['service_id'], $data['slug'] ?? $data['name']);

        return $this->categories->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $serviceId = $data['service_id'] ?? $category->service_id;

        if (array_key_exists('slug', $data) || array_key_exists('name', $data)) {
            $data['slug'] = $this->uniqueSlug(
                $serviceId,
                $data['slug'] ?? $data['name'] ?? $category->name,
                ignoreId: $category->id,
            );
        }

        return $this->categories->update($category, $data);
    }

    public function delete(Category $category): void
    {
        $this->categories->delete($category);
    }

    /**
     * Slugs only need to be unique within a service — same rule Service's
     * own slug generation follows for its subcategories/categories.
     */
    protected function uniqueSlug(int $serviceId, string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        $slug = $base;
        $suffix = 1;

        while (
            Category::where('service_id', $serviceId)
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
