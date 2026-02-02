<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'title',
        'description',
        'category_id',
        'status',
    ];

    /**
     * Покупатель, который создал проект
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Категория проекта
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Позиции проекта
     */
    public function items()
    {
        return $this->hasMany(ProjectItem::class, 'project_id');
    }
}
