<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeOptionTranslation extends Model
{
    protected $fillable = [
        'attribute_option_id',
        'locale',
        'value',
    ];

    public function option()
    {
        return $this->belongsTo(AttributeOption::class);
    }
}
