<?php

namespace App\Domain\Collection\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCollectionTranslation extends Model
{
    protected $table = 'collection_translations';
    protected $fillable = [
        'collection_id',
        'locale',
        'title',
        'description',
        'seo_title',
        'seo_description',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function collection()
{
    return $this->belongsTo(
        ProductCollection::class,
        'collection_id'
    );
}
}