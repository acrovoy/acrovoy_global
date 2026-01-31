<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpCategoryTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['help_category_id', 'locale', 'name', 'description'];

    public function category()
    {
        return $this->belongsTo(HelpCategory::class);
    }
}
