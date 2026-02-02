<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectItemDescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_item_id',
        'type',
        'description',
    ];

    public function projectItem()
    {
        return $this->belongsTo(ProjectItem::class);
    }
}
