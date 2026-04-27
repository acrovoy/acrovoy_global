<?php

namespace App\Domain\RFQ\Models;

use Illuminate\Database\Eloquent\Model;

class RfqCustomAttribute extends Model
{
    protected $table = 'rfq_custom_attributes';

    protected $fillable = [
        'rfq_id',
        'key',
        'value',
        'type',
    ];

    /*
    |----------------------------------------
    | RELATION TO RFQ
    |----------------------------------------
    */

    public function rfq()
    {
        return $this->belongsTo(Rfq::class);
    }

    /*
    |----------------------------------------
    | OPTIONAL: CAST VALUE HELPERS
    |----------------------------------------
    */

    public function getCastedValueAttribute()
    {
        return match ($this->type) {

            'number' => is_numeric($this->value)
                ? (float) $this->value
                : null,

            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),

            'json' => json_decode($this->value, true),

            default => $this->value,
        };
    }
}