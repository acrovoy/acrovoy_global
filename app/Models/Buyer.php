<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Domain\Contact\Traits\HasContacts;

use App\Domain\Media\Models\Media;
use App\Models\BuyerProfile;

use App\Domain\Contact\Models\Contact;

class Buyer extends Model
{
    use HasFactory, HasContacts;


    protected $fillable = [
    'user_id',
    'buyerable_type',
    'buyerable_id',

    'name',
    'slug',

    'email',
    'phone',
    'website',

    'short_description',
    'description',

    'address',
    'country_id',

    'logo',
    'banner',

    'is_verified',
    'is_premium',

    'status',
    'reputation',
];


    public function businessTypes()
{
    return $this->morphToMany(
        BusinessType::class,
        'business_typeable'
    );
}

 public function country()
    {
        return $this->belongsTo(Country::class);
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

public function catalogImageMedia()
{
    return $this->media()
        ->where('collection', 'catalog_images')
        ->latest();
}

public function profile()
{
    return $this->hasOne(BuyerProfile::class);
}

public function certificatesMedia()
{
    return $this->media()
        ->where('collection', 'buyer_certificates')
        ->where('media_role', 'certificate');
}


public function members()
{
    return $this->morphMany(
        CompanyUser::class,
        'company'
    );
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

    public function primaryAddress()
{
    return $this->morphOne(
        CompanyAddress::class,
        'addressable'
    )->where('is_primary', true);
}

}
