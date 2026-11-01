<?php

namespace App\Domain\Community\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostComment extends Model
{
    use SoftDeletes;

    protected $table = 'post_comments';

    public const STATUS_PUBLISHED = 'published';
    public const STATUS_HIDDEN = 'hidden';

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
        'status',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }
}