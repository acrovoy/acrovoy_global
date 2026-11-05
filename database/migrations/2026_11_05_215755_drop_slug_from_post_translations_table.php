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
        Schema::table('post_translations', function (Blueprint $table) {
            $table->dropUnique('post_translations_locale_slug_unique');
            $table->dropColumn('slug');
        });
    }

    public function down(): void
    {
        Schema::table('post_translations', function (Blueprint $table) {
            $table->string('slug')->nullable();
            $table->unique(['locale', 'slug'], 'post_translations_locale_slug_unique');
        });
    }
};
