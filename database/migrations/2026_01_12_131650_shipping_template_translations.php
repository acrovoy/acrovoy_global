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
        Schema::create('shipping_template_translations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('shipping_template_id')->constrained('shipping_templates')->onDelete('cascade');
    $table->string('locale', 5);
    $table->string('title');
    $table->text('description')->nullable();
    $table->timestamps();

    $table->unique(['shipping_template_id', 'locale'], 'st_translations_template_locale_unique');
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
