<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyAddress extends Model
{
    protected $fillable = [
        'country_id',
        'state',
        'city',
        'postal_code',
        'address_line_1',
        'address_line_2',
        'latitude',
        'longitude',
        'is_primary',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}