<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
    'code',
    'name',
    'symbol',
    'is_active',
    'is_default',
    'is_priority',
    'priority',
    'notes',
];

      public function rate()
    {
        return $this->hasOne(
            ExchangeRate::class,
            'currency_code',
            'code'
        );
    }

     protected static function booted()
    {
        static::deleting(function ($currency) {
            // Удаляем связанный курс
            $currency->rate()->delete();
        });
    }
}
