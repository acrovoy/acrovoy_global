<?php

namespace App\Domain\RFQ\Models;

use Illuminate\Database\Eloquent\Model;

class RfqRequirementAttribute extends Model
{
    protected $table = 'rfq_requirement_attributes';

    protected $fillable = [
        'rfq_id',
        'attribute_id',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
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
}