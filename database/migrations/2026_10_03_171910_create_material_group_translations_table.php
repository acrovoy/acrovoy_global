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
        Schema::create('material_group_translations', function (Blueprint $table) {
    $table->id();

    $table->foreignId('material_group_id')
        ->constrained('material_groups')
        ->cascadeOnDelete();

    $table->string('locale', 10);
    $table->string('name');
    $table->text('description')->nullable();

    $table->timestamps();

    $table->unique(['material_group_id', 'locale']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_group_translations');
    }
};
