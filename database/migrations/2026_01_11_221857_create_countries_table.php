<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Country;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $countries = [
            ['code' => 'us', 'name' => 'USA'],
            ['code' => 'uk', 'name' => 'United Kingdom'],
            ['code' => 'de', 'name' => 'Germany'],
            ['code' => 'fr', 'name' => 'France'],
            ['code' => 'ua', 'name' => 'Ukraine'],
            ['code' => 'sa', 'name' => 'Saudi Arabia'],
        ];

        foreach ($countries as $data) {
            Country::create($data);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
