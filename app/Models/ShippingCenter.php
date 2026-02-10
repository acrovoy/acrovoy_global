<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCenter extends Model
{
    protected $fillable = [
        'origin_country_id',
        'destination_country_id',
        'price',
        'delivery_days',
        'is_active',
        'notes',
        'origin_location_id',
        'destination_location_id',
    ];

    public function originCountry()
{
    return $this->belongsTo(Country::class, 'origin_country_id');
}

public function destinationCountry()
{
    return $this->belongsTo(Country::class, 'destination_country_id');
}

public function originLocation()
{
    return $this->belongsTo(Location::class, 'origin_location_id');
}

public function destinationLocation()
{
    return $this->belongsTo(Location::class, 'destination_location_id');
}

}
