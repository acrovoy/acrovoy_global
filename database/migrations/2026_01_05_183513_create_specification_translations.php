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
        Schema::create('specification_translations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('specification_id')->constrained()->cascadeOnDelete();
    $table->string('locale', 5);
    $table->string('key');
    $table->string('value');
    $table->timestamps();

    $table->unique(['specification_id', 'locale']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specification_translations');
    }
};
