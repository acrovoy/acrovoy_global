<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [

        'slug',
        'parent_id',
        'level',
        'path',

        'commission_percent',

        'type',

        'is_leaf',
        'is_selectable',
        'children_count',

        'sort_order',
    ];


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function children()
    {
        return $this->hasMany(
            Category::class,
            'parent_id'
        )->orderBy('sort_order');
    }


    public function parent()
    {
        return $this->belongsTo(
            Category::class,
            'parent_id'
        );
    }


    public function products()
    {
        return $this->hasMany(Product::class);
    }


    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }


    /*
    |--------------------------------------------------------------------------
    | TRANSLATION
    |--------------------------------------------------------------------------
    */

    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations
            ->firstWhere('locale', $locale)
            ?? $this->translations
            ->firstWhere('locale', 'en');
    }


    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getNameAttribute()
    {
        return $this->translation()?->name;
    }


    public function getBreadcrumbAttribute()
    {
        if (!$this->path) {
            return null;
        }

        return collect(
            explode('/', $this->path)
        )->implode(' → ');
    }


    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeSelectable($query)
    {
        return $query->where(
            'is_selectable',
            true
        );
    }


    public function scopeLeaf($query)
    {
        return $query->where(
            'is_leaf',
            true
        );
    }


    public function scopeRoot($query)
    {
        return $query->whereNull(
            'parent_id'
        );
    }


    public function scopeOrdered($query)
    {
        return $query->orderBy(
            'sort_order'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function hasChildren()
    {
        return $this->children_count > 0;
    }


    public function isRoot()
    {
        return is_null(
            $this->parent_id
        );
    }

    public function attributes()
{
    return $this->belongsToMany(
        Attribute::class,
        'category_attributes'
    )
    ->withPivot(['is_required', 'sort_order'])
    ->withTimestamps();
}


public function childrenRecursive()
{
    return $this->children()->with('childrenRecursive.translations');
}

public function types()
{
    return $this->hasMany(CategoryType::class);
}

public function scopeForType($query, $type)
{
    return $query->whereHas(
        'types',
        fn($q) => $q->where('type', $type)
    );
}


public function isRfq()
{
    return $this->types->contains('type', 'rfq');
}

public function isProject()
{
    return $this->types->contains('type', 'project');
}

public function isProduct()
{
    return $this->types->contains('type', 'product');
}

}