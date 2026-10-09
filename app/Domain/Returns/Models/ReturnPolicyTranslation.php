<?php

namespace App\Domain\Returns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnPolicyTranslation extends Model
{
    protected $table = 'return_policy_translations';

    protected $fillable = [
        'return_policy_id',
        'locale',
        'name',
        'description',
    ];

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