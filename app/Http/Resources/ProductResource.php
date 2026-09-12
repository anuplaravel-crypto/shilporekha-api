<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'style_id' => $this->style_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => $this->imageUrl(),
            'description' => $this->description,
            'price' => $this->price,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'subcategory' => new SubcategoryResource($this->whenLoaded('subcategory')),
            'style' => new StyleResource($this->whenLoaded('style')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * image_path is normally a storage-relative path from an upload
     * (resolved via the public disk's URL), but seeders/tests may store a
     * ready-made external URL directly — pass those through unchanged.
     */
    protected function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        if (Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        return Storage::disk('public')->url($this->image_path);
    }
}
