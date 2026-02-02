<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectItemMedia extends Model
{
    use HasFactory;

    protected $table = 'project_item_media';

    protected $fillable = [
        'project_item_id',
        'image_path',
        'is_main',
    ];

    /**
     * Связь с ProjectItem
     */
    public function projectItem()
    {
        return $this->belongsTo(ProjectItem::class);
    }
}
