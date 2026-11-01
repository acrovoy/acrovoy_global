<?php

namespace App\Domain\Community\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostTranslation extends Model
{
    protected $table = 'post_translations';

    protected $fillable = [
        'post_id',
        'locale',
        'title',
        'slug',
        'excerpt',
        'content',
        'seo_title',
        'seo_description',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}