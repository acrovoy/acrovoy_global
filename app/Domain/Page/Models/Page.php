<?php

namespace App\Domain\Page\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Domain\Page\Models\PageTranslation;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'template',
        'status',
        'sort_order',
        'created_by',
        'updated_by',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


    public function getPublicIdAttribute(): string
{
    return 'PG-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
}

    public function translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(PageTranslation::class)
            ->where('locale', app()->getLocale());
    }

    
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getTitleAttribute(): ?string
    {
        return $this->translation?->title;
    }

    public function getExcerptAttribute(): ?string
    {
        return $this->translation?->excerpt;
    }

    public function getContentAttribute(): ?string
    {
        return $this->translation?->content;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeOrdered($query)
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}