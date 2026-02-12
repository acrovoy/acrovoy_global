<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'country_id', 
    ];

    // Родительская связь
    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    // Дочерние элементы
    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

public function childrenRecursive()
    {
        return $this->hasMany(Location::class, 'parent_id')->with('childrenRecursive');
    }

    public function cities()
{
    return $this->hasMany(Location::class, 'parent_id');
}

}
