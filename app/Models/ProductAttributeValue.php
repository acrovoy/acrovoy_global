<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    protected $fillable = [
        'product_id',
        'attribute_id',
        'unit_id',
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
    $attribute = $this->attribute;

    if (!$attribute) {
        return '';
    }

    $locale = app()->getLocale();

    /*
    |--------------------------------------------------------------------------
    | BOOLEAN
    |--------------------------------------------------------------------------
    */

    if ($attribute->type === 'boolean') {

        // Получаем значение для текущего языка
        $value = $this->translations
            ->firstWhere('locale', $locale)
            ?->value;

        // Если перевода нет — берем любой доступный
        if ($value === null || $value === '') {
            $value = $this->translations
                ->first()
                ?->value;
        }

        // Нет значения
        if ($value === null || $value === '') {
            return '';
        }

        // Приводим разные варианты true к boolean
        $isTrue = in_array(
            strtolower(trim((string) $value)),
            [
                '1',
                'true',
                'yes',
                'on',
                'y',
            ],
            true
        );

        // FALSE вообще не выводим в карточке товара
        if (!$isTrue) {
            return '';
        }

        return __('product/product_show.yes');
    }


    /*
    |--------------------------------------------------------------------------
    | SELECT / MULTISELECT
    |--------------------------------------------------------------------------
    */

    if (in_array($attribute->type, ['select', 'multiselect'])) {

        $values = $this->options
            ->map(function ($opt) use ($locale) {

                $option = $opt->option;

                if (!$option) {
                    return null;
                }

                $translation = $option->translations
                    ->firstWhere('locale', $locale);

                return $translation?->value
                    ?? $option->translations->first()?->value
                    ?? $option->code
                    ?? null;
            })
            ->filter()
            ->values()
            ->all();

        return implode(', ', $values);
    }


    /*
    |--------------------------------------------------------------------------
    | TEXT / NUMBER / OTHER
    |--------------------------------------------------------------------------
    */

    $value = $this->translations
        ->firstWhere('locale', $locale)
        ?->value;

    // Если текущего языка нет — берем первый доступный перевод
    if ($value === null || $value === '') {
        $value = $this->translations
            ->first()
            ?->value;
    }

    return $value ?? '';
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

public function unit()
{
    return $this->belongsTo(Unit::class);
}

}
