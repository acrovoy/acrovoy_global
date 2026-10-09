<?php

namespace App\Domain\Returns\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReturnPolicy extends Model
{
    protected $table = 'product_return_policies';

    protected $fillable = [
        'product_id',
        'return_policy_id',
        'use_supplier_default',
        'returnable',
        'return_window_days',
        'return_shipping_payer',
        'restocking_fee_enabled',
        'restocking_fee_percent',
        'custom_products_returnable',
        'additional_information',
    ];

    protected $casts = [
        'use_supplier_default' => 'boolean',
        'returnable' => 'boolean',
        'return_window_days' => 'integer',
        'restocking_fee_enabled' => 'boolean',
        'restocking_fee_percent' => 'decimal:2',
        'custom_products_returnable' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Product
    |--------------------------------------------------------------------------
    */

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            Product::class,
            'product_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Return Policy
    |--------------------------------------------------------------------------
    */

    public function returnPolicy(): BelongsTo
    {
        return $this->belongsTo(
            ReturnPolicy::class,
            'return_policy_id'
        );
    }
}