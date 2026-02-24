<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationTranslation extends Model
{
    protected $fillable = [
        'location_id',
        'locale',
        'name',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}