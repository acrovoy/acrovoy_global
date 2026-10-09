<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Country;
use App\Domain\Shipping\Models\DeliveryType;
use App\Domain\Shipping\Models\ShippingTemplateDeliveryType;


class ShippingTemplate extends Model
{
    use HasFactory;


    protected $fillable = [
        'provider_id',
        'provider_type',
        'courier_id',
        'warehouse_id',
        'created_by',
        'updated_by',
        'title',
        'description',
        'price',
        'price_unit',
        'delivery_type',
        'delivery_time',
        'is_active',
    ];

    // Связь с пользователем
    

    public function locations()
{
    return $this->belongsToMany(Location::class, 'shipping_template_location')
        ->wherePivot('location_type', 'delivery');
}

public function warehouse()
{
    return $this->belongsTo(Warehouse::class, 'warehouse_id');
}

public function translations()
{
    return $this->hasMany(ShippingTemplateTranslation::class);
}

// Получить заголовок для текущей локали
public function getTitleAttribute()
{
    $locale = app()->getLocale();
    $translation = $this->translations->firstWhere('locale', $locale);
    return $translation->title ?? ($this->translations->first()->title ?? '');
}

// Получить описание для текущей локали
public function getDescriptionAttribute()
{
    $locale = app()->getLocale();
    $translation = $this->translations->firstWhere('locale', $locale);
    return $translation->description ?? ($this->translations->first()->description ?? '');
}

public function products()
{
    return $this->belongsToMany(Product::class, 'product_shipping_template');
}

public function getPriceUnitLabelAttribute()
{
    return match($this->price_unit) {
        'per_item' => 'per item',
        'per_kg' => 'per kg',
        'per_cubic_meter' => 'per m³',
        'flat' => 'flat rate',
    };
}

public function provider()
{
    return $this->morphTo();
}

public function deliveryOptions()
{
    return $this->hasMany(
        ShippingTemplateDeliveryType::class,
        'shipping_template_id'
    )->orderBy('sort_order');
}

public function deliveryTypes()
{
    return $this->belongsToMany(
        DeliveryType::class,
        'shipping_template_delivery_types'
    )
        ->withPivot([
            'price',
            'price_unit',
            'delivery_time',
            'incoterm',
            'is_active',
            'sort_order',
        ])
        ->withTimestamps()
        ->orderByPivot('sort_order');
}


public function courier()
{
    return $this->belongsTo(
        \App\Domain\Courier\Models\Courier::class,
        'courier_id'
    );
}

}
