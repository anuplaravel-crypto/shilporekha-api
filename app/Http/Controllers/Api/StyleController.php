<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStyleRequest;
use App\Http\Requests\UpdateStyleRequest;
use App\Http\Resources\StyleResource;
use App\Models\Style;
use App\Services\StyleService;
use Illuminate\Http\JsonResponse;

class StyleController extends Controller
{
    public function __construct(protected StyleService $styles) {}

    public function index(): JsonResponse
    {
        return $this->success(StyleResource::collection($this->styles->list()));
    }

    public function show(Style $style): JsonResponse
    {
        return $this->success(new StyleResource($style));
    }

    public function store(StoreStyleRequest $request): JsonResponse
    {
        $style = $this->styles->create($request->validated());

        return $this->success(new StyleResource($style), 'Style created successfully.', 201);
    }

    public function update(UpdateStyleRequest $request, Style $style): JsonResponse
    {
        $style = $this->styles->update($style, $request->validated());

        return $this->success(new StyleResource($style), 'Style updated successfully.');
    }

    public function destroy(Style $style): JsonResponse
    {
        $this->styles->delete($style);

        return $this->success(null, 'Style deleted successfully.');
    }
}
