<?php

namespace App\Domain\Returns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnPolicyResolution extends Model
{
    protected $table = 'return_policy_resolutions';

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
    | Translations
    |--------------------------------------------------------------------------
    */

    public function translations(): HasMany
    {
        return $this->hasMany(
            ReturnPolicyResolutionTranslation::class,
            'resolution_id'
        );
    }

    public function translation(?string $locale = null): ?ReturnPolicyResolutionTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations
            ->firstWhere('locale', $locale);
    }

    /*
    |--------------------------------------------------------------------------
    | Return Policies
    |--------------------------------------------------------------------------
    */

    public function policies(): BelongsToMany
    {
        return $this->belongsToMany(
            ReturnPolicy::class,
            'return_policy_resolution_pivot',
            'resolution_id',
            'return_policy_id'
        )
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }
}