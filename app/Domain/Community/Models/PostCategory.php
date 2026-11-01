<?php

namespace App\Domain\Community\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PostCategory extends Model
{
    protected $table = 'post_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'post_category_id');
    }

    public function translations(): HasMany
{
    return $this->hasMany(PostCategoryTranslation::class, 'post_category_id');
}

public function translation(string $locale): HasOne
{
    return $this->hasOne(PostCategoryTranslation::class, 'post_category_id')
        ->where('locale', $locale);
}

public function getTranslation(string $locale, string $defaultLocale = 'en'): ?PostCategoryTranslation
{
    return $this->translations->firstWhere('locale', $locale)
        ?? $this->translations->firstWhere('locale', $defaultLocale);
}
}