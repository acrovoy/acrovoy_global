<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Str;

class ProductVariantGroup extends Model
{
    use HasFactory;

    protected $table = 'product_variant_groups';

    protected $fillable = [
        'name',
        'variant_hash',
        'sort_order',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function products()
    {
        return $this->hasMany(Product::class, 'variant_group_id');
    }

    protected static function booted()
{
    static::creating(function ($model) {

        if (empty($model->variant_hash)) {
            $model->variant_hash = (string) Str::uuid();
        }

    });
}

public function items()
{
    return $this->hasMany(ProductVariantItem::class, 'variant_group_id');
}

}