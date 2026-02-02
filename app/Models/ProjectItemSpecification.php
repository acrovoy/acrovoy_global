<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectItemSpecification extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_item_id',
        'type',
        'parameter',
        'value',
    ];

    public function projectItem()
    {
        return $this->belongsTo(ProjectItem::class);
    }
}
