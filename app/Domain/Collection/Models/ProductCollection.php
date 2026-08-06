<?php

namespace App\Domain\Collection\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

use App\Models\Product;

use App\Domain\Media\Models\Media;
use App\Domain\Collection\Models\ProductCollectionHighlight;

class ProductCollection extends Model
{
    protected $table = 'collections';

    protected $fillable = [
        'owner_type',
        'owner_id',
        'slug',
        'cover_image',
        'type',
        'visibility',
        'is_featured',
        'sort_order',
        'published_at',
        'subtitle',
        'overview',        
        'ideal_for',
        'procurement_notes',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
       
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


    

    /**
 * Public Collection ID
 */
public function getPublicIdAttribute(): string
{
    return 'CL-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
}

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function translations()
{
    return $this->hasMany(
        ProductCollectionTranslation::class,
        'collection_id'
    );
}

    public function translation()
{
    return $this->hasOne(
        ProductCollectionTranslation::class,
        'collection_id'
    )->where('locale', app()->getLocale());
}

public function getCurrentTranslationAttribute()
{
    return $this->translation
        ?? $this->fallbackTranslation
        ?? $this->translations->first();
}

    public function products()
{
    return $this->morphedByMany(
        Product::class,
        'collectionable',
        'collectionables',
        'collection_id',
        'collectionable_id'
    )
    ->withPivot('sort_order')
    ->withTimestamps();
}

    
    public function media()
{
    return $this->morphMany(Media::class, 'model');
}

public function cover()
{
    return $this->morphOne(Media::class, 'model')
        ->where('media_role', 'cover');
}

public function getCoverImageUrlAttribute(): string
{
    if ($this->relationLoaded('cover') && $this->cover) {
        return asset($this->cover->path);
    }

    if ($this->cover()->exists()) {
        return asset($this->cover()->first()->path);
    }

    $product = $this->products()
        ->with('images')
        ->first();

    if ($product && $product->main_image_url) {
        return asset($product->main_image_url);
    }

    return asset('images/placeholders/collection.jpg');
}

public function getNameAttribute(): ?string
{
    return $this->currentTranslation?->title;
}

public function getShortDescriptionAttribute(): ?string
{
    return $this->currentTranslation?->description;
}

public function getDescriptionAttribute(): ?string
{
    return $this->currentTranslation?->description;
}


public function highlights()
{
    return $this->hasMany(
        ProductCollectionHighlight::class,
        'collection_id',
        'id'
    )->orderBy('sort_order');
}

}