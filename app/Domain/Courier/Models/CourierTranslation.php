<?php

namespace App\Domain\Courier\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierTranslation extends Model
{
    protected $table = 'courier_translations';

    protected $fillable = [
        'courier_id',
        'locale',
        'name',
        'description',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class);
    }
}