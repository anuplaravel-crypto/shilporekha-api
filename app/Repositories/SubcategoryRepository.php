<?php

namespace App\Repositories;

use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Collection;

class SubcategoryRepository
{
    /**
     * All subcategories, optionally scoped to one category.
     */
    public function all(?int $categoryId = null): Collection
    {
        return Subcategory::when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): Subcategory
    {
        return Subcategory::findOrFail($id);
    }

    public function create(array $data): Subcategory
    {
        return Subcategory::create($data);
    }

    public function update(Subcategory $subcategory, array $data): Subcategory
    {
        $subcategory->update($data);

        return $subcategory->fresh();
    }

    public function delete(Subcategory $subcategory): void
    {
        $subcategory->delete();
    }
}
