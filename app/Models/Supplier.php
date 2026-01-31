<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'name',
    'email',
    'slug',       
    'is_verified',
    'is_premium',
    'status',
    'phone',
    'address',
    'country_id',
    'logo',
    'catalog_image',
    'short_description',
    'description',
];

    /**
     * Связь с пользователем
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }


    public function reviews()
{
    return $this->hasManyThrough(
        Review::class,    // Модель, которую хотим получить
        Product::class,   // Промежуточная модель
        'supplier_id',    // В Product - внешний ключ на Supplier
        'product_id',     // В Review - внешний ключ на Product
        'id',             // Локальный ключ Supplier
        'id'              // Локальный ключ Product
    );
}

// Получить все споры по заказам своих товаров
public function disputes()
{
    return $this->hasManyThrough(
        OrderDispute::class, 
        OrderItem::class,   // через OrderItem получаем заказ -> споры
        'product_id',       // В OrderItem - внешний ключ на Product
        'order_id',         // В OrderDispute - внешний ключ на Order
        'id',               // В Supplier - локальный ключ
        'order_id'          // В OrderItem - локальный ключ
    );
}

public function certificates()
{
    return $this->hasMany(SupplierCertificate::class);
}


public function getBadgesAttribute(): array
    {
        $badges = [];

        foreach ($this->certificates as $cert) {
            $name = strtoupper($cert->name);

            if (str_contains($name, 'ISO')) $badges[] = 'ISO';
            if (str_contains($name, 'ECO')) $badges[] = 'ECO';
            if (str_contains($name, 'FSC')) $badges[] = 'FSC';
        }

        return array_unique($badges);
    }

    // Проверка полного профиля
    public function isProfileComplete(): bool
    {
        return $this->name && $this->logo && $this->catalog_image;
    }



    public function reputationLogs()
{
    return $this->hasMany(\App\Models\SupplierReputationLog::class)->orderByDesc('created_at');
}

public function shippingTemplates()
{
    return $this->hasMany(\App\Models\ShippingTemplate::class, 'manufacturer_id');
}




}
