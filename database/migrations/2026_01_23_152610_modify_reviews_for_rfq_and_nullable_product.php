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
        Schema::table('reviews', function (Blueprint $table) {
            // Разрешаем product_id быть null
            $table->unsignedBigInteger('product_id')->nullable()->change();

            // Добавляем поле type для различения отзывов (product / rfq)
            $table->enum('type', ['product', 'rfq'])->default('product')->after('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Возвращаем product_id как обязательное
            $table->unsignedBigInteger('product_id')->nullable(false)->change();

            // Удаляем поле type
            $table->dropColumn('type');
        });
    }
};
