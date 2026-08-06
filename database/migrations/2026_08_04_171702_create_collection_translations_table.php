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
        Schema::create('collection_translations', function (Blueprint $table) {

            $table->id();

            $table->foreignId('collection_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('locale', 5);

            $table->string('title');

            $table->text('description')->nullable();

            $table->string('seo_title')->nullable();

            $table->text('seo_description')->nullable();

            $table->timestamps();

            $table->unique([
                'collection_id',
                'locale'
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_translations');
    }
};
