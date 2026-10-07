<?php

namespace App\Domain\Shipping\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryTypeTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_type_id',
        'locale',
        'name',
        'description',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function deliveryType()
    {
        return $this->belongsTo(DeliveryType::class);
    }
}