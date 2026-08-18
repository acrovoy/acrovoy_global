<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Unit extends Model
{
    protected $fillable = [
        'code',
        'symbol',
        'unit_group',
        'conversion_factor',
        'conversion_offset',
        'is_base',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'conversion_factor' => 'decimal:12',
        'conversion_offset' => 'decimal:12',
        'is_base' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'name',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function translations(): HasMany
    {
        return $this->hasMany(UnitTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(UnitTranslation::class)
            ->where('locale', app()->getLocale());
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getNameAttribute(): string
{
    return $this->translation?->name
        ?? $this->translations
            ->firstWhere('locale', config('app.fallback_locale'))
            ?->name
        ?? $this->code;
}

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopeGroup($query, string $group)
    {
        return $query->where('unit_group', $group);
    }
}