<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Specification;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'undername',
        'slug',
        'description',
        'category_id',
        'moq',
        'lead_time',
        'customization',
        'supplier_id',
        'status',
        'country_id',
        'reject_reason'
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

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    

    public function getImageUrlAttribute()
{
    if ($this->mainImage && $this->mainImage->image_path) {
        return asset('storage/' . $this->mainImage->image_path);
    }

    return asset('images/no-image.png'); // заглушка
}


    public function mainImage()
{
    return $this->hasOne(ProductImage::class)->where('is_main', 1);
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


}
