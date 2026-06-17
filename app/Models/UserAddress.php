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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function regionLocation()
{
    return $this->belongsTo(Location::class, 'region');
}

public function country()
{
    return $this->belongsTo(Country::class, 'country');
}


}
