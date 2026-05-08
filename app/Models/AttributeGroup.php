<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeGroup extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'owner_type',
        'owner_id',
        'created_by',
    ];

    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }
}