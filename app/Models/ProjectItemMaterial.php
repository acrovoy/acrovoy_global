<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectItemMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_item_id',
        'material_id',
    ];

    public function projectItem()
    {
        return $this->belongsTo(ProjectItem::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
