<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierType extends Model
{
    protected $fillable = ['slug'];

    public function suppliers()
{
    return $this->belongsToMany(Supplier::class);
}

    public function translations()
    {
        return $this->hasMany(SupplierTypeTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(SupplierTypeTranslation::class)
                    ->where('locale', app()->getLocale());
    }
}
