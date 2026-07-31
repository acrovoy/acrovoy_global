<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessTypeTranslation extends Model
{
    protected $fillable = [
        'business_type_id',
        'locale',
        'name',
    ];

    public function businessType()
    {
        return $this->belongsTo(BusinessType::class);
    }
}