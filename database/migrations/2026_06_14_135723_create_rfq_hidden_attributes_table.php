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
        Schema::create('rfq_hidden_attributes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rfq_id')
                ->constrained('rfqs')
                ->cascadeOnDelete();

            $table->foreignId('attribute_id')
                ->constrained('attributes')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['rfq_id', 'attribute_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_hidden_attributes');
    }
};
