<?php

namespace App\Domain\Returns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierReturnPolicySetting extends Model
{
    protected $table = 'supplier_return_policy_settings';

    protected $fillable = [
        'supplier_id',
        'default_return_policy_id',
    ];

    protected $casts = [
        'supplier_id' => 'integer',
        'default_return_policy_id' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The default return policy selected by the supplier.
     *
     * This can be either:
     * - a platform return policy
     * - a supplier's own custom return policy
     */
    public function defaultReturnPolicy(): BelongsTo
    {
        return $this->belongsTo(
            ReturnPolicy::class,
            'default_return_policy_id'
        );
    }
}
