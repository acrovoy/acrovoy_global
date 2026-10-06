<?php

namespace App\Domain\Material\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Material;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Domain\Media\Models\Media;

class MaterialGroup extends Model
{
    use HasFactory;

    protected $table = 'material_groups';

    protected $fillable = [
        'slug',
        'brand',
        'brand_url',
        'owner_type',
        'owner_id',
        'is_active',
        'is_custom',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function translations(): HasMany
    {
        return $this->hasMany(MaterialGroupTranslation::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function logo(): MorphOne
{
    return $this->morphOne(Media::class, 'model')
        ->where('collection', 'material_group_logos')
        ->where('media_role', 'material_group_logo')
        ->where('is_main', true);
}

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function translation(?string $locale = null): ?MaterialGroupTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations
            ->firstWhere('locale', $locale);
    }

    public function translatedName(?string $locale = null): ?string
    {
        return $this->translation($locale)?->name;
    }

    public function translatedDescription(?string $locale = null): ?string
    {
        return $this->translation($locale)?->description;
    }
}