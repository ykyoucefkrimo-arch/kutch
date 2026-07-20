<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewArrival extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'category_id',
        'description',
        'short_description',
        'price',
        'price_promo',
        'stock',
        'in_stock',
        'is_custom',
        'main_image',
        'images',
        'material',
        'color',
        'delivery_days',
        'sort_order',
    ];

    protected $casts = [
        'price'         => 'integer',
        'price_promo'   => 'integer',
        'stock'         => 'integer',
        'in_stock'      => 'boolean',
        'is_custom'     => 'boolean',
        'images'        => 'array',
        'delivery_days' => 'integer',
        'sort_order'    => 'integer',
    ];

    protected $appends = ['image_url'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->main_image) {
            return null;
        }

        return asset('storage/' . $this->main_image);
    }
}
