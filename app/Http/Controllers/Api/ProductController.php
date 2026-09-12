<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $products) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['service_id', 'category_id', 'subcategory_id', 'style_id']);

        return $this->success(ProductResource::collection($this->products->list($filters)));
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'subcategory', 'style']);

        return $this->success(new ProductResource($product));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->products->create($request->validated());

        return $this->success(new ProductResource($product), 'Product created successfully.', 201);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->products->update($product, $request->validated());

        return $this->success(new ProductResource($product), 'Product updated successfully.');
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->products->delete($product);

        return $this->success(null, 'Product deleted successfully.');
    }
}
