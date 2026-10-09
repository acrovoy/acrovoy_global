<?php

namespace App\Domain\Returns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReturnPolicy extends Model
{
    protected $table = 'return_policies';

    protected $fillable = [
        'name',
        'code',
        'owner_type',
        'owner_id',
        'is_default',
        'is_active',
        'return_window_days',
        'return_shipping_payer',
        'restocking_fee_enabled',
        'restocking_fee_percent',
        'custom_products_returnable',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'restocking_fee_enabled' => 'boolean',
        'custom_products_returnable' => 'boolean',
        'return_window_days' => 'integer',
        'restocking_fee_percent' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Translations
    |--------------------------------------------------------------------------
    */

    public function translations(): HasMany
    {
        return $this->hasMany(ReturnPolicyTranslation::class);
    }

    public function translation(?string $locale = null): ?ReturnPolicyTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations
            ->firstWhere('locale', $locale);
    }

    /*
    |--------------------------------------------------------------------------
    | Reasons
    |--------------------------------------------------------------------------
    */

    public function reasons(): BelongsToMany
    {
        return $this->belongsToMany(
            ReturnPolicyReason::class,
            'return_policy_reason_pivot',
            'return_policy_id',
            'reason_id'
        )->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Resolutions
    |--------------------------------------------------------------------------
    */

    public function resolutions(): BelongsToMany
    {
        return $this->belongsToMany(
            ReturnPolicyResolution::class,
            'return_policy_resolution_pivot',
            'return_policy_id',
            'resolution_id'
        )->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Product Policies
    |--------------------------------------------------------------------------
    */

    public function productPolicies(): HasMany
    {
        return $this->hasMany(ProductReturnPolicy::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isNoReturns(): bool
    {
        return $this->code === 'no_returns';
    }

    public function hasReturnWindow(): bool
    {
        return $this->return_window_days !== null;
    }

    public function hasRestockingFee(): bool
    {
        return $this->restocking_fee_enabled
            && $this->restocking_fee_percent !== null
            && $this->restocking_fee_percent > 0;
    }
}