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
        Schema::create('user_addresses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->string('first_name');
    $table->string('last_name');

    $table->string('country', 2); // ISO-2 (UA, DE, US)
    $table->string('city');
    $table->string('region')->nullable();
    $table->string('street');
    $table->string('postal_code');

    $table->string('phone')->nullable();

    $table->boolean('is_default')->default(true);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
