<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogisticCompany extends Model
{
    use HasFactory;

    protected $table = 'logistic_companies';

    // Поля, которые можно массово заполнять через create() или update()
    protected $fillable = [
        'name',
        'description',
        'email',
        'phone',
        'website',
    ];

    // Если понадобится связь с шаблонами доставки
    public function shippingTemplates()
    {
        return $this->hasMany(ShippingTemplate::class, 'logistic_company_id');
    }
}
