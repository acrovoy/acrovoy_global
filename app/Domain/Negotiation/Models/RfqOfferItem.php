<?php

namespace App\Domain\Negotiation\Models;

use Illuminate\Database\Eloquent\Model;

class RfqOfferItem extends Model
{
    protected $table = 'rfq_offer_items';

    protected $fillable = [
        'offer_id',
        'name',
        'description',
        'price',
        'quantity',
    ];

    public function offer()
    {
        return $this->belongsTo(RfqOffer::class, 'offer_id');
    }

    public function versionSnapshots()
{
    return $this->hasMany(
        RfqOfferVersionItem::class,
        'offer_item_id'
    );
}

}
