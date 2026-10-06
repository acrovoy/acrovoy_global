<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\Material\Models\MaterialGroup;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['slug',
    'material_group_id',];

    
public function translations()
{
    return $this->hasMany(MaterialTranslation::class, 'material_id');
}

public function translate($locale = null)
{
    $locale = $locale ?? app()->getLocale();
    return $this->translations()->where('locale', $locale)->first();
}

/**
 * UX helper — returns translated name for current locale.
 * NOT for business logic.
 */
public function getNameAttribute()
{
    $locale = app()->getLocale();

    $translation = $this->translations
        ->firstWhere('locale', $locale);

    return $translation->name
        ?? $this->translations->first()->name
        ?? '';
}

public function materialGroup(): BelongsTo
{
    return $this->belongsTo(
        MaterialGroup::class,
        'material_group_id'
    );
}


public function photo()
{
    return $this->morphOne(
        \App\Domain\Media\Models\Media::class,
        'model'
    )
    ->where('collection', 'material_photos')
    ->where('media_role', 'material_photo')
    ->where('is_main', true);
}

   
}
