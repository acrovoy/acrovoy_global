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
        Schema::create('page_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('page_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('locale', 5);

            $table->string('title');

            $table->text('excerpt')->nullable();

            $table->longText('content')->nullable();

            $table->string('seo_title')->nullable();

            $table->text('seo_description')->nullable();

            $table->text('seo_keywords')->nullable();

            $table->timestamps();

            $table->unique(['page_id', 'locale']);
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_translations');
    }
};
