<?php

namespace App\Domain\Negotiation\Models;

use Illuminate\Database\Eloquent\Model;

class RfqOfferItem extends Model
{
    protected $table = 'rfq_offer_items';

    protected $fillable = [

        'rfq_offer_id',
        'rfq_id',

        'supplier_id',

        'name',
        'description',

        'base_price',
        'currency',
    ];

    /*
    |------------------------------------------------
    | RELATIONS
    |------------------------------------------------
    */

    public function offer()
    {
        return $this->belongsTo(RfqOffer::class, 'rfq_offer_id');
    }

    public function rfq()
    {
        return $this->belongsTo(\App\Domain\RFQ\Models\Rfq::class);
    }

    public function supplier()
    {
        return $this->belongsTo(\App\Models\Supplier::class);
    }

    /*
    |------------------------------------------------
    | VERSION ITEMS (IMPORTANT LINK)
    |------------------------------------------------
    */

    public function versionItems()
    {
        return $this->hasMany(RfqOfferVersionItem::class, 'offer_item_id');
    }
}