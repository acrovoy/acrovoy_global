<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [
        'code',
        'type',
        'unit',
        'is_required',
        'is_filterable',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(AttributeTranslation::class);
    }

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'category_attributes'
        )
        ->withPivot(['is_required', 'sort_order'])
        ->withTimestamps();
    }

    public function options()
    {
        return $this->hasMany(AttributeOption::class);
    }

    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function getNameAttribute()
{
    return $this->translations
        ->where('locale', app()->getLocale())
        ->first()?->name;
}




    // Метод для получения перевода по текущей локали (или fallback на 'en')
    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations
            ->firstWhere('locale', $locale)
            ?? $this->translations
                ->firstWhere('locale', 'en');
    }


}
