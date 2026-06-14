<?php

namespace App\Domain\RFQ\Models;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\RFQ\Models\Rfq;

class RfqHiddenAttribute extends Model
{
    protected $table = 'rfq_hidden_attributes';

    protected $fillable = [
        'rfq_id',
        'attribute_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}