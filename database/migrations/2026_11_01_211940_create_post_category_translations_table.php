<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_category_id')
                ->constrained('post_categories')
                ->cascadeOnDelete();

            $table->string('locale', 10);
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(['post_category_id', 'locale']);
            $table->unique(['locale', 'slug']);
            $table->index('locale');
        });

        // Перенос существующих данных в английский перевод
        DB::table('post_categories')->orderBy('id')->get()->each(function ($category) {
            DB::table('post_category_translations')->insert([
                'post_category_id' => $category->id,
                'locale' => 'en',
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_category_translations');
    }
};
