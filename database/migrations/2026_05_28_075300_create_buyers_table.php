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
        Schema::create('buyers', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Owner
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Main Info
            |--------------------------------------------------------------------------
            */

            $table->string('name');

            $table->string('slug')->unique();

            $table->string('email')->nullable();

            $table->string('phone')->nullable();

            $table->string('website')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Company Profile
            |--------------------------------------------------------------------------
            */

            $table->text('short_description')->nullable();

            $table->longText('description')->nullable();

            $table->string('address')->nullable();

            $table->foreignId('country_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Media
            |--------------------------------------------------------------------------
            */

            $table->string('logo')->nullable();

            $table->string('banner')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_verified')->default(false);

            $table->boolean('is_premium')->default(false);

            $table->enum('status', [
                'active',
                'pending',
                'blocked',
            ])->default('pending');

            /*
            |--------------------------------------------------------------------------
            | Metrics
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('reputation')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyers');
    }
};
