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
        Schema::create('shipping_template_location', function (Blueprint $table) {
    $table->id();

    $table->foreignId('shipping_template_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('location_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->unique(
    ['shipping_template_id', 'location_id'],
    'stl_template_location_unique'
);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_template_location');
    }
};
