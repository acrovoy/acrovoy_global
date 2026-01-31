<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpArticleTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['help_article_id', 'locale', 'title', 'content'];

    public function article()
    {
        return $this->belongsTo(HelpArticle::class);
    }
}
