<?php

namespace App\Domain\Negotiation\Models;

use Illuminate\Database\Eloquent\Model;

class RfqOfferVersion extends Model
{
    protected $table = 'rfq_offer_versions';

    protected $fillable = [

        'rfq_offer_id',

        'version_number',

        'total_price',

        'comment',

        'is_counter',

        'created_by',

        'accepted_at'
    ];


    public function offer()
    {
        return $this->belongsTo(
            RfqOffer::class,
            'rfq_offer_id'
        );
    }


    public function creator()
    {
        return $this->belongsTo(
            \App\Models\User::class,
            'created_by'
        );
    }


    public function isAccepted(): bool
    {
        return !is_null($this->accepted_at);
    }

    public function items()
{
    return $this->hasMany(
        RfqOfferVersionItem::class,
        'offer_version_id'
    );
}


}