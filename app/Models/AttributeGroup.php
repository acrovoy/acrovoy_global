<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeGroup extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'owner_type',
        'owner_id',
        'created_by',
    ];

    public function attributes(): HasMany
    {
        return $this->hasMany(
            Attribute::class,
            'group_id'
        );
    }

    public function translations(): HasMany
{
    return $this->hasMany(
        AttributeGroupTranslation::class,
        'attribute_group_id'
    );
}

public function translation(?string $locale = null): ?AttributeGroupTranslation
{
    $locale ??= app()->getLocale();

    return $this->translations
        ->firstWhere('locale', $locale);
}

}