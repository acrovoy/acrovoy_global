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
        Schema::create('return_policy_reason_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reason_id')
                ->constrained('return_policy_reasons')
                ->cascadeOnDelete();

            $table->foreignId('language_id')
                ->constrained('languages')
                ->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(
                ['reason_id', 'language_id'],
                'return_reason_translation_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_policy_reason_translations');
    }
};
