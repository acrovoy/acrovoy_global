<?php

namespace App\Domain\Negotiation\Models;

use Illuminate\Database\Eloquent\Model;

class NegotiationEvent extends Model
{
    protected $fillable = [
        'rfq_id',
        'subject_type',
        'subject_id',
        'user_id',
        'type',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function rfq()
    {
        return $this->belongsTo(\App\Domain\RFQ\Models\Rfq::class);
    }
}