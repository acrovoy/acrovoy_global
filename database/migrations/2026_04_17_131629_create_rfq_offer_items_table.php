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
        Schema::create('rfq_offer_items', function (Blueprint $table) {
    $table->id();

    $table->foreignId('offer_id')
        ->constrained('rfq_offers')
        ->cascadeOnDelete();

    $table->string('name');
    $table->text('description')->nullable();

    $table->decimal('price', 15, 2);
    $table->integer('quantity')->default(1);

    $table->timestamps();

    $table->index(['offer_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfq_offer_items');
    }
};
