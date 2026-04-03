<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    protected $fillable = [
        'product_id',
        'attribute_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function translations()
    {
        return $this->hasMany(
            ProductAttributeValueTranslation::class
        );
    }

    public function getTranslatedValue(string $locale = null): string
{
    $locale = $locale ?? app()->getLocale();

    // Сначала ищем перевод
    $translation = $this->translations->firstWhere('locale', $locale);
    if ($translation) {
        return $translation->value;
    }

    // Если нет перевода, возвращаем первый
    return $this->translations->first()?->value ?? '';
}

public function getTranslatedValueAttribute(): ?string
{
    // Если это multiselect — вытаскиваем все опции и соединяем названия
    if ($this->attribute->type === 'multiselect' || $this->attribute->type === 'select') {
        return $this->options->map(fn($option) => $option->translated_value)->implode(', ');
    }

    // Для text/number/boolean
    return $this->translations->where('locale', app()->getLocale())->first()?->value;
}

public function getOptionValues(string $locale = null): string
{
    $locale = $locale ?? app()->getLocale();

    return $this->options
        ->map(fn($o) => $o->option->translations->firstWhere('locale', $locale)?->value ?? '')
        ->filter()
        ->implode(', ');
}

    public function options()
    {
        return $this->hasMany(ProductAttributeValueOption::class);
    }
}
