<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    protected const RELATIONS = ['category', 'subcategory', 'style'];

    /**
     * All products, optionally filtered by any of service_id/category_id/
     * subcategory_id/style_id (all optional, combinable — e.g. the
     * storefront listing a service's products in one subcategory).
     *
     * @param  array{service_id?: int, category_id?: int, subcategory_id?: int, style_id?: int}  $filters
     */
    public function all(array $filters = []): Collection
    {
        return Product::with(self::RELATIONS)
            ->when($filters['service_id'] ?? null, fn ($query, $value) => $query->where('service_id', $value))
            ->when($filters['category_id'] ?? null, fn ($query, $value) => $query->where('category_id', $value))
            ->when($filters['subcategory_id'] ?? null, fn ($query, $value) => $query->where('subcategory_id', $value))
            ->when($filters['style_id'] ?? null, fn ($query, $value) => $query->where('style_id', $value))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): Product
    {
        return Product::with(self::RELATIONS)->findOrFail($id);
    }

    public function create(array $data): Product
    {
        $product = Product::create($data);

        return $product->fresh(self::RELATIONS);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh(self::RELATIONS);
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
