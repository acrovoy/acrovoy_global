<?php

namespace App\Domain\Returns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnPolicyResolutionTranslation extends Model
{
    protected $table = 'return_policy_resolution_translations';

    protected $fillable = [
        'resolution_id',
        'locale',
        'name',
        'description',
    ];

    /*
    |--------------------------------------------------------------------------
    | Return Policy Resolution
    |--------------------------------------------------------------------------
    */

    public function resolution(): BelongsTo
    {
        return $this->belongsTo(
            ReturnPolicyResolution::class,
            'resolution_id'
        );
    }
}