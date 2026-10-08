<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_term_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_term_id')
                ->constrained('payment_terms')
                ->cascadeOnDelete();

            $table->string('locale', 10);

            $table->string('name');
            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(
                ['payment_term_id', 'locale'],
                'payment_term_translations_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_term_translations');
    }
};
