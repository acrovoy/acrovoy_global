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
        Schema::create('rfq_custom_attributes', function (Blueprint $table) {

            $table->id();

            $table->foreignId('rfq_id')
                ->constrained('rfqs')
                ->cascadeOnDelete();

            $table->string('key');
            $table->text('value')->nullable();

            // optional future extension (очень полезно потом)
            $table->string('type')->nullable();

            $table->timestamps();

            // performance
            $table->index(['rfq_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_custom_attributes');
    }
};
