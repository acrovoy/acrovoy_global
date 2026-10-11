<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedSupplier extends Model
{
    protected $table = 'saved_suppliers';

    protected $fillable = [
        'buyer_type',
        'buyer_id',
        'supplier_id',
        'created_by',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
