<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
    protected $fillable = [
        'attribute_id',
        'sort_order',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    

    public function getValueAttribute()
{
    return $this->translations
        ->where('locale', app()->getLocale())
        ->first()?->value;
}

public function translations()
    {
        return $this->hasMany(AttributeOptionTranslation::class, 'attribute_option_id');
    }

    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        $translation = $this->translations
            ->firstWhere('locale', $locale)
            ?? $this->translations
            ->firstWhere('locale', 'en');

        return $translation;
    }

    // Получаем значение локализованного названия
    public function translatedValue($locale = null)
    {
        return $this->translation($locale)?->value ?? '—';
    }

    
}
