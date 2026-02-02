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
        Schema::create('project_items', function (Blueprint $table) {
            $table->id();

            // Ссылка на проект
            $table->unsignedBigInteger('project_id');

            // Ссылка на поставщика (может быть null)
            $table->unsignedBigInteger('supplier_id')->nullable();

            // Ссылка на продукт (если выбран из каталога)
            $table->unsignedBigInteger('product_id')->nullable();

            // Название продукта (для кастомных позиций)
            $table->string('product_name')->nullable();

            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('lead_time_days')->default(0);

            $table->timestamps();

            // Внешние ключи
            $table->foreign('project_id')
                  ->references('id')->on('projects')
                  ->onDelete('cascade');

            $table->foreign('supplier_id')
                  ->references('id')->on('suppliers')
                  ->onDelete('set null');

            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_items');
    }
};
