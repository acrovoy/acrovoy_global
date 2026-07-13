<?php

namespace App\Domain\Project\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class ProjectVisibilityCategory extends Model
{
    protected $fillable = [
        'project_id',
        'category_id',
    ];

    /**
     * Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}