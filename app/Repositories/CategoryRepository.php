<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    /**
     * All categories, optionally scoped to one service, with their
     * subcategories eager loaded.
     */
    public function all(?int $serviceId = null): Collection
    {
        return Category::with('subcategories')
            ->when($serviceId, fn ($query) => $query->where('service_id', $serviceId))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): Category
    {
        return Category::with('subcategories')->findOrFail($id);
    }

    public function create(array $data): Category
    {
        $category = Category::create($data);

        return $category->setRelation('subcategories', $category->subcategories()->get());
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->fresh('subcategories');
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
