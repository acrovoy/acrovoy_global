<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'code',
        
        'is_active',
        'is_priority',
        'is_default',
        'sort_order',
    ];

    
    protected $casts = [
        'is_active' => 'boolean',
        'is_priority' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function regions()
{
    return $this->hasMany(Location::class, 'country_id')->whereNull('parent_id');
}

public function translations()
    {
        return $this->hasMany(CountryTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(CountryTranslation::class)
            ->where('locale', app()->getLocale());
    }

    public function getNameAttribute()
    {
        return $this->translation?->name
            ?? $this->translations()->where('locale', 'en')->first()?->name
            ?? $this->code;
    }

    public function scopeWithCurrentTranslation($query)
{
    return $query->leftJoin('country_translations as ct', function ($join) {
        $join->on('countries.id', '=', 'ct.country_id')
             ->where('ct.locale', app()->getLocale());
    })
    ->select('countries.*', 'ct.name as translated_name');
}

}
