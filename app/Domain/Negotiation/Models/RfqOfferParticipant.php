<?php

namespace App\Domain\Negotiation\Models;

use Illuminate\Database\Eloquent\Model;

class RfqOfferParticipant extends Model
{
    protected $table = 'rfq_offer_participants';

    protected $fillable = [
        'offer_id',
        'user_id',
        'role',
    ];

    public function offer()
    {
        return $this->belongsTo(RfqOffer::class, 'offer_id');
    }
}