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
        Schema::create('premium_seller_plans', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('price');
    $table->boolean('popular')->default(false);
    $table->text('features'); // заменили json на text
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_seller_plans');
    }
};
