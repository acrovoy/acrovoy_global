<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportMarket extends Model
{
    protected $fillable = ['slug'];

    public function translations()
    {
        return $this->hasMany(ExportMarketTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(ExportMarketTranslation::class)
            ->where('locale', app()->getLocale());
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'export_market_supplier')
            ->withTimestamps();
    }
}