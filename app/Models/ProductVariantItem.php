<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Domain\Media\Models\Media;

class ProductVariantItem extends Model
{
    use HasFactory;

    protected $table = 'product_variant_items';

    protected $fillable = [
        'variant_group_id',
        'product_id',
        'title',
        'attribute_value_json',
        'media_id',
        'sort_order',
        'metadata'
    ];

    protected $casts = [
        'attribute_value_json' => 'array',
        'metadata' => 'array'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function group()
    {
        return $this->belongsTo(ProductVariantGroup::class, 'variant_group_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    

   public function getImageUrlAttribute(): string
{
    // 🔹 Если айтем связан с медиа — возвращаем cdn_url
    if ($this->media?->cdn_url) {
        return $this->media->cdn_url;
    }

    // 🔹 Если медиа нет, проверяем у самого продукта main_image
    if ($this->product?->main_image?->cdn_url) {
        return $this->product->main_image->cdn_url;
    }

    // 🔹 Если ничего нет — placeholder
    return '/images/no-image.png';
}

}
