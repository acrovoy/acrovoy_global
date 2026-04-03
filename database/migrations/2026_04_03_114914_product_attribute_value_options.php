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
        Schema::create('product_attribute_value_options', function (Blueprint $table) {
    $table->id();

    $table->foreignId('product_attribute_value_id')
          ->constrained('product_attribute_values')
          ->cascadeOnDelete()
          ->name('pavo_value_id_fk'); // короткое имя

    $table->foreignId('attribute_option_id')
          ->constrained('attribute_options')
          ->cascadeOnDelete()
          ->name('pavo_option_id_fk'); // короткое имя

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
