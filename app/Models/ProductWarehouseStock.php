<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Product;
use App\Models\Warehouse;

class ProductWarehouseStock extends Model
{
    use HasFactory;

    protected $table = 'product_warehouse_stocks';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'reserved_quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'reserved_quantity' => 'decimal:3',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAvailableQuantityAttribute()
    {
        return (float) $this->quantity - (float) $this->reserved_quantity;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function increaseStock($amount): void
    {
        $this->quantity += $amount;
        $this->save();
    }

    public function decreaseStock($amount): void
    {
        $this->quantity = max(0, $this->quantity - $amount);
        $this->save();
    }

    public function reserveStock($amount): void
    {
        if ($this->available_quantity < $amount) {
            throw new \Exception('Not enough available stock');
        }

        $this->reserved_quantity += $amount;
        $this->save();
    }

    public function releaseStock($amount): void
    {
        $this->reserved_quantity = max(0, $this->reserved_quantity - $amount);
        $this->save();
    }
}