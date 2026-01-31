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
        'manufacturer_id',
        'title',
        'description',
        'price',
        'delivery_time',
    ];

    // Связь с пользователем
    public function manufacturer()
    {
        return $this->belongsTo(User::class, 'manufacturer_id');
    }


    public function countries()
{
    return $this->belongsToMany(Country::class, 'shipping_template_country');
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

}
