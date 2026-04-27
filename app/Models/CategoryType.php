<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryType extends Model
{
    protected $fillable = [
        'category_id',
        'type'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}