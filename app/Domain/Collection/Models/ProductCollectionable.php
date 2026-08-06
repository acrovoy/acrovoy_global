<?php

namespace App\Domain\Collection\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProductCollectionable extends Model
{
    protected $table = 'collectionables';

    protected $fillable = [
        'collection_id',
        'collectionable_type',
        'collectionable_id',
        'sort_order',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function collection(): BelongsTo
    {
        return $this->belongsTo(ProductCollection::class);
    }

    public function collectionable(): MorphTo
    {
        return $this->morphTo();
    }
}