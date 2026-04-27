<?php

namespace App\Domain\Negotiation\Models;

use Illuminate\Database\Eloquent\Model;

class RfqOffer extends Model
{
    protected $table = 'rfq_offers';

    protected $fillable = [
        'rfq_id',
        'supplier_id',
        'status',
        'total_amount',
    ];

    public function rfq()
    {
        return $this->belongsTo(\App\Domain\RFQ\Models\Rfq::class);
    }

    public function items()
    {
        return $this->hasMany(RfqOfferItem::class, 'offer_id');
    }

    public function participants()
    {
        return $this->hasMany(RfqOfferParticipant::class, 'offer_id');
    }
}
