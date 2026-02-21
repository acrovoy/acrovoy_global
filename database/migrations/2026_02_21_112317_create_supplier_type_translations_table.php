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
        Schema::create('supplier_type_translations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('supplier_type_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->string('locale'); // en, ru, de
    $table->string('name');   // Manufacturer / Производитель
    $table->timestamps();

    $table->unique(['supplier_type_id', 'locale']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_type_translations');
    }
};
