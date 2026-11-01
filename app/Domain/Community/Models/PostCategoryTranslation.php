<?php

namespace App\Domain\Community\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostCategoryTranslation extends Model
{
    protected $table = 'post_category_translations';

    protected $fillable = [
        'post_category_id',
        'locale',
        'name',
        'slug',
        'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }
}