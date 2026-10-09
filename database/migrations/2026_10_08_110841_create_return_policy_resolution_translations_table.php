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
        Schema::create('return_policy_resolution_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resolution_id')
                ->constrained('return_policy_resolutions')
                ->cascadeOnDelete();

            $table->foreignId('language_id')
                ->constrained('languages')
                ->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(
                ['resolution_id', 'language_id'],
                'return_resolution_translation_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_policy_resolution_translations');
    }
};
