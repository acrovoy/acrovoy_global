<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Country;


class ShippingTemplate extends Model
{
    use HasFactory;


    protected $fillable = [
        'provider_id',
        'provider_type',
        'warehouse_id',
        'created_by',
        'updated_by',
        'title',
        'description',
        'price',
        'price_unit',
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

}
