<?php

namespace App\Domain\Shipping\Models;

use App\Models\ShippingTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function translations()
    {
        return $this->hasMany(DeliveryTypeTranslation::class);
    }

    public function shippingTemplates()
    {
        return $this->belongsToMany(
            ShippingTemplate::class,
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
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Translation
    |--------------------------------------------------------------------------
    */

    public function translation(?string $locale = null)
    {
        $locale ??= app()->getLocale();

        return $this->translations
            ->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', 'en')
            ?? $this->translations->first();
    }

    public function getNameAttribute(): string
    {
        return $this->translation()?->name ?? $this->code;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->translation()?->description;
    }
}