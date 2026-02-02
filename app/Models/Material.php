<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['slug'];

    public function products()
{
    return $this->belongsToMany(
        Product::class,
        'product_materials', // имя таблицы
        'material_id',       // FK для этой модели
        'product_id'         // FK для связанной модели
    );
}

public function translations()
{
    return $this->hasMany(MaterialTranslation::class, 'material_id');
}

public function translate($locale = null)
{
    $locale = $locale ?? app()->getLocale();
    return $this->translations()->where('locale', $locale)->first();
}

/**
 * UX helper — returns translated name for current locale.
 * NOT for business logic.
 */
public function getNameAttribute()
{
    $locale = app()->getLocale();

    $translation = $this->translations
        ->firstWhere('locale', $locale);

    return $translation->name
        ?? $this->translations->first()->name
        ?? '';
}
   
}
