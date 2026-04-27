<?php

namespace App\Domain\RFQ\Models;

use Illuminate\Database\Eloquent\Model;

class RfqRequirementValue extends Model
{
    protected $table = 'rfq_requirement_values';

    protected $fillable = [
        'rfq_id',
        'attribute_id',
        'value_text',
        'value_number',
        'value_boolean',
    ];

    protected $casts = [
        'value_boolean' => 'boolean',
        'value_number' => 'float',
    ];

    /*
    |-------------------------
    | RELATIONS
    |-------------------------
    */

    public function rfq()
    {
        return $this->belongsTo(Rfq::class);
    }

    public function attribute()
    {
        return $this->belongsTo(\App\Models\Attribute::class);
    }

    /*
    |-------------------------
    | ACCESSOR (clean value)
    |-------------------------
    */

    public function getValueAttribute()
    {
        return $this->value_text
            ?? $this->value_number
            ?? $this->value_boolean;
    }
}