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
        Schema::create('rfq_requirement_attributes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('rfq_id')->index();
            $table->unsignedBigInteger('attribute_id')->index();

            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->foreign('rfq_id')
                ->references('id')
                ->on('rfqs')
                ->cascadeOnDelete();

            $table->foreign('attribute_id')
                ->references('id')
                ->on('attributes')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_requirement_attributes');
    }
};
