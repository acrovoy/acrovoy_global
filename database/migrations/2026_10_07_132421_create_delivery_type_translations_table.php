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
        Schema::create('delivery_type_translations', function (Blueprint $table) {
    $table->id();

    $table->foreignId('delivery_type_id')
        ->constrained('delivery_types')
        ->cascadeOnDelete();

    $table->string('locale', 10);

    $table->string('name');

    $table->text('description')->nullable();

    $table->timestamps();

    $table->unique(
        ['delivery_type_id', 'locale'],
        'delivery_type_translation_unique'
    );
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_type_translations');
    }
};
