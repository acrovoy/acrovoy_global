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
        Schema::create('category_types', function (Blueprint $table) {

            $table->id();

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'product',
                'rfq',
                'project'
            ]);

            $table->timestamps();

            $table->unique([
                'category_id',
                'type'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_types');
    }
};
