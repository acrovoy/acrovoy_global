<?php

namespace App\Domain\Negotiation\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\ShippingDimensions;
use App\Models\ShippingTemplate;

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

    public function shippingTemplates()
{
    $participant = $this->participant;

    if (!$participant) {
        return collect();
    }

    if (!method_exists($participant, 'shippingTemplates')) {
        return collect();
    }

    return $participant->shippingTemplates ?? collect();
}

public function computeShippingPrice(ShippingTemplate $template): float
    {
        $finalPrice = $template->price;

        if ($this->shippingDimensions) {
            $dimensions = $this->shippingDimensions;

            switch ($template->price_unit) {
                case 'per_kg':
                    $finalPrice = $template->price * $dimensions->weight;
                    break;

                case 'per_cubic_meter':
                    $volume = ($dimensions->length / 100) * ($dimensions->width / 100) * ($dimensions->height / 100);
                    $finalPrice = $template->price * $volume;
                    break;

                case 'per_item':
                default:
                    $finalPrice = $template->price;
            }
        }

        return round($finalPrice, 2);
    }

    public function shippingDimensions()
{
    return $this->morphOne(
        ShippingDimensions::class,
        'dimensionable'
    );
}

    
}