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
        Schema::create('business_typeables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_type_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('business_typeable_id');
            $table->string('business_typeable_type');

            $table->index(
                ['business_typeable_type', 'business_typeable_id'],
                'btable_morph_idx'
            );

            $table->unique(
                [
                    'business_type_id',
                    'business_typeable_type',
                    'business_typeable_id',
                ],
                'btable_unique'
            );

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_typeables');
    }
};
