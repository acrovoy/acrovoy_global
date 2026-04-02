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
        Schema::create('product_attribute_value_translations', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('product_attribute_value_id');
    $table->string('locale', 5);
    $table->string('value')->nullable();
    $table->timestamps();

    $table->foreign('product_attribute_value_id', 'fk_pav_trans_value_id')
          ->references('id')
          ->on('product_attribute_values')
          ->cascadeOnDelete();

    $table->unique(['product_attribute_value_id', 'locale'], 'uniq_pav_trans_locale');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_value_translations');
    }
};
