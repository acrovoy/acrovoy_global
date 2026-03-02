<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufacturingCapability extends Model
{
    protected $fillable = [
    'slug',
    'icon',
    'sort_order',
    'visibility_flag'
];

protected $casts = [
        'visibility_flag' => 'boolean',
    ];

    public function scopeVisible($query)
{
    return $query->where('visibility_flag', true);
}

public function scopeOrdered($query)
{
    return $query->orderBy('sort_order')
        ->orderBy('id');
}

public function translations()
    {
        return $this->hasMany(ManufacturingCapabilityTranslation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Translation helpers
    |--------------------------------------------------------------------------
    */

    public function translate($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations
            ->firstWhere('locale', $locale);
    }

    

   

    public function suppliers()
    {
        return $this->belongsToMany(
            SupplierProfile::class,
            'supplier_profile_manufacturing_capability'
        );
    }

    public function getNameAttribute()
    {
        $locale = app()->getLocale();

        return $this->translations
            ->firstWhere('locale', $locale)
            ?->name
            ?? $this->translations->first()?->name
            ?? '';
    }
}
