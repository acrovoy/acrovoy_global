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
        Schema::create('shipping_template_delivery_types', function (Blueprint $table) {
    $table->id();

    $table->foreignId('shipping_template_id')
        ->constrained('shipping_templates')
        ->cascadeOnDelete();

    $table->foreignId('delivery_type_id')
        ->constrained('delivery_types')
        ->restrictOnDelete();

    $table->decimal('price', 10, 2)->nullable();

    $table->enum('price_unit', [
        'per_item',
        'per_kg',
        'per_cubic_meter',
        'flat',
    ])->default('flat');

    $table->string('delivery_time')->nullable();

    $table->string('incoterm', 10)->nullable();

    $table->boolean('is_active')->default(true);

    $table->unsignedInteger('sort_order')->default(0);

    $table->timestamps();

    $table->unique(
        ['shipping_template_id', 'delivery_type_id'],
        'shipping_template_delivery_type_unique'
    );
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_template_delivery_types');
    }
};
