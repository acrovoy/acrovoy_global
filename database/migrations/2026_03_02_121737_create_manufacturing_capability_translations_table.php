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
        Schema::create('manufacturing_capability_translations', function (Blueprint $table) {

    $table->id();

    $table->unsignedBigInteger('manufacturing_capability_id');

    $table->string('locale', 5);

    $table->string('name');

    $table->timestamps();

    $table->foreign('manufacturing_capability_id', 'mc_trans_capability_fk')
        ->references('id')
        ->on('manufacturing_capabilities')
        ->onDelete('cascade');

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_capability_translations');
    }
};
