<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BuyerProfile extends Model
{
    use HasFactory;

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | Company Overview
        |--------------------------------------------------------------------------
        */

        'buyer_id',

        'about_us_description',

        'founded_year',

        'total_employees',


        /*
        |--------------------------------------------------------------------------
        | Purchasing Profile
        |--------------------------------------------------------------------------
        */

        'purchasing_description',

        'annual_purchase_volume',

        'purchase_currency',


        /*
        |--------------------------------------------------------------------------
        | Supplier Requirements
        |--------------------------------------------------------------------------
        */

        'supplier_requirements',

        'required_certifications',


        /*
        |--------------------------------------------------------------------------
        | Commercial Information
        |--------------------------------------------------------------------------
        */

        'annual_revenue',


        /*
        |--------------------------------------------------------------------------
        | Import & Logistics
        |--------------------------------------------------------------------------
        */

        'annual_import_volume',

        'preferred_incoterms',


        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */

        'moq',

    ];


    protected $casts = [

        'founded_year' => 'integer',

        'total_employees' => 'integer',

        'annual_purchase_volume' => 'decimal:2',

        'annual_revenue' => 'decimal:2',

        'annual_import_volume' => 'decimal:2',

    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    
}