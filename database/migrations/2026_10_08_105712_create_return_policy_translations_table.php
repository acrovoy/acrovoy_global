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
        Schema::create('return_policy_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('return_policy_id')
                ->constrained('return_policies')
                ->cascadeOnDelete();

            $table->foreignId('language_id')
                ->constrained('languages')
                ->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(
                ['return_policy_id', 'language_id'],
                'return_policy_translation_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_policy_translations');
    }
};
