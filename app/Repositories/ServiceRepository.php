<?php

namespace App\Repositories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository
{
    /**
     * All services, ordered for display, with their subcategories eager
     * loaded (read-only here — subcategory CRUD is a separate feature).
     */
    public function all(): Collection
    {
        return Service::with('subcategories')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): Service
    {
        return Service::with('subcategories')->findOrFail($id);
    }

    public function create(array $data): Service
    {
        $service = Service::create($data);

        // Load the (necessarily empty, for a brand-new service) relation so
        // ServiceResource's whenLoaded('subcategories') always includes the
        // key — matching all()/find()/update(), instead of omitting it only
        // for freshly created services.
        return $service->setRelation('subcategories', $service->subcategories()->get());
    }

    public function update(Service $service, array $data): Service
    {
        $service->update($data);

        return $service->fresh('subcategories');
    }

    public function delete(Service $service): void
    {
        $service->delete();
    }
}
