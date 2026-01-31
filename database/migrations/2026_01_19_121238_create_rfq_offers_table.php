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
        Schema::create('rfq_offers', function (Blueprint $table) {
    $table->id();

    $table->foreignId('rfq_id')
        ->constrained('rfqs')
        ->cascadeOnDelete();

    $table->foreignId('supplier_id')
        ->constrained()
        ->cascadeOnDelete();

    // Предложение
    $table->decimal('price', 12, 2);
    $table->integer('delivery_days')->nullable();
    $table->text('comment')->nullable();

    // Статус предложения
    $table->enum('status', [
        'sent',
        'accepted',
        'rejected'
    ])->default('sent');

    $table->timestamps();

    // Один продавец — одно предложение
    $table->unique(['rfq_id', 'supplier_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfq_offers');
    }
};
