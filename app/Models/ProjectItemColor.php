<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectItemColor extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_item_id',
        'color',
        'texture',
        'texture_path',
    ];

    public function projectItem()
    {
        return $this->belongsTo(ProjectItem::class);
    }
}
