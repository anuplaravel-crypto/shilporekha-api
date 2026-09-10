<?php

namespace App\Services;

use App\Models\Service;
use App\Repositories\ServiceRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

/**
 * Named "ServiceCatalogService" rather than "ServiceService" to avoid
 * colliding, in name only, with the Service *model* this class manages —
 * this is the business-logic layer for the services catalog (T-Shirt,
 * Logo, Packaging, Branding), not a generic "Service" of some other kind.
 */
class ServiceCatalogService
{
    public function __construct(protected ServiceRepository $services) {}

    public function list(): Collection
    {
        return $this->services->all();
    }

    public function find(int $id): Service
    {
        return $this->services->find($id);
    }

    public function create(array $data): Service
    {
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['name']);

        return $this->services->create($data);
    }

    public function update(Service $service, array $data): Service
    {
        if (array_key_exists('slug', $data) || array_key_exists('name', $data)) {
            $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['name'] ?? $service->name, ignoreId: $service->id);
        }

        return $this->services->update($service, $data);
    }

    public function delete(Service $service): void
    {
        $this->services->delete($service);
    }

    protected function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        $slug = $base;
        $suffix = 1;

        while (
            Service::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
