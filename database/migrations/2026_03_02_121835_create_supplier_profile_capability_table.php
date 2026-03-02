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
        Schema::create('supplier_profile_manufacturing_capability', function (Blueprint $table) {

            $table->id();

            $table->foreignId('supplier_profile_id');
            $table->foreignId('manufacturing_capability_id');

            $table->timestamps();

            $table->foreign(
                'supplier_profile_id',
                'sp_profile_fk'
            )
                ->references('id')
                ->on('supplier_profiles')
                ->cascadeOnDelete();

            $table->foreign(
                'manufacturing_capability_id',
                'sp_capability_fk'
            )
                ->references('id')
                ->on('manufacturing_capabilities')
                ->cascadeOnDelete();

            $table->unique(
                ['supplier_profile_id', 'manufacturing_capability_id'],
                'sp_capability_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_profile_manufacturing_capability');
    }
};
