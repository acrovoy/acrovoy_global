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
        Schema::create('courier_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('courier_id')
                ->constrained('couriers')
                ->cascadeOnDelete();

            $table->string('locale', 5);
            $table->string('name');
            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(
                ['courier_id', 'locale'],
                'courier_translations_courier_locale_unique'
            );

            $table->index(['locale', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_translations');
    }
};
