<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_method_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_method_id')
                ->constrained('payment_methods')
                ->cascadeOnDelete();

            $table->string('locale', 10);

            $table->string('name');
            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(
                ['payment_method_id', 'locale'],
                'payment_method_translations_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_method_translations');
    }
};
