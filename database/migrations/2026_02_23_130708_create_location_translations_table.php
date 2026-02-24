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
        Schema::create('location_translations', function (Blueprint $table) {

            $table->id();

            $table->foreignId('location_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('locale', 10)->index();

            $table->string('name');

            $table->timestamps();

            $table->unique(['location_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_translations');
    }
};
