<?php

namespace App\Domain\Community\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostProduct extends Model
{
    protected $table = 'post_products';

    public const UPDATED_AT = null;

    protected $fillable = [
        'post_id',
        'product_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}