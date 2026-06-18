<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_type',
        'provider_id',
        'name',
        'contact_person',
        'phone',
        'country_id',
        'location_id',
        'address',
        'is_default',
        'created_by',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Кто создал склад
    

    // Страна
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Локация (город/регион)
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Provider (Supplier / LogisticCompany / etc.)
    |--------------------------------------------------------------------------
    */

    public function provider()
    {
        return $this->morphTo(null, 'provider_type', 'provider_id');
    }

     /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->country?->name,
            $this->location?->name,
            $this->address,
        ]);

        return implode(', ', $parts);
    }

    public function getIsDefaultLabelAttribute(): string
    {
        return $this->is_default ? 'Default' : '';
    }
}