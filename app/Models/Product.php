<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Specification;
use App\Domain\Media\Models\Media;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
                
        'slug',
        'sku',
        'category_id',
        'moq',
        'lead_time',
        'customization',
        'supplier_id',
        'status',
        'country_id',
        'reject_reason',
        'origin_region_id',
        'origin_city_id',
        'origin_address',
        'origin_contact_name',
        'origin_contact_phone',
        'variant_group_id',
    ];


    protected static function booted()
    {
        static::created(function (Product $product) {
            $product->stock()->create([
                'quantity' => 0,
                'notes' => 'Initial stock',
            ]);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

   public function supplier()
{
    return $this->belongsTo(\App\Models\Supplier::class, 'supplier_id');
}

    public function specifications()
    {
        return $this->hasMany(Specification::class);
    }

    public function priceTiers()
    {
        return $this->hasMany(PriceTier::class);
    }

    public function getMaxTierPriceAttribute()
{
    return $this->priceTiers()
        ->orderByDesc('max_qty')
        ->value('price');
}

    

// Материалы (pivot)
    public function materials()
{
    return $this->belongsToMany(
        Material::class,
        'product_materials', // имя таблицы
        'product_id',        // FK для этой модели
        'material_id'        // FK для связанной модели
    );
}

public function colors()
{
    return $this->hasMany(Color::class);
}

// отношения
    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->firstWhere('locale', $locale);
    }

    // Аксессоры для удобного доступа
    public function getNameAttribute($value)
    {
        return $this->translation()?->name ?? $value;
    }

    public function getUndernameAttribute($value)
    {
        return $this->translation()?->undername ?? $value;
    }

    public function getDescriptionAttribute($value)
    {
        return $this->translation()?->description ?? $value;
    }


    public function user()
{
    return $this->belongsTo(User::class);
}

public function stocks()
{
    return $this->hasMany(ProductStock::class);
}

public function shippingTemplates()
{
    return $this->belongsToMany(ShippingTemplate::class, 'product_shipping_template');
}

public function country()
{
    return $this->belongsTo(Country::class);
}

public function stock()
{
    return $this->hasOne(ProductStock::class);
}

// Отзывы на этот продукт
public function reviews()
{
    return $this->hasMany(Review::class);
}


public function orderItems()
{
    return $this->hasMany(OrderItem::class, 'product_id');
}

/**
 * Заказы продукта (через OrderItem)
 */
public function orders()
{
    return $this->hasManyThrough(
        Order::class,
        OrderItem::class,
        'product_id', // FK OrderItem → Product
        'id',         // FK Order → OrderItem
        'id',         // Local key Product
        'order_id'    // Local key OrderItem
    );
}

public function scopeWithBaseRelations($query)
{
    $locale = app()->getLocale();

    return $query
        ->with([
            'materials.translations' => fn($q) => $q->where('locale', $locale),
            'category',
            'country',
            'supplier',
        ])
        ->withCount('reviews')
        ->withSum([
            'orderItems as sold_count' => fn ($q) =>
                $q->whereHas('order', fn($o) => $o->where('status', 'completed'))
        ], 'quantity');
}

    public function originCountry()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function originRegion()
    {
        return $this->belongsTo(Location::class, 'origin_region_id');
    }

    public function originCity()
    {
        return $this->belongsTo(Location::class, 'origin_city_id');
    }


    public function images()
{
    return $this->morphMany(Media::class, 'model')
        ->where('collection', 'product_gallery')
        ->orderBy('id');
}

public function getMainImageAttribute()
{
    $images = $this->images->sortBy('sort_order');

    return $images->firstWhere('is_main', 1) ?? $images->first();
}

// Удобный accessor для URL главного изображения
// Главное изображение
public function getMainImageUrlAttribute(): ?string
{
    $media = $this->images->sortBy('sort_order')->firstWhere('is_main', 1) ?? $this->images->sortBy('sort_order')->first();
    return $media?->url('large'); // можно 'original', 'large', 'thumb'
}

// Миниатюры для галереи
public function getThumbnailsAttribute(): array
{
    $variants = config('media.collections.product_gallery.variants', []);

    return $this->images->sortBy('sort_order')->map(function($media) use ($variants) {
        $urls = [];

        foreach ($variants as $variant => $size) {
            $urls[$variant] = $media->url($variant);
        }

        $urls['is_main'] = $media->is_main;

        return $urls;
    })->toArray();
}

public function getCatalogImageUrlAttribute(): string
{
    $main = $this->images->sortBy('sort_order')->firstWhere('is_main', 1)
        ?? $this->images->sortBy('sort_order')->first();

    return $main?->url('small') ?? asset('images/no-image.png');
}

public function variantGroup()
{
    return $this->belongsTo(ProductVariantGroup::class);
}



public function variantItems()
{
    return $this->hasMany(ProductVariantItem::class, 'variant_group_id', 'variant_group_id');
}

public function variantPreview()
{
    return $this->morphOne(Media::class, 'model')
        ->where('collection', 'product_variant_image')
        ->orderBy('id', 'asc');
}

public function attributeValues()
{
    return $this->hasMany(
        ProductAttributeValue::class
    );
}

public function attributes()
{
    return $this->belongsToMany(
        Attribute::class,
        'product_attribute_values'
    );
}

public function shippingDimensions()
{
    return $this->hasOne(ProductShippingDimensions::class);
}

public function computeShippingPrice(ShippingTemplate $template): float
{
    $finalPrice = $template->price;

    if ($this->shippingDimensions) {
        $dimensions = $this->shippingDimensions;

        switch ($template->price_unit) {
            case 'per_kg':
                $finalPrice = $template->price * $dimensions->weight;
                break;

            case 'per_cubic_meter':
                $volume = ($dimensions->length / 100) * ($dimensions->width / 100) * ($dimensions->height / 100);
                $finalPrice = $template->price * $volume;
                break;

            case 'per_item':
            default:
                $finalPrice = $template->price;
        }
    }

    return round($finalPrice, 2);
}

}
