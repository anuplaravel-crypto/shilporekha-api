<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Services\ServiceCatalogService;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function __construct(protected ServiceCatalogService $services) {}

    public function index(): JsonResponse
    {
        return $this->success(ServiceResource::collection($this->services->list()));
    }

    public function show(Service $service): JsonResponse
    {
        $service->load('categories');

        return $this->success(new ServiceResource($service));
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = $this->services->create($request->validated());

        return $this->success(new ServiceResource($service), 'Service created successfully.', 201);
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $service = $this->services->update($service, $request->validated());

        return $this->success(new ServiceResource($service), 'Service updated successfully.');
    }

    public function destroy(Service $service): JsonResponse
    {
        $this->services->delete($service);

        return $this->success(null, 'Service deleted successfully.');
    }
}
