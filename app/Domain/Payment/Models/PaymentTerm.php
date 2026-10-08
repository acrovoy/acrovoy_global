<?php

namespace App\Domain\Payment\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentTerm extends Model
{
    protected $table = 'payment_terms';

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

    public function translations(): HasMany
    {
        return $this->hasMany(PaymentTermTranslation::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_payment_terms'
        )
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('product_payment_terms.sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Translation
    |--------------------------------------------------------------------------
    */

    public function translation(?string $locale = null): ?PaymentTermTranslation
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