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
        Schema::create('post_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('post_id')
                ->constrained('posts')
                ->cascadeOnDelete();

            $table->string('locale', 10);

            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            $table->longText('content');

            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();

            $table->timestamps();

            $table->unique(['post_id', 'locale']);
            $table->unique(['locale', 'slug']);
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_translations');
    }
};
