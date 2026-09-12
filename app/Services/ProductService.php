<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductService
{
    public function __construct(protected ProductRepository $products) {}

    /**
     * @param  array{service_id?: int, category_id?: int, subcategory_id?: int, style_id?: int}  $filters
     */
    public function list(array $filters = []): Collection
    {
        return $this->products->all($filters);
    }

    public function find(int $id): Product
    {
        return $this->products->find($id);
    }

    public function create(array $data): Product
    {
        $this->assertTaxonomyIsConsistent($data['service_id'], $data['category_id'], $data['subcategory_id']);

        $data['slug'] = $this->uniqueSlug($data['service_id'], $data['slug'] ?? $data['name']);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image_path'] = $data['image']->store('products', 'public');
        }
        unset($data['image']);

        return $this->products->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $serviceId = $data['service_id'] ?? $product->service_id;
        $categoryId = $data['category_id'] ?? $product->category_id;
        $subcategoryId = $data['subcategory_id'] ?? $product->subcategory_id;
        $this->assertTaxonomyIsConsistent($serviceId, $categoryId, $subcategoryId);

        if (array_key_exists('slug', $data) || array_key_exists('name', $data)) {
            $data['slug'] = $this->uniqueSlug(
                $serviceId,
                $data['slug'] ?? $data['name'] ?? $product->name,
                ignoreId: $product->id,
            );
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $oldPath = $product->image_path;
            $data['image_path'] = $data['image']->store('products', 'public');

            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
        }
        unset($data['image']);

        return $this->products->update($product, $data);
    }

    public function delete(Product $product): void
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $this->products->delete($product);
    }

    /**
     * The category must actually belong to the service, and the
     * subcategory must actually belong to that category — otherwise a
     * client could submit any combination of valid-but-unrelated IDs and
     * end up with a product whose taxonomy doesn't chain together.
     */
    protected function assertTaxonomyIsConsistent(int $serviceId, int $categoryId, int $subcategoryId): void
    {
        $category = Category::find($categoryId);

        if (! $category || $category->service_id !== $serviceId) {
            throw ValidationException::withMessages([
                'category_id' => ['The selected category does not belong to the selected service.'],
            ]);
        }

        $subcategory = Subcategory::find($subcategoryId);

        if (! $subcategory || $subcategory->category_id !== $categoryId) {
            throw ValidationException::withMessages([
                'subcategory_id' => ['The selected subcategory does not belong to the selected category.'],
            ]);
        }
    }

    /**
     * Slugs only need to be unique within a service.
     */
    protected function uniqueSlug(int $serviceId, string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        $slug = $base;
        $suffix = 1;

        while (
            Product::where('service_id', $serviceId)
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
