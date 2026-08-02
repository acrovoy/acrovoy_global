<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buyer_profiles', function (Blueprint $table) {

            $table->id();


            /*
            |--------------------------------------------------------------------------
            | Relation
            |--------------------------------------------------------------------------
            */

            $table->foreignId('buyer_id')
                ->constrained()
                ->cascadeOnDelete();



            /*
            |--------------------------------------------------------------------------
            | Company Overview
            |--------------------------------------------------------------------------
            */

            $table->text('about_us_description')
                ->nullable();


            $table->year('founded_year')
                ->nullable();


            $table->unsignedInteger('total_employees')
                ->nullable();



            /*
            |--------------------------------------------------------------------------
            | Purchasing Profile
            |--------------------------------------------------------------------------
            */


            // Description of what buyer purchases
            $table->text('purchasing_description')
                ->nullable();


            // Annual purchasing volume
            $table->decimal(
                'annual_purchase_volume',
                15,
                2
            )
            ->nullable();


            $table->string(
                'purchase_currency',
                3
            )
            ->default('USD');



            /*
            |--------------------------------------------------------------------------
            | Supplier Requirements
            |--------------------------------------------------------------------------
            */


            $table->text('supplier_requirements')
                ->nullable();


            $table->text('required_certifications')
                ->nullable();



            /*
            |--------------------------------------------------------------------------
            | Commercial Information
            |--------------------------------------------------------------------------
            */


            $table->decimal(
                'annual_revenue',
                15,
                2
            )
            ->nullable();



            /*
            |--------------------------------------------------------------------------
            | Import & Logistics
            |--------------------------------------------------------------------------
            */


            // yearly import volume
            $table->decimal(
                'annual_import_volume',
                15,
                2
            )
            ->nullable();



            // FOB / CIF / EXW etc.
            $table->string('preferred_incoterms')
                ->nullable();



            /*
            |--------------------------------------------------------------------------
            | Order Requirements
            |--------------------------------------------------------------------------
            */


            // Minimum order quantity
            $table->string('moq')
                ->nullable();



            /*
            |--------------------------------------------------------------------------
            | Meta
            |--------------------------------------------------------------------------
            */

            $table->timestamps();


            $table->unique('buyer_id');

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('buyer_profiles');
    }
};
