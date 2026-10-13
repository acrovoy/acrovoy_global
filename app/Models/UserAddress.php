<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'first_name',
        'last_name',
        'country',
        'city',
        'region',
        'street',
        'postal_code',
        'phone',
        'is_default',
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'user_id');
    }

    public function regionLocation()
    {
        return $this->belongsTo(Location::class, 'region');
    }

    public function countryLocation()
    {
        return $this->belongsTo(Location::class, 'country');
    }
}