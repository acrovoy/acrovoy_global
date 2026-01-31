<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipping_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manufacturer_id'); // кто создал шаблон
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // стоимость доставки
            $table->string('delivery_time')->nullable(); // пример: "3-5 дней"
            $table->timestamps();

            $table->foreign('manufacturer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_templates');
    }
};
