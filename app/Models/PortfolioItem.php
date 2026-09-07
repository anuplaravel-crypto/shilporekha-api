<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcategory_id',
        'title',
        'slug',
        'description',
        'image_path',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    // The parent service isn't a direct column — reach it through the subcategory,
    // e.g. $item->subcategory->service, or eager load with ->with('subcategory.service').

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
