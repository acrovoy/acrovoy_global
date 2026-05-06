<?php

namespace App\Domain\Negotiation\Models;

use Illuminate\Database\Eloquent\Model;

class RfqOfferVersionItemOption extends Model
{
    protected $table = 'rfq_offer_version_item_options';

    protected $fillable = [
        'offer_version_item_id',
        'option_id',
    ];

    /**
     * 🔗 Связь с item (version item)
     */
    public function item()
    {
        return $this->belongsTo(
            RfqOfferVersionItem::class,
            'offer_version_item_id'
        );
    }

    /**
     * 🔗 Связь с option (attribute option)
     */
    public function option()
    {
        return $this->belongsTo(
            \App\Models\AttributeOption::class,
            'option_id'
        );
    }
}