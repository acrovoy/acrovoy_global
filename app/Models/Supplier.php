<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\Media\Models\Media;
use App\Domain\Contact\Traits\HasContacts;
use App\Domain\Contact\Models\Contact;
use App\Models\BusinessType;
use App\Domain\Collection\Models\ProductCollection;

class Supplier extends Model
{
    use HasFactory, HasContacts;

    protected $fillable = [
        'user_id',

        'supplierable_type',
        'supplierable_id',

        'name',
        'email',
        'slug',
        'is_verified',
        'is_trusted',
        'is_premium',
        'status',
        'phone',
        'address',
        'country_id',
        'short_description',
        'description',
    ];


    protected $with = [
        'country',
        'businessTypes.translation',
        'exportMarkets.translation',
        'factoryPhotos',
        'profile'
    ];

    protected $appends = ['years_on_platform'];

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

    public function supplierReviews()
    {
        return $this->hasMany(SupplierReview::class);
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

    


    
    // Проверка полного профиля
    public function isProfileComplete(): bool
{
    return
        filled($this->name) &&
        $this->logo()->exists() &&
        $this->catalogImageMedia()->exists();
}



    public function reputationLogs()
    {
        return $this->hasMany(\App\Models\SupplierReputationLog::class)->orderByDesc('created_at');
    }

    public function shippingTemplates()
{
    return $this->morphMany(
        ShippingTemplate::class,
        'provider',
        'provider_type',
        'provider_id'
    )->where('is_active', 1);
}

  

    public function businessTypes()
{
    return $this->morphToMany(
        BusinessType::class,
        'business_typeable'
    );
}

    public function exportMarkets()
    {
        return $this->belongsToMany(
            ExportMarket::class,
            'export_market_supplier'
        )->withTimestamps();
    }

    public function getMoqRangeAttribute()
    {
        $moqs = $this->products()
            ->pluck('moq')
            ->filter();

        if ($moqs->isEmpty()) {
            return null;
        }

        return [
            'min' => $moqs->min(),
            'max' => $moqs->max(),
            'avg' => round($moqs->avg())
        ];
    }

    public function getLevelAttribute()
    {
        $score = $this->reputation ?? 0;

        return match (true) {
            $score <= 50 => 'Basic',
            $score <= 120 => 'Silver',
            $score <= 200 => 'Gold',
            default => 'Platinum'
        };
    }

    public function getYearsOnPlatformAttribute(): int
    {
        return now()->diffInYears($this->created_at);
    }

    public function factoryPhotos()
{
    return $this->media()
        ->where('collection', 'factory_photos')
        ->orderByDesc('id');
}

public function media()
{
    return $this->morphMany(Media::class, 'model');
}

public function logo()
{
    return $this->media()
        ->where('collection', 'company_logos')
        ->latest()
        ->first();
}

public function getLogoAttribute()
{
    return $this->media
        ->where('collection', 'company_logos')
        ->first();
}

public function certificatesMedia()
{
    return $this->media()
        ->where('collection', 'supplier_certificates')
        ->where('media_role', 'certificate');
}

public function catalogImageMedia()
{
    return $this->media()
        ->where('collection', 'catalog_images')
        ->latest();
}

public function getCatalogPreviewAttribute()
{
    return $this->catalogImageMedia()->first();
}

public function profile()
{
    return $this->hasOne(SupplierProfile::class);
}


public function manufacturingCapabilities()
{
    return $this->belongsToMany(
        \App\Models\ManufacturingCapability::class,
        'company_manufacturing_capability'
    );
}


public function users()
{
    return $this->morphMany(CompanyUser::class, 'company');
}

public function members()
{
    return $this->morphMany(
        CompanyUser::class,
        'company'
    );
}


public function profileMembers()
{
    return $this->morphMany(
        CompanyUser::class,
        'company'
    )
    ->where('show_in_profile', true);
}

public function contacts()
{
    return $this->morphMany(Contact::class, 'contactable');
}

public function publicContacts()
{
    return $this->morphMany(Contact::class, 'contactable')
        ->where('is_public', true);
}

public function addresses()
{
    return $this->morphMany(
        CompanyAddress::class,
        'addressable'
    );
}

public function primaryAddress()
{
    return $this->morphOne(
        CompanyAddress::class,
        'addressable'
    )->where('is_primary', true);
}



public function collections(): MorphToMany
{
    return $this->morphToMany(
        ProductCollection::class,
        'collectionable'
    )->withPivot('sort_order')
     ->withTimestamps();
}

public function supplierable()
{
    return $this->morphTo();
}

}
