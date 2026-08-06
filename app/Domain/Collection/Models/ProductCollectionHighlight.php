<?php

namespace App\Domain\Collection\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Domain\Collection\Models\ProductCollection;

class ProductCollectionHighlight extends Model
{

    protected $table = 'collection_highlights';
    protected $fillable = [
        'collection_id',
        'title',
        'sort_order',
    ];

    public function collection()
{
    return $this->belongsTo(
        ProductCollection::class,
        'collection_id',
        'id'
    );
}
}
