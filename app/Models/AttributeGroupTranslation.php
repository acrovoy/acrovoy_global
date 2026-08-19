<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeGroupTranslation extends Model
{
    protected $fillable = [
        'attribute_group_id',
        'locale',
        'name',
    ];

    /**
     * Attribute group.
     */
    public function attributeGroup(): BelongsTo
    {
        return $this->belongsTo(
            AttributeGroup::class,
            'attribute_group_id'
        );
    }
}