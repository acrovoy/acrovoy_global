<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'code',
        'name',
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
}
