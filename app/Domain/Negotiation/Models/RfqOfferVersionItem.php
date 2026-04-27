<?php

namespace App\Domain\Negotiation\Models;

use Illuminate\Database\Eloquent\Model;

class RfqOfferVersionItem extends Model
{
    protected $table = 'rfq_offer_version_items';

    protected $fillable = [

        'offer_version_id',
        'offer_item_id',

        'requirement_id',

        'unit_price',
        'quantity',
        'currency',

        'lead_time_days',
        'moq',
        'notes',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function version()
    {
        return $this->belongsTo(RfqOfferVersion::class, 'offer_version_id');
    }


    public function offerItem()
    {
        return $this->belongsTo(RfqOfferItem::class, 'offer_item_id');
    }


    public function requirement()
    {
        return $this->belongsTo(
            \App\Domain\RFQ\Models\RfqRequirement::class,
            'requirement_id'
        );
    }
}