<?php

namespace App\Domain\Community\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domain\Media\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Post extends Model
{
    use SoftDeletes;

    protected $table = 'posts';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'user_id',
        'company_type',
        'company_id',
        'post_category_id',
        'status',
        'reject_reason',
        'views_count',
        'likes_count',
        'comments_count',
        'published_at',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PostTranslation::class, 'post_id');
    }

    public function translation(string $locale): HasOne
{
    return $this->hasOne(PostTranslation::class, 'post_id')
        ->where('locale', $locale);
}

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Product::class,
            'post_products',
            'post_id',
            'product_id'
        )->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function productLinks(): HasMany
    {
        return $this->hasMany(PostProduct::class, 'post_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class, 'post_id');
    }

    public function publishedComments(): HasMany
    {
        return $this->comments()->where('status', 'published');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }


    public function media(): MorphMany
{
    return $this->morphMany(Media::class, 'model')
        ->where('collection', 'community_posts')
        ->orderBy('sort_order');
}

public function cover(): MorphOne
{
    return $this->morphOne(Media::class, 'model')
        ->where('collection', 'community_posts')
        ->where('is_main', true);
}

}