<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpArticle extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'category', 'published'];

    public function translations()
    {
        return $this->hasMany(HelpArticleTranslation::class);
    }

    public function categoryObj()
    {
        return $this->belongsTo(HelpCategory::class, 'category', 'slug');
    }

    public function translate($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->firstWhere('locale', $locale);
    }
}
