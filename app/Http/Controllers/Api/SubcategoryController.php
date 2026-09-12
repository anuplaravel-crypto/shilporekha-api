<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubcategoryRequest;
use App\Http\Requests\UpdateSubcategoryRequest;
use App\Http\Resources\SubcategoryResource;
use App\Models\Subcategory;
use App\Services\SubcategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function __construct(protected SubcategoryService $subcategories) {}

    public function index(Request $request): JsonResponse
    {
        $subcategories = $this->subcategories->list($request->integer('category_id') ?: null);

        return $this->success(SubcategoryResource::collection($subcategories));
    }

    public function show(Subcategory $subcategory): JsonResponse
    {
        return $this->success(new SubcategoryResource($subcategory));
    }

    public function store(StoreSubcategoryRequest $request): JsonResponse
    {
        $subcategory = $this->subcategories->create($request->validated());

        return $this->success(new SubcategoryResource($subcategory), 'Subcategory created successfully.', 201);
    }

    public function update(UpdateSubcategoryRequest $request, Subcategory $subcategory): JsonResponse
    {
        $subcategory = $this->subcategories->update($subcategory, $request->validated());

        return $this->success(new SubcategoryResource($subcategory), 'Subcategory updated successfully.');
    }

    public function destroy(Subcategory $subcategory): JsonResponse
    {
        $this->subcategories->delete($subcategory);

        return $this->success(null, 'Subcategory deleted successfully.');
    }
}
