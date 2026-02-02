<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'product_id',
        'product_name',      // текстовое поле для имени продукта/позиции
        'quantity',
        'price',
        'lead_time_days',
    ];

    /**
     * Проект, к которому относится позиция
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Описания позиции (Project Item Descriptions)
     */
    public function descriptions()
    {
        return $this->hasMany(ProjectItemDescription::class);
    }

    /**
     * Спецификации позиции (Project Item Specifications)
     */
    public function specifications()
    {
        return $this->hasMany(ProjectItemSpecification::class);
    }

    /**
     * Материалы позиции (Project Item Materials)
     */
    public function materials()
    {
        return $this->belongsToMany(Material::class, 'project_item_materials');
    }

    /**
     * Цвета и текстуры позиции (Project Item Colors)
     */
    public function colors()
    {
        return $this->hasMany(ProjectItemColor::class);
    }

    protected static function booted()
{
    static::deleting(function ($item) {
        $item->descriptions()->delete();
        $item->specifications()->delete();
        $item->materials()->detach();
        $item->colors()->delete();
    });
}

public function product() {
        return $this->belongsTo(Product::class);
    }

    public function media()
{
    return $this->hasMany(ProjectItemMedia::class);
}


}
