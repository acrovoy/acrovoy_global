<?php

namespace App\Domain\Negotiation\Models;

use App\Models\ShippingDimensions;
use App\Models\ShippingTemplate;
use App\Models\Supplier;
use App\Models\User;

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
        'owner_type',
        'owner_id',
        'ordered_at',
        'accepted_at',
        'accepted_by',
        'status',
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
    return $this->hasMany(RfqOfferVersionItem::class, 'offer_version_id');
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

public function getOwnerNameAttribute(): ?string
{
    return match ($this->owner_type) {
        Supplier::class => Supplier::whereKey($this->owner_id)->value('name'),

        User::class => optional(User::find($this->owner_id))
            ?->name
            ? trim(optional(User::find($this->owner_id))->name . ' ' . optional(User::find($this->owner_id))->last_name)
            : null,

        default => null,
    };
}

}