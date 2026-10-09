<?php

namespace App\Domain\Returns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnPolicyReasonTranslation extends Model
{
    protected $table = 'return_policy_reason_translations';

    protected $fillable = [
        'reason_id',
        'locale',
        'name',
        'description',
    ];

    /*
    |--------------------------------------------------------------------------
    | Return Policy Reason
    |--------------------------------------------------------------------------
    */

    public function reason(): BelongsTo
    {
        return $this->belongsTo(
            ReturnPolicyReason::class,
            'reason_id'
        );
    }
}