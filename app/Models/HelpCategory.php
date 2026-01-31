<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpCategory extends Model
{
    use HasFactory;

    protected $fillable = ['slug'];

    

    public function articles()
    {
        return $this->hasMany(HelpArticle::class, 'category', 'slug');
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
   
}
