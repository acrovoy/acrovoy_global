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
    if (in_array($this->attribute->type, ['select', 'multiselect'])) {
        return $this->option_values; // accessor
    }

    return $this->translations
        ->firstWhere('locale', app()->getLocale())
        ?->value;
}

public function getOptionValues(): string
{
    $locale = app()->getLocale();

    return $this->options
        ->map(function ($pivot) use ($locale) {

            $option = $pivot->option;

            if (!$option) return null;

            return $option->translations
                ->firstWhere('locale', $locale)
                ?->value
                ?? $option->code
                ?? null;
        })
        ->filter()
        ->implode(', ');
}

    public function options()
    {
        return $this->hasMany(ProductAttributeValueOption::class);
    }


    public function getOptionValuesAttribute(): string
{
    $locale = app()->getLocale();

    return $this->options
        ->map(function ($pivot) use ($locale) {

            $option = $pivot->option;

            if (!$option) return null;

            return $option->translations
                ->firstWhere('locale', $locale)
                ?->value
                ?? $option->code
                ?? null;
        })
        ->filter()
        ->implode(', ');
}


public function getDisplayValueAttribute(): string
{
    if (!in_array($this->attribute->type, ['select', 'multiselect'])) {
        return $this->translations
            ->firstWhere('locale', app()->getLocale())
            ?->value ?? '';
    }

    $values = $this->options->map(function ($opt) {

        $option = $opt->option;

        if (!$option) return null;

        $translation = $option->translations
            ->firstWhere('locale', app()->getLocale());

        return $translation?->value ?? $option->translations->first()?->value;
    })
    ->filter()
    ->values()
    ->all();

    return implode(', ', $values);
}


public function attributeOptions()
{
    return $this->hasManyThrough(
        AttributeOption::class,
        ProductAttributeValueOption::class,
        'product_attribute_value_id',
        'id',
        'id',
        'attribute_option_id'
    );
}


}
