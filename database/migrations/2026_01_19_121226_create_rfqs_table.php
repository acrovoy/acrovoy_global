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
        Schema::create('rfqs', function (Blueprint $table) {
    $table->id();

    // Кто создал RFQ
    $table->foreignId('buyer_id')
        ->constrained('users')
        ->cascadeOnDelete();

    // Основные данные
    $table->string('title');
    $table->text('description');

    // Категория (если есть)
    $table->foreignId('category_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();

    // Количество
    $table->integer('quantity')->nullable();

    // До какого момента принимаются предложения
    $table->dateTime('deadline')->nullable();

    // Статус RFQ
    $table->enum('status', [
        'active',
        'closed',
        'cancelled'
    ])->default('active');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfqs');
    }
};
