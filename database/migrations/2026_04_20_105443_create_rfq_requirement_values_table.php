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
        Schema::create('rfq_requirement_values', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('rfq_id');
            $table->unsignedBigInteger('attribute_id');

            $table->text('value_text')->nullable();
            $table->decimal('value_number', 18, 6)->nullable();
            $table->boolean('value_boolean')->nullable();

            $table->timestamps();

            // indexes
            $table->index('rfq_id');
            $table->index('attribute_id');

            // uniqueness: one attribute per RFQ
            $table->unique(['rfq_id', 'attribute_id']);

            // foreign keys
            $table->foreign('rfq_id')
                ->references('id')
                ->on('rfqs')
                ->onDelete('cascade');

            $table->foreign('attribute_id')
                ->references('id')
                ->on('attributes')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_requirement_values');
    }
};
