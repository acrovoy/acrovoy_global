<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpCategory extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'parent_id'];

    

   public function articles()
{
    return $this->hasMany(HelpArticle::class, 'category', 'slug')
        ->with('translations');
}


    public function translations()
{
    return $this->hasMany(HelpCategoryTranslation::class, 'help_category_id');
}

    // Доступ к переводу по текущей локали
    public function getTranslatedNameAttribute()
    {
        $locale = app()->getLocale();
        $translation = $this->translations->where('locale', $locale)->first();
        return $translation ? $translation->name : null;
    }

    public function getTranslatedDescriptionAttribute()
    {
        $locale = app()->getLocale();
        $translation = $this->translations->where('locale', $locale)->first();
        return $translation ? $translation->description : null;
    }

    public function parent()
{
    return $this->belongsTo(HelpCategory::class, 'parent_id');
}

public function children()
{
    return $this->hasMany(self::class, 'parent_id')
        ->with('children'); 
}


public function hasChildrenRecursive()
{
    return $this->children()->exists() || $this->articles()->exists();
}
   
}
