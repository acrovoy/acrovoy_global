<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'currency',
        'role',
        'purchase_country',
        'premium_plan_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function premiumPlan()
    {
        return $this->belongsTo(PremiumSellerPlan::class, 'premium_plan_id');
    }


    // Связь с премиум-планом покупателя
    public function buyerPremiumPlan()
    {
        return $this->belongsTo(PremiumSellerPlan::class, 'buyer_premium_plan_id');
    }

    // Проверка активного премиум-плана поставщика
    public function isSupplierPremium()
    {
        return $this->premium_plan_id
            && $this->supplier_premium_start
            && $this->supplier_premium_end
            && now()->between($this->supplier_premium_start, $this->supplier_premium_end);
    }

    // Проверка активного премиум-плана покупателя
    public function isBuyerPremium()
    {
        return $this->buyer_premium_plan_id
            && $this->buyer_premium_start
            && $this->buyer_premium_end
            && now()->between($this->buyer_premium_start, $this->buyer_premium_end);
    }

    public function getFullNameAttribute()
    {
        return trim($this->name . ' ' . $this->last_name);
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class);
    }

    public function addresses()
{
    return $this->hasMany(UserAddress::class);
}

public function defaultAddress()
{
    return $this->hasOne(UserAddress::class)->where('is_default', true);
}


// Отзывы, оставленные пользователем
public function reviews()
{
    return $this->hasMany(Review::class);
}

// Споры, открытые пользователем
public function orderDisputes()
{
    return $this->hasMany(OrderDispute::class);
}

// Заказы пользователя (если ещё нет)
public function orders()
{
    return $this->hasMany(Order::class);
}

}
