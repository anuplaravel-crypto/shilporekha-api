<?php

namespace App\Repositories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository
{
    /**
     * All services, ordered for display, with their categories eager
     * loaded (read-only here — category/subcategory CRUD is a separate
     * feature).
     */
    public function all(): Collection
    {
        return Service::with('categories')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): Service
    {
        return Service::with('categories')->findOrFail($id);
    }

    public function create(array $data): Service
    {
        $service = Service::create($data);

        // Load the (necessarily empty, for a brand-new service) relation so
        // ServiceResource's whenLoaded('categories') always includes the
        // key — matching all()/find()/update(), instead of omitting it only
        // for freshly created services.
        return $service->setRelation('categories', $service->categories()->get());
    }

    public function update(Service $service, array $data): Service
    {
        $service->update($data);

        return $service->fresh('categories');
    }

    public function delete(Service $service): void
    {
        $service->delete();
    }
}
