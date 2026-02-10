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
        Schema::create('shipping_centers', function (Blueprint $table) {
    $table->id();
    $table->string('origin_country');        // Страна погрузки
    $table->string('destination_country');   // Страна выгрузки
    $table->decimal('price', 10, 2);         // Стоимость доставки
    $table->integer('delivery_days')->default(0); // Срок доставки в днях
    $table->boolean('is_active')->default(true);  // Активен/неактивен
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_centers');
    }
};
