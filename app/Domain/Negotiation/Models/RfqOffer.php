<?php

namespace App\Domain\Negotiation\Models;

use Illuminate\Database\Eloquent\Model;

class RfqOffer extends Model
{
    protected $table = 'rfq_offers';

    protected $fillable = [
        'rfq_id',

        // ✅ polymorphic
        'participant_type',
        'participant_id',

        'status',
        'total_amount',
    ];

    /*
    |----------------------------------------------------------
    | RELATIONS
    |----------------------------------------------------------
    */

    public function rfq()
    {
        return $this->belongsTo(\App\Domain\RFQ\Models\Rfq::class);
    }

    /**
     * Polymorphic participant (Supplier / etc)
     */
    public function participant()
    {
        return $this->morphTo();
    }

    /**
     * Offer versions
     */
    public function versions()
    {
        return $this->hasMany(RfqOfferVersion::class, 'rfq_offer_id');
    }

    /**
     * Latest version (очень важно для UI)
     */
    public function latestVersion()
    {
        return $this->hasOne(RfqOfferVersion::class, 'rfq_offer_id')
            ->latestOfMany('version_number');
    }
}