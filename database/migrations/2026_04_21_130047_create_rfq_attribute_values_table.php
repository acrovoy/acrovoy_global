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
        Schema::create('rfq_attribute_values', function (Blueprint $table) {

            $table->id();

            $table->foreignId('rfq_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('attribute_id')
                ->constrained()
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | VALUE STORAGE (typed columns)
            |--------------------------------------------------------------------------
            */

            $table->text('value_text')->nullable();

            $table->decimal('value_number', 18, 6)->nullable();

            $table->boolean('value_boolean')->nullable();

            $table->date('value_date')->nullable();


            /*
            |--------------------------------------------------------------------------
            | SELECT OPTION (single-select attributes)
            |--------------------------------------------------------------------------
            */

            $table->foreignId('attribute_option_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | INDEXES
            |--------------------------------------------------------------------------
            */

            $table->index(['rfq_id', 'attribute_id']);


            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('rfq_attribute_values');
    }
};
