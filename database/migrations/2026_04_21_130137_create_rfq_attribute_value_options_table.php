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
        Schema::create('rfq_attribute_value_options', function (Blueprint $table) {

            $table->id();

            $table->foreignId('rfq_attribute_value_id')
                ->constrained('rfq_attribute_values')
                ->cascadeOnDelete();

            $table->foreignId('attribute_option_id')
                ->constrained()
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | SHORT UNIQUE INDEX NAME (MySQL-safe)
            |--------------------------------------------------------------------------
            */

            $table->unique(
                ['rfq_attribute_value_id', 'attribute_option_id'],
                'rfq_attr_val_opt_unique'
            );


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_attribute_value_options');
    }
};
