<?php

namespace App\Domain\Payment\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';

    protected $fillable = [
        'code',
        'is_active',
        'sort_order',
        'icon_svg',
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

    public function translations(): HasMany
    {
        return $this->hasMany(PaymentMethodTranslation::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_payment_methods'
        )
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('product_payment_methods.sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Translation
    |--------------------------------------------------------------------------
    */

    public function translation(?string $locale = null): ?PaymentMethodTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations
            ->firstWhere('locale', $locale);
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