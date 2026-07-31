<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    protected $fillable = [
        'slug',
        'target_type',
    ];

    /*
    |--------------------------------------------------------------------------
    | Translations
    |--------------------------------------------------------------------------
    */

    public function translations()
    {
        return $this->hasMany(BusinessTypeTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(BusinessTypeTranslation::class)
            ->where('locale', app()->getLocale());
    }

    /*
    |--------------------------------------------------------------------------
    | Polymorphic relations
    |--------------------------------------------------------------------------
    */

    public function suppliers()
    {
        return $this->morphedByMany(
            Supplier::class,
            'business_typeable'
        );
    }

    public function buyers()
    {
        return $this->morphedByMany(
            Buyer::class,
            'business_typeable'
        );
    }

    public function logisticCompanies()
    {
        return $this->morphedByMany(
            LogisticCompany::class,
            'business_typeable'
        );
    }

    public function users()
    {
        return $this->morphedByMany(
            User::class,
            'business_typeable'
        );
    }
}