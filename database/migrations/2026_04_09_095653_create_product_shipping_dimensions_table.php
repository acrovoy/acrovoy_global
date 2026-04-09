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
        Schema::create('product_shipping_dimensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');
            $table->decimal('length', 8, 2)->comment('Длина упаковки в см');
            $table->decimal('width', 8, 2)->comment('Ширина упаковки в см');
            $table->decimal('height', 8, 2)->comment('Высота упаковки в см');
            $table->decimal('weight', 8, 3)->comment('Вес упаковки в кг');
            $table->string('package_type', 50)->default('box')->comment('Тип упаковки: box, pallet, set');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_shipping_dimensions');
    }
};
